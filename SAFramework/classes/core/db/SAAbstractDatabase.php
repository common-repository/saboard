<?php
if (! class_exists ( 'SAAbstractDatabase' )) {
	abstract class SAAbstractDatabase {
		public function isTableExists($tableName) {
			$checktable = mysql_query ( "SHOW TABLES LIKE '$tableName'" );
	
			return mysql_num_rows ( $checktable ) > 0;
		}
		
		public function isColumnExists($tableName,$columnName){
			$query = 'SHOW COLUMNS FROM '.$tableName.' LIKE \''.$columnName.'\' ';
			
			return mysql_num_rows(mysql_query($query)) > 0;
		}
		
		public function addColumn($tableName,$columnName,$type,$length){
			if(!$this->isColumnExists($tableName, $columnName)){
				$query = 'ALTER TABLE '.$tableName.' ADD '.$columnName.' '.$type.'('.$length.')';
				mysql_query($query);
			}
		}
	
		public function fileExecQuery($fileName) {
			$file = file_get_contents ( $fileName );
			$sqls = explode ( ";", $file );
	
			foreach ( $sqls as $sql ) {
				if (trim ( $sql ) == "")
					continue;
					
				mysql_query ( $sql );
			}
		}
	
		public function dropTables($tables = array()) {
			if (empty ( $tables )) {
				die ( 'table empty...' );
			}
	
			foreach ( $tables as $table ) {
				$query = 'DROP TABLE ' . $table . ';';
				mysql_query ( $query );
			}
		}
	
		public function cleanTable($tables=array()){
			foreach ( $tables as $table ) {
				$query = 'DELETE TABLE ' . $table . ';';
				mysql_query($query);
			}
		}
	
		public function backupTables($file, $tables = '*', $ddl = false) {
			if ($tables == '*') {
				$tables = array ();
				$result = mysql_query ( 'SHOW TABLES' );
					
				while ( $row = mysql_fetch_row ( $result ) ) {
					$tables [] = $row [0];
				}
			} else {
				$tables = is_array ( $tables ) ? $tables : explode ( ',', $tables );
			}
	
			$return = '';
	
			$resultArray = array ();
	
			$totalrowcnt = 0;
	
			foreach ( $tables as $table ) {
				$result = mysql_query ( 'SELECT * FROM ' . $table );
				$result2 = mysql_query ( 'SHOW COLUMNS FROM ' . $table );
					
				$num_fields = mysql_num_fields ( $result );
					
				if ($ddl) {
					$return .= 'DROP TABLE ' . $table . ';';
					$row2 = mysql_fetch_row ( mysql_query ( 'SHOW CREATE TABLE ' . $table ) );
					$return .= "\n\n" . $row2 [1] . ";\n\n";
				}
					
				$fields = array ();
					
				while ( $row = mysql_fetch_field ( $result ) ) {
					array_push ( $fields, $row->name );
				}
					
				$rowcnt = 0;
					
				for($i = 0; $i < $num_fields; $i ++) {
					while ( $row = mysql_fetch_row ( $result ) ) {
						$return .= 'INSERT INTO ' . $table . ' (';
							
						$rowcnt ++;
						$totalrowcnt ++;
							
						for($k = 1; $k < count ( $fields ); $k ++) {
	
							$return .= $fields [$k];
	
							if ($k < count ( $fields ) - 1) {
								$return .= ',';
							}
						}
							
						$return .= ') VALUES (';
							
						for($j = 1; $j < $num_fields; $j ++) {
							$row [$j] = addslashes ( $row [$j] );
							preg_replace ( '/"\n","\\n"/', '', $row [$j] );
	
							if (isset ( $row [$j] )) {
								$return .= '"' . $row [$j] . '"';
							} else {
								$return .= '""';
							}
							if ($j < ($num_fields - 1)) {
								$return .= ',';
							}
						}
						$return .= ");\n";
					}
				}
					
				$resultArray ['tables'] [$table] ['rowcnt'] = $rowcnt;
					
				$return .= "\n\n\n";
			}
	
			$resultArray ['totalrowcnt'] = $totalrowcnt;
			$resultArray ['file'] = $file;
	
			$handle = fopen ( $file, 'w+' );
			fwrite ( $handle, $return );
			fclose ( $handle );
	
			return $resultArray;
		}
	}	
}