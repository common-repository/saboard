<?php
class SABoardUser {
	var $boardTable;
	var $boardRoles;
	
	var $userId;
	var $userNm;

	var $boardDomain;
	
	public function __construct($boardTable){
		$this->userId = sa_get_current_user_login_id();
		$this->userNm = sa_get_current_user_nm();
		$this->boardTable = $boardTable;
		
		$this->init();
	}
	
	public function setBoardDomain($boardDomain){
		$this->boardDomain = $boardDomain;
	}
	
	private function init(){
		$this->boardRoles = array();
		
		$this->boardRoles['read']  = $this->boardTable->table['board_table_read_role'];
		$this->boardRoles['write'] = $this->boardTable->table['board_table_write_role'];
	}
	
	private function eq_role($role){
		global $wp_roles;
		
		if($this->boardRoles[$role] != 'all'){
			if(!is_user_logged_in()){
				return false;
			}
			
			if($this->boardRoles[$role] == 'login_user' && is_user_logged_in()){
				return true;
			}
		
			foreach(sa_getCurrentUser()->roles as $user_role){
				$a = $wp_roles->get_role($user_role);
				$b = $wp_roles->get_role($this->boardRoles[$role]);
				
				if(is_object($b)){
					foreach($b->capabilities as $key=>$value){
						if(preg_match('/level_/', $key)){
							return $a->capabilities[$key];
						}
					}	
				}
			}
				
			return false;
		}
		
		return true;
	}
	
	public function is_pw_pass($password){
		if(is_super_admin()){
			return true;
		}
		
		$password = sa_convert_str_to_mysql_password($password);
		$password_check = !empty($password) && $password == $this->boardDomain['board_password'];
		
		return $password_check;
	}
	
	public function getReadRole(){
		return $this->eq_role('read');
	}
	
	public function getReadSecretRole(){
		//어드민이 아닌경우 무조건 비밀번호를 확인한다.
		
		if($this->eq_role('read')){
				
		}
	}
	
	public function getWriteRole(){
		return $this->eq_role('write');
	}
	
	public function getDeleteActionRole(){
		return $this->boardDomain['board_user_id'] == $this->userId && !empty($this->boardDomain['board_user_id']);
	}
	
	public function getDeleteRole(){
		//게시물에 저장된 아이디와 로그인한 아이디가 다른경우
		if(!empty($this->boardDomain['board_user_id'])){
			if($this->boardDomain['board_user_id'] != $this->userId){
				return false;
			}
		}

		//게시물이 전체공개일경우
		if($this->boardRoles['write'] == 'all'){
			return true;
		}
		
		//로그인하지않고 게시물이 로그인한 사용자가 입력한경우
		if(!is_user_logged_in() && !empty($this->boardDomain['board_user_id'])){
			return false;
		}
		
		return false;
	}
	
	public function getModifyRole(){
		if(!empty($this->boardDomain['board_user_id'])){
			if($this->boardDomain['board_user_id'] != $this->userId){
				return false;
			}
		}
		
		if($this->boardRoles['write'] == 'all'){
			return true;
		}
		
		return true;
	}
	
	public function is_eq_password(){
		$password = SARequest::getParameter('board_password','');
		$password = sa_convert_str_to_mysql_password($password);
		
		return !empty($password) && $password == $this->boardDomain['board_password'];
	}
	
	public function getRoles(){
		if(is_super_admin()){
			return array(
				'read'   => true,
				'write'  => true,
				'delete' => true,
				'modify' => true,
				'delete_action' =>true,
				'read_secret'=>true
			);
		}
		
		return array(
			'read'   => $this->getReadRole(),
			'write'  => $this->getWriteRole(),
			'delete' => $this->getDeleteRole(),
			'delete_action'=> $this->getDeleteActionRole(),
			'modify' => $this->getModifyRole()
		);
	}
	
	public function getRole($key){
		if(is_super_admin()){
			return true;
		}
		
		$roles = $this->getRoles();
		
		return $roles[$key];
	}	
}