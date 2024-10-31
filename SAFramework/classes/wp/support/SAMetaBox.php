<?php
/**
 * 
 * @example
 class TestMetaBox extends SAMetaBox{
	public function view($post,$metabox) {}
 }
	
 $testBox = new TestMetaBox('testMetabox',array (
		'title' => 'testMetaBox',
		'id' => 'testMetaBox'
 ), array (
		'age' => 'input',
		'sex' => 'input'
 ) );
*/
if(!class_exists('SAMetaBox')){
	abstract class SAMetaBox {
		protected $args;
		protected $key;
		protected $postMeta;
		protected $fields;
		
		public function __construct($key,$args = array() , $fields=array()){
			$this->args = $args;
			$this->key  = $key;
			$this->fields = $fields;
			
			$this->setup();
		}
		
		public function setup(){
			add_action( 'add_meta_boxes', array(&$this,'add_meta_boxes') );
			add_action( 'save_post' 	, array(&$this,'save_postdata') );
		}
		
		public function add_meta_boxes(){
			$default = array(
					 'id' 				=> '',
					 'title'     		=> '',
					 'callback'  		=> array(&$this,'render'),
					 'post_type' 		=> 'post',
					 'context'      	=> 'side',
					 'priority'   		=> 'default',
					 'callback_args'    => array()
			);
	
			$args = sa_parse_args($this->args,$default);
			$post_type = get_current_screen()->post_type;
	
			add_meta_box( $args['id'],$args['title'],$args['callback'],$post_type,$args['context'],$args['priority'],$args['callback_args'] );
		}
		
		public function getPostMeta($id,$key){
			return esc_attr( get_post_meta( $id, $this->key.$key, true ) );
		}
		
		public function render($post,$metabox){
			wp_nonce_field( plugin_basename( __FILE__ ), $this->key );
			
			$form = new SAFormHtml();
	
			foreach($this->fields as $name=>$tag){
				$value = $this->getPostMeta($post->ID, $name);
				
				switch ($tag){
					case 'input' : 
						echo $form->label($name,$name).$form->input( $name , array('value'=>$value,'style'=>'display:block;margin:5px 0;') );
					break;
					
					case 'textarea' :
						echo $form->label($name,$name).$form->textArea( $name, $value ,array('style'=>'display:block;margin:5px 0;'));
					break;
					
					case 'select' :
					
					break;
				}	
			}
			
			$this->view($post, $metabox);
		}
		
		public abstract function view($post,$metabox);
		
		public function save_postdata( $post_id ) {
			if ( 'page' == sa_get_array_value($_POST,'post_type') ) {
				if ( ! current_user_can( 'edit_page', $post_id ) )
					return;
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) )
					return;
			}
			
			if ( ! isset( $_POST[$this->key] ) || ! wp_verify_nonce( $_POST[$this->key], plugin_basename( __FILE__ ) ) )
				return;
		
			$post_ID = $_POST['post_ID'];
	
			foreach($this->fields as $name => $tag){
				$mydata = sanitize_text_field( $_POST[$name] );
				update_post_meta($post_ID, $this->key.$name , $mydata);
			}
		}
	}
}