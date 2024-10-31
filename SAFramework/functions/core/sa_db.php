<?php
if (! function_exists ( 'sa_get_autoincrementKey' )) {
	function sa_get_autoincrementKey($tablename) {
		$next_increment = 0;
		$qShowStatus = "SHOW TABLE STATUS LIKE '$tablename'";
		$qShowStatusResult = mysql_query ( $qShowStatus ) or die ( "Query failed: " . mysql_error () . "<br/>" . $qShowStatus );
				
		$row = mysql_fetch_assoc ( $qShowStatusResult );
		
		return $row ['Auto_increment'];
	}
}

if (! function_exists ( 'sa_convert_str_to_mysql_password' )) {
	function sa_convert_str_to_mysql_password($str){
		$qShowStatus = 'SELECT PASSWORD("'.$str.'") password';
		$qShowStatusResult = mysql_query ( $qShowStatus ) or die ( "Query failed: " . mysql_error () . "<br/>" . $qShowStatus );
		
		$row = mysql_fetch_assoc ( $qShowStatusResult );
		
		return $row['password'];
	}
}

if (! function_exists ( 'sa_db_table_exists' )) {
	function sa_db_table_exists($tableName){
		$checktable = mysql_query("SHOW TABLES LIKE '$tableName'");
		
		return mysql_num_rows($checktable) > 0;
	}
}

if (! function_exists ( 'sa_db_delete' )) {
	function sa_db_drop($tables = array()){
		foreach ( $tables as $table ) {
			$query = 'DROP TABLE ' . $table . ';';
			mysql_query($query);
		}
	}
}

if (! function_exists ( 'sa_db_delete' )) {
	function sa_db_delete($tables = array()){
		foreach ( $tables as $table ) {
			$query = 'DELETE FROM ' . $table . ';';
			mysql_query($query);
		}
	}
}

if (! function_exists ( 'sa_db_backup' )) {
	function sa_db_backup($file, $tables = '*' , $ddl=false) {
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
		
		$resultArray = array();
		
		$totalrowcnt = 0;
		
		foreach ( $tables as $table ) {
			$result = mysql_query ( 'SELECT * FROM '    . $table );
			$result2 = mysql_query( 'SHOW COLUMNS FROM '. $table );
			
			$num_fields = mysql_num_fields ( $result );
			
			if($ddl){
				$return .= 'DROP TABLE ' . $table . ';';
				$row2 = mysql_fetch_row ( mysql_query ( 'SHOW CREATE TABLE ' . $table ) );
				$return .= "\n\n" . $row2 [1] . ";\n\n";
			}
			
			$fields =array();
			
			while ( $row = mysql_fetch_field ( $result ) ) {
				array_push($fields, $row->name);
			}
			
			$rowcnt = 0;
			
			for($i = 0; $i < $num_fields; $i ++) {
				while ( $row = mysql_fetch_row ( $result ) ) {
					$return .= 'INSERT INTO ' . $table .' ('; 
					
					$rowcnt ++;
					$totalrowcnt++;
					
					for($k=1;$k<count($fields);$k++){
						
						$return .= $fields[$k];
						
						if($k < count($fields)-1){
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
			
			$resultArray['tables'][$table]['rowcnt'] = $rowcnt;
			
			
			$return .= "\n\n\n";
		}
		
		$resultArray['totalrowcnt'] = $totalrowcnt;
		$resultArray['file'] = $file;
		
		$handle = fopen ( $file, 'w+' );
		fwrite ( $handle, $return );
		fclose ( $handle );
		
		return $resultArray;
	}
}

if (! function_exists ( 'sa_db_backup' )) {
	function sa_db_backup_schema($file, $tables = '*') {
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

		$resultArray = array();

		$totalrowcnt = 0;

		foreach ( $tables as $table ) {
			$result = mysql_query ( 'SELECT * FROM '    . $table );
			$result2 = mysql_query( 'SHOW COLUMNS FROM '. $table );
				
			$num_fields = mysql_num_fields ( $result );
				
			$return .= 'DROP TABLE ' . $table . ';';
			$row2 = mysql_fetch_row ( mysql_query ( 'SHOW CREATE TABLE ' . $table ) );
			$return .= "\n\n" . $row2 [1] . ";\n\n";
		}
		
		$handle = fopen ( $file, 'w+' );
		fwrite ( $handle, $return );
		fclose ( $handle );
		
		return $file;
	}
}

if (! function_exists ( 'sa_db_query_sqlfile' )) {
	function sa_db_query_sqlfile($filename){
		$file = file_get_contents($filename);
		$sqls = explode(";", $file);
		
		foreach ($sqls as $sql) {
			if (trim($sql) == "") continue;
		
			mysql_query($sql);
		}
	}
}