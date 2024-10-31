<?php
class SABoardAdminService {
	private static $instance;
	
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new SABoardAdminService();
		}
	
		return self::$instance;
	}
	
	private $fpBatis;
	
	public function __construct() {
		$map = dirname(__FILE__).'/../sqlMapConfig.xml';
		
		$this->fpBatis = new FPBatis ( $map );
	}
	
	public function insertBoardTable($obj){
		return $this->fpBatis->doInsert('saboardadmin.insertBoardTable', $obj);
	}
	
	public function updateBoardTable($obj){
		return $this->fpBatis->doUpdate('saboardadmin.updateBoardTable', $obj);
	}
	
	public function deleteBoardTable($obj){
		return $this->fpBatis->doDelete('saboardadmin.deleteBoardTable', $obj);
	}
	
	public function getBoardTableList($obj){
		return $this->fpBatis->doSelect('saboardadmin.getBoardTableList', $obj);
	}
	
	public function getBoardTableDomain($obj){
		$result = $this->fpBatis->queryForObject('saboardadmin.getBoardTableDomain', $obj);

		if(!empty($result)){
			$result['board_table_show_columns'] = sa_unserialize($result['board_table_show_columns']);
		}
		
		return $result;
	}
	
	public function getBoardGroupByIndex($obj){
		return $this->fpBatis->queryForObject('saboardadmin.getBoardGroupByIndex', $obj);
	}
	
	public function getBoardGroupList($obj=array()){
		return $this->fpBatis->doSelect('saboardadmin.getBoardGroupList', $obj);
	}
	
	public function insertBoardGroup($obj){
		return $this->fpBatis->doInsert('saboardadmin.insertBoardGroup', $obj);
	}
	
	public function updateBoardGroup($obj){
		return $this->fpBatis->doUpdate('saboardadmin.updateBoardGroup', $obj);
	}
	
	public function deleteBoardGroup($obj){
		return $this->fpBatis->doDelete('saboardadmin.deleteBoardGroup', $obj);
	}
	
	public function getSkinList($skin){
		global $saManager;
	
		return sa_file_getDirectoryNames($saManager->getSaPluginPath().'/board_skins/'.$skin,1);
	}
	
	public function getSkins(){
		return array(
			 'board'=> $this->getSkinList('board')
			,'pagination' => $this->getSkinList('pagination')
			,'reply' => $this->getSkinList('reply')
			,'search' => $this->getSkinList('search')
		);
	}
	
	public function getBoardTheme($obj){
		return $this->fpBatis->queryForObject('saboardcommon.getBoardTheme',$obj);
	}
	
	public function getBoard_dropdown_roles( $selected = false ) {
		$p = '';
	
		$a = array('value'=>'all');
	
		if($a['value'] == $selected){
			$a['selected'] = 'selected';
		}
	
		$b = array('value'=>'login_user');
	
		if($b['value'] == $selected){
			$b['selected'] = 'selected';
		}
	
		$p .= SAHtml::content_html('option','전체공개',$a);
		$p .= SAHtml::content_html('option','로그인사용자',$b);
	
		$r = '';
	
		$editable_roles = get_editable_roles();
	
		foreach ( $editable_roles as $role => $details ) {
			$name = translate_user_role($details['name'] );
				
			if ( $selected == $role )
				$p .= "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
			else
				$r .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
		}
	
		echo $p . $r;
	}
}