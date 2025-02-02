<?php

/**
	 * Class: FPBatis - F(aux)P(hp)Batis
	 * Credit: Adam Doyle (adamldoyle@gmail.com)
	 * Purpose: To provide a semi-port of iBatis for Java.
	 * 
	 * This class is far from a full-fledged port of iBatis; however, it does
	 * provide support for some of the finer features of iBatis.  Currently
	 * FPBatis only supports MySQL databases (due to me not needing it to use
	 * on other types of databases).  Some of the more intricate features are
	 * not accounted for (but could be in the future).
	 * 
	 * Every effort has been made to keep the XML files in the proper structure
	 * according to the DTD, although many of the features are never used.
	 * 
	 * For a full class description/usage tips, please refer to:
	 * http://code.google.com/p/fpbatis/
	 */
if (! class_exists ( 'fpbatis' )) {
	class fpbatis {
		private $conn; // Database connection
		private $sqlMap; // Filename for the main sqlMap file
		private $xmlDoc; // Loaded sqlMap file
		private $namespaces; // Associate array of namespace files
		private $debug; // Display all SQL statements
		
		/**
		 *
		 * @param $map sqlmappath        	
		 * @param array $connectionInfo
		 *        DB_HOST,DB_USER,DB_PASSWORD,DB_NAME
		 */
		function __construct($map, array $connectionInfo = array()) {
			if (empty ( $connectionInfo )) {
				$connectionInfo = array (
						'DB_HOST' => DB_HOST,
						'DB_USER' => DB_USER,
						'DB_PASSWORD' => DB_PASSWORD,
						'DB_NAME' => DB_NAME 
				);
			}
			
			$this->sqlMap = $map;
			$this->conn = null;
			$this->xmlDoc = new DOMDocument ();
			$this->xmlDoc->load ( $this->sqlMap );
			
			$this->createConnection ( $connectionInfo );
			$this->buildNamespaces ();
			$this->debug = false;
		}
		function createConnection($connectionInfo = array()) {
			if (is_array ( $connectionInfo ) && count ( $connectionInfo ) > 0) {
				$this->conn = @mysql_connect ( $connectionInfo ['DB_HOST'], $connectionInfo ['DB_USER'], $connectionInfo ['DB_PASSWORD'] ) or die ( 'Unable to connect to server.' );
			}
			
			if (isset ( $connectionInfo ['DB_NAME'] )) {
				mysql_select_db ( $connectionInfo ['DB_NAME'], $this->conn ) or die ( 'Unable to connect to database.' );
			}
		}
		
		/**
		 * Parse the main sqlMap for the list of sub-maps (by namespace) and
		 * add the loaded files to the namespaces array for easy access.
		 */
		function buildNamespaces() {
			$sqlMapConfig = $this->xmlDoc->getElementsByTagName ( 'sqlMapConfig' );
			$sqlMapConfig = $sqlMapConfig->item ( 0 );
			
			if (strrpos ( $this->sqlMap, '/' ) !== false)
				$dir = substr ( $this->sqlMap, 0, strrpos ( $this->sqlMap, '/' ) + 1 );
			else
				$dir = '';
			$maps = $sqlMapConfig->getElementsByTagName ( 'sqlMap' );
			foreach ( $maps as $map ) {
				$ext = $dir . $map->getAttribute ( 'resource' );
				$tempDoc = new DomDocument ();
				$tempDoc->load ( $ext );
				$node = $tempDoc->getElementsByTagName ( 'sqlMap' )->item ( 0 );
				$this->namespaces [$node->getAttribute ( 'namespace' )] = $node;
			}
		}
		
		/**
		 * Given a namespace, tag name, and id, it returns the XML node (null
		 * if not found).
		 */
		function findMapElement($namespace, $tagName, $id) {
			$map = $this->namespaces [$namespace];
			
			if ($map != '') {
				
				foreach ( $map->getElementsByTagName ( $tagName ) as $elem ) {
					if ($elem->getAttribute ( 'id' ) == $id) {
						
						return $elem;
					}
				}
			}
			return null;
		}
		function applyDynamicElement($item, $params, $dynamic) {
			$stmt = '';
			
			if ($item->getAttribute ( 'open' ) != null)
				$stmt .= ' ' . $item->getAttribute ( 'open' );
			if (! $dynamic && $item->getAttribute ( 'prepend' ) != null)
				$stmt .= $item->getAttribute ( 'prepend' ) . ' ';
			if ($item->nodeName == 'dynamic')
				$dynamic = true;
			$stmt .= $this->buildUpStatement ( $item, $params, $dynamic );
			if ($item->getAttribute ( 'close' ) != null)
				$stmt .= $item->getAttribute ( 'close' ) . ' ';
			
			return $stmt;
		}
		function buildUpStatement($elm, $params, $dynamic = false) {
			$childTags = array (
					'#text',
					'include',
					'dynamic',
					'iterate',
					'isParameterPresent',
					'isNotParameterPresent',
					'isEmpty',
					'isNotEmpty',
					'isNull',
					'isNotNull',
					'isEqual',
					'isNotEqual',
					'isGreaterThan',
					'isGreaterEqual',
					'isLessThan',
					'isLessEqual',
					'isPropertyAvailable',
					'isNotPropertyAvailable' 
			);
			
			$stmt = '';
			
			foreach ( $elm->childNodes as $item ) {
				switch ($item->nodeName) {
					case '#text' :
						if (preg_replace ( '/\s\s+/', '', $item->nodeValue ) != '')
							$stmt .= preg_replace ( '/\s\s+/', ' ', $item->nodeValue );
						break;
					case 'dynamic' :
						$subStmt = $this->buildUpStatement ( $item, $params, true );
						if (preg_replace ( '/\s\s+/', '', $subStmt ) != '') {
							$stmt .= $this->applyDynamicElement ( $item, $params, $dynamic );
							$dynamic = false;
						}
						break;
					case 'iterate' :
						if (! empty ( $params [$item->getAttribute ( 'property' )] )) {
							$subStmt = '';
							
							$paramList = $params [$item->getAttribute ( 'property' )];
							$size_list = sizeof ( $paramList );
							for($i = 0; $i < $size_list; $i ++) {
								$param = $paramList [$i];
								$params [$item->getAttribute ( 'property' ) . '[]'] = $param;
								$sub = $this->buildUpStatement ( $item, $params, $dynamic );
								$pieces = split ( "#", $item->nodeValue );
								if (sizeof ( $pieces ) > 1) {
									$sub = $pieces [0];
									for($j = 1; $j < sizeof ( $pieces ); $j += 2) {
										$sub .= "'" . $params [$pieces [$j]] . "'" . $pieces [$j + 1];
									}
								}
								if ($item->getAttribute ( 'conjunction' ) != null && $i != 0)
									$subStmt .= $item->getAttribute ( 'conjunction' );
								$subStmt .= $sub;
							}
							
							if ($subStmt != '') {
								if ($item->getAttribute ( 'open' ) != null)
									$subStmt = $item->getAttribute ( 'open' ) . $subStmt;
								if ($item->getAttribute ( 'close' ) != null)
									$subStmt .= $item->getAttribute ( 'close' );
								if ($dynamic)
									$dynamic = false;
								else if ($item->getAttribute ( 'prepend' ) != null)
									$stmt .= $item->getAttribute ( 'prepend' );
								$stmt .= $subStmt;
								if ($item->getAttribute ( 'append' ) != null)
									$stmt .= $item->getAttribute ( 'append' );
								$dynamic = false;
							}
						}
						break;
					case 'isNotEmpty' :
					case 'isParameterPresent' :
					case 'isPropertyAvailable' :
						if (! empty ( $params [$item->getAttribute ( 'property' )] )) {
							$stmt .= $this->applyDynamicElement ( $item, $params, $dynamic );
							$dynamic = false;
						}
						break;
					case 'isEmpty' :
					case 'isNotParameterPresent' :
					case 'isNotPropertyAvailable' :
						if (empty ( $params [$item->getAttribute ( 'property' )] )) {
							$stmt .= $this->applyDynamicElement ( $item, $params, $dynamic );
							$dynamic = false;
						}
						break;
					case 'isNull' :
						if ($params [$item->getAttribute ( 'property' )] === null) {
							$stmt .= $this->applyDynamicElement ( $item, $params, $dynamic );
							$dynamic = false;
						}
						break;
					case 'isNotNull' :
						if ($params [$item->getAttribute ( 'property' )] !== null) {
							$stmt .= $this->applyDynamicElement ( $item, $params, $dynamic );
							$dynamic = false;
						}
						break;
					default :
						
						break;
				}
			}
			return $stmt;
		}
		
		function parse_args($args, $defaults = '') {
			if (is_object ( $args )) {
				$r = get_object_vars ( $args );
			} elseif (is_array ( $args )) {
				$r = & $args;
			} else {
				parse_str ( $args, $array );
				if (get_magic_quotes_gpc ())
					$array = stripslashes_deep ( $array );
				
				$this->parse_args ( $array );
			}
			
			if (is_array ( $defaults ))
				return array_merge ( $defaults, $r );
			
			return $r;
		}
		
		/**
		 * Run the statement given by the id.
		 * Supports array and single variable parameterClasses, as well as
		 * linking sub-statements through the result declaration.
		 */
		function doSelect($id, $params = null, $debug = false) {
			$ids = preg_split ( "/\./", $id );
			
			if ($elm = $this->findMapElement ( $ids [0], 'select', $ids [1] )) {
				$stmt = $elm->nodeValue;
				$class = $elm->getAttribute ( 'parameterClass' );
				
				$stmt = $this->buildUpStatement ( $elm, $params );
				
				$pieces = preg_split ( "/\\$/", $stmt );
				if (sizeof ( $pieces ) > 1) {
					$stmt = $pieces [0];
					
					switch ($class) {
						case '' :
						case 'array' :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$a = $this->get_array_value($params,$pieces[$i]);
								
								$stmt .= "" . $a . "" . $pieces [$i + 1];
							}
							
							break;
						default :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$stmt .= "" . $params . "" . $pieces [$i + 1];
							}
							
							break;
					}
				}
				
				$pieces = preg_split ( "/#/", $stmt );
				
				if (sizeof ( $pieces ) > 1) {
					$stmt = $pieces [0];
					switch ($class) {
						case '' :
						case 'array' :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$a = $this->get_array_value($params,$pieces[$i]);
								
								$stmt .= "'" . $a . "'" . $pieces [$i + 1];
							}
							break;
						default :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$stmt .= "'" . $params . "'" . $pieces [$i + 1];
							}
							break;
					}
				}
				
				$stmt = str_replace ( "\r\n", " ", $stmt );
				
				$resultMap = $elm->getAttribute ( 'resultMap' );
				
				if ($debug || $this->debug)
					echo 'DEBUG: ' . $stmt . '<br/>';
				
				if (empty ( $resultMap )) {
					return $this->customSelect ( $stmt );
				}
				
				if ($resultMap = $this->findMapElement ( $ids [0], 'resultMap', $resultMap )) {
					$result = mysql_query ( $stmt, $this->conn ) or die ( 'There was an error running your SQL statement: ' . $stmt );
					$resultTagsArry [] = $resultMap->getElementsByTagName ( 'result' );
					$num_rows = mysql_numrows ( $result );
					$results = array ();
					
					while ( $resultMap->getAttribute ( 'extends' ) != null ) {
						if ($resultMap = $this->findMapElement ( $ids [0], 'resultMap', $resultMap->getAttribute ( 'extends' ) )) {
							$resultTagsArry [] = $resultMap->getElementsByTagName ( 'result' );
						}
					}
					
					for($i = 0; $i < $num_rows; $i ++) {
						$resultElm = array ();
						
						foreach ( $resultTagsArry as $resultTags ) {
							foreach ( $resultTags as $resultTag ) {
								if ($resultTag->getAttribute ( 'select' ) == null) {
									$resultElm [$resultTag->getAttribute ( 'property' )] = mysql_result ( $result, $i, $resultTag->getAttribute ( 'column' ) );
								} else {
									$columns = array ();
									$column = rtrim ( trim ( $resultTag->getAttribute ( 'column' ), '{' ), '}' );
									if (strpos ( $column, '=' ) === false) {
										$resultElm [$resultTag->getAttribute ( 'property' )] = $this->doSelect ( $resultTag->getAttribute ( 'select' ), mysql_result ( $result, $i, $column ) );
									} else {
										foreach ( split ( ',', $column ) as $piece ) {
											$colPieces = split ( '=', $piece );
											$columns [$colPieces [0]] = mysql_result ( $result, $i, $colPieces [1] );
										}
										$resultElm [$resultTag->getAttribute ( 'property' )] = $this->doSelect ( $resultTag->getAttribute ( 'select' ), $columns );
									}
								}
							}
						}
						
						$results [] = $resultElm;
					}
					
					return $results;
				}
			}
			
			return null;
		}
		
		/**
		 * Run the statement given by the id.
		 * Supports array and single variable parameterClasses, as well as
		 * linking sub-statements through the result declaration.
		 */
		function queryForObject($id, $params = null, $debug = false) {
			$ids = preg_split ( "/\./", $id );
			
			if ($elm = $this->findMapElement ( $ids [0], 'select', $ids [1] )) {
				$stmt = $elm->nodeValue;
				$class = $elm->getAttribute ( 'parameterClass' );
				
				$stmt = $this->buildUpStatement ( $elm, $params );
								
				$pieces = preg_split ( "/\\$/", $stmt );
				if (sizeof ( $pieces ) > 1) {
					$stmt = $pieces [0];
					
					switch ($class) {
						case '' :
						case 'array' :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$a = $this->get_array_value($params,$pieces[$i]);
								
								$stmt .= "" . $a . "" . $pieces [$i + 1];
							}
							
							break;
						default :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$stmt .= "" . $params . "" . $pieces [$i + 1];
							}
							
							break;
					}
				}
				
				$pieces = preg_split ( "/#/", $stmt );
				if (sizeof ( $pieces ) > 1) {
					$stmt = $pieces [0];
					switch ($class) {
						case '' :
						case 'array' :
							
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$a = $this->get_array_value($params,$pieces[$i]);
								
								$stmt .= "'" . $a . "'" . $pieces [$i + 1];
							}
							break;
						default :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$stmt .= "'" . $params . "'" . $pieces [$i + 1];
							}
							break;
					}
				}
				
				$stmt = str_replace ( "\r\n", " ", $stmt );
				
				$resultMap = $elm->getAttribute ( 'resultMap' );
				
				if ($debug || $this->debug)
					echo 'DEBUG: ' . $stmt . '<br/>';
				
				$result = $this->customSelect ( $stmt );
				
				if (count ( $result ) == 1) {
					$pop = array_pop ( $result );
					
					if (count ( $pop ) == 1) {
						return array_pop ( $pop );
					} else {
						return $pop;
					}
				} else {
					return null;
				}
			}
		}
		function doSelectObject($id, $params = null, $debug = false) {
			$array = $this->doSelect ( $id, $params, $debug );
			return is_array ( $array ) && count ( $array ) > 0 ? $array [0] : false;
		}
		
		/**
		 * Perform an insert given an array of variables and an insert id to
		 * use, returns the object back (null if incorrect id).
		 */
		function doInsert($id, $obj, $fromForm = false) {
			$ids = preg_split ( "/\./", $id );
			if ($elm = $this->findMapElement ( $ids [0], 'insert', $ids [1] )) {
				$elm = $elm->cloneNode ( true );
				if ($subStmt = $elm->getElementsByTagName ( 'selectKey' )->item ( 0 )) {
					$elm->removeChild ( $subStmt );
				}
				$stmt = $elm->nodeValue;
				$pieces = preg_split ( "/#/", $stmt );
				
				$stmt = $pieces [0];
				$stmt = $this->buildUpStatement ( $elm, $obj );
				$class = $elm->getAttribute ( 'parameterClass' );
				$params = $obj;
				
				$pieces = preg_split ( "/\\$/", $stmt );
				
				if (sizeof ( $pieces ) > 1) {
					$stmt = $pieces [0];
					switch ($class) {
						case '' :
						case 'array' :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$a = $this->get_array_value($params,$pieces[$i]);
								
								$stmt .= "" . $a . "" . $pieces [$i + 1];
							}
							break;
						default :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$stmt .= "" . $params . "" . $pieces [$i + 1];
							}
							break;
					}
				}
				
				$pieces = preg_split ( "/#/", $stmt );
				
				if (sizeof ( $pieces ) > 1) {
					$stmt = $pieces [0];
					switch ($class) {
						case '' :
						case 'array' :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$a = $this->get_array_value($params,$pieces[$i]);
								
								$stmt .= "'" . $a . "'" . $pieces [$i + 1];
							}
							break;
						default :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$stmt .= "'" . $params . "'" . $pieces [$i + 1];
							}
							break;
					}
				}
				
				if ($this->debug)
					echo 'DEBUG: ' . $stmt . '<br/>';
				mysql_query ( $stmt, $this->conn ) or die ( 'There was an error running your SQL statement: ' . $stmt );
				
				if ($subStmt != null) {
					if ($this->debug)
						echo 'DEBUG: ' . $subStmt->nodeValue . '<br/>';
					$result = mysql_query ( $subStmt->nodeValue, $this->conn ) or die ( 'There was an error running your SQL statement: ' . $subStmt->nodeValue );
					$obj [$subStmt->getAttribute ( 'keyProperty' )] = mysql_result ( $result, 0, 0 );
				}
				return array (
						'result' => $obj,
						'id' => mysql_insert_id () 
				);
			}
			
			return null;
		}
		
		/**
		 * Similar to insert, but for updates.
		 */
		function doUpdate($id, $obj, $fromForm = false) {
			$params = $obj;
			
			$ids = preg_split ( "/\./", $id );
			
			if ($elm = $this->findMapElement ( $ids [0], 'update', $ids [1] )) {
				
				$stmt = $elm->nodeValue;
				$pieces = preg_split ( "/#/", $stmt );
				$stmt = $pieces [0];
				
				$stmt = $this->buildUpStatement ( $elm, $obj );
				$class = $elm->getAttribute ( 'parameterClass' );
				
				$pieces = preg_split ( "/\\$/", $stmt );
				if (sizeof ( $pieces ) > 1) {
					$stmt = $pieces [0];
					switch ($class) {
						case '' :
						case 'array' :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$a = $this->get_array_value($params,$pieces[$i]);
								
								$stmt .= "" . $a . "" . $pieces [$i + 1];
							}
							break;
						default :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$stmt .= "" . $params . "" . $pieces [$i + 1];
							}
							break;
					}
				}
				
				$pieces = preg_split ( "/#/", $stmt );
				if (sizeof ( $pieces ) > 1) {
					$stmt = $pieces [0];
					switch ($class) {
						case '' :
						case 'array' :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$a = $this->get_array_value($params,$pieces[$i]);
								
								$stmt .= "'" . $a . "'" . $pieces [$i + 1];
							}
							break;
						default :
							for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
								$stmt .= "'" . $params . "'" . $pieces [$i + 1];
							}
							break;
					}
				}
				
				if ($this->debug)
					echo 'DEBUG: ' . $stmt . '<br/>';
				
				mysql_query ( $stmt, $this->conn ) or die ( 'There was an error running your SQL statement: ' . $stmt );
				return $obj;
			}
			
			return null;
		}
		
		/**
		 * Similar to insert, but for deletes.
		 * Returns true if successful,
		 * null if id not valid.
		 */
		function doDelete($id, $obj) {
			$ids = preg_split ( "/\./", $id );
			
			if ($elm = $this->findMapElement ( $ids [0], 'delete', $ids [1] )) {
				$stmt = $elm->nodeValue;
				$pieces = preg_split ( "/#/", $stmt );
				$stmt = $pieces [0];
				
				for($i = 1; $i < sizeof ( $pieces ); $i += 2) {
					$a = $this->get_array_value($obj,$pieces[$i]);
					
					$stmt .= "'" . $a . "'" . $pieces [$i + 1];
				}
				
				if ($this->debug)
					echo 'DEBUG: ' . $stmt . '<br/>';
				mysql_query ( $stmt, $this->conn ) or die ( 'There was an error running your SQL statement: ' . $stmt );
				return true;
			}
			
			return null;
		}
		
		/**
		 * Given an array, a primary key and a value to compare against, this
		 * performs either an insert or an update.
		 */
		function doSave($namespace, $obj, $key = 'id', $insertId = 'insert', $updateId = 'update', $newValue = -1) {
			if ($obj [$key] == $newValue || $obj [$key] == '')
				return $this->doInsert ( $namespace . '.' . $insertId, $obj );
			else
				return $this->doUpdate ( $namespace . '.' . $updateId, $obj );
		}
		function doSaveForm($namespace, $key = 'id', $insertId = 'insert', $updateId = 'update', $newValue = -1) {
			if ($this->param ( $key ) == $newValue || $this->param ( $key ) == '')
				return $this->doInsert ( $namespace . '.' . $insertId, array (), true );
			else
				return $this->doUpdate ( $namespace . '.' . $updateId, array (), true );
		}
		function &customQuery($stmt) {
			$result = mysql_query ( $stmt, $this->conn ) or die ( 'There was an error running your SQL statement: ' . $stmt );
			return $result;
		}
		function &customSelect($stmt, $type = MYSQL_ASSOC) {
			$result = & $this->customQuery ( $stmt );
			$results = array ();
			while ( $row = mysql_fetch_array ( $result, $type ) ) {
				$results [] = $row;
			}
			return $results;
		}
		function param($Name) {
			global $HTTP_GET_VARS;
			global $HTTP_POST_VARS;
			
			if (isset ( $HTTP_GET_VARS [$Name] ))
				return ($HTTP_GET_VARS [$Name]);
			
			if (isset ( $HTTP_POST_VARS [$Name] ))
				return ($HTTP_POST_VARS [$Name]);
			
			return ("");
		}
		function getConnection() {
			return $this->conn;
		}
		function getSqlMap() {
			return $this->sqlMap;
		}
		function setDebug($debug) {
			$this->debug = $debug;
		}
		
		function get_array_value($array,$key){
			return array_key_exists($key, $array) ? $array[$key] : null;
		}
	}
}
?>
