<?php
if (! class_exists ( 'SADbHelper' )) {
	class SADbHelper {
		public static function addColumn($table, $columnName, $type) {
			if(self::isExistsColumn($table,$columnName)){
				return false;
			}
			
			$query = 'alter table ' . $table . ' add column ' . $columnName . ' ' . $type;
			mysql_query ( $query );
		}
		
		public static function dropColumn($table, $columnName) {
			if(!self::isExistsColumn($table,$columnName)){
				return false;
			}
			
			$query = 'alter table ' . $table . ' drop column ' . $columnName;
			mysql_query ( $query );
		}
		
		public static function isExistsColumn($table, $columnName) {
			$result = mysql_query("SHOW columns from ".$table." where field='".$columnName."'");
			$r = mysql_num_rows($result);
			
			$exists = $r > 0 ? true : false;
			
			return $exists;
		}
		
		public static function isExistsTable($tableName){
			$checktable = mysql_query("SHOW TABLES LIKE '$tableName'");
		
			return mysql_num_rows($checktable) > 0;
		}
		
		public static function isExistsValue($tableName,$column,$value){
			$query = 'SELECT count(*) FROM '.$tableName . ' WHERE '.$column.'=\''.$value.'\'';
			
			$result = mysql_query($query);
			$row = mysql_fetch_row($result);
			
			return $row[0] > 0;
		}
	}
}