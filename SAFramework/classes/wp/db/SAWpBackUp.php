<?php
if(!class_exists('SAWpBackUp')){
	class SAWpBackUp extends SAAbstractDatabase{
		var $tables;
		var $backupPath;
		var $backupList;
		
		public function __construct() {
		}
		
		public function backUp($ddl = true) {
			if (empty ( $this->tables )) {
				die ( 'backup table empty...' );
			}
	
			do_action ( 'sa_wp_database_backup_action'  );
			
			if(!is_dir($this->backupPath)){
				mkdir($this->backupPath);
			}
			
			return $this->backupTables ( $this->backupPath.'/db-backup_'.time().'.sql', $this->tables, $ddl );
		}
		
		public function restore($fileName) {
			$this->fileExecQuery ( $this->backupPath.'/'.$fileName );
		}
		
		public function getBackUpFiles() {
			$result = array ();
			
			$fileNames = sa_file_getDirectoryNames ( $this->backupPath );
			
			foreach ( $fileNames as $fileName ) {
				$f = new SAFile ( $this->backupPath,$fileName );
				
				array_push ( $result, $f );
			}
			
			return $result;
		}
		
		public function deleteBackupFile($fileName) {
			$f = new SAFile ( $this->backupPath,$fileName );
			$f->delete ();
			
			do_action ( 'sa_wp_database_delete_action' , $fileName );
		}
		
		public function getTables() {
			return $this->tables;
		}
		
		public function getBackupPath() {
			return $this->backupPath;
		}
		
		public function getBackupList() {
			return $this->backupList;
		}
		
		public function setTables($tables) {
			$this->tables = $tables;
		}
		
		public function setBackupPath($backupPath) {
			$this->backupPath = $backupPath;
		}
		
		public function setBackupList($backupList) {
			$this->backupList = $backupList;
		}
	}
}