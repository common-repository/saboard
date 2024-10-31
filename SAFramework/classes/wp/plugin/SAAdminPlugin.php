<?php
if(!class_exists('SAAdminPlugin')){
	class SAAdminPlugin {
		private $args;
		private $__file;
		
		public function __construct($args,$__file) {
			$this->__file = $__file;
			$this->args = $args;
			
			$this->args = wp_parse_args ( $this->args, array (
					'page_title' => '',
					'menu_title' => '',
					'capability' => 'administrator',
					'menu_slug' => '',
					'func' => '',
					'icon_url' => '',
					'position' => ''
					
			) );
			
			if (empty ( $this->args->menu_title )) {
				$this->args ['menu_title'] = $this->args ['page_title'];
			}
			
			add_action('admin_init', array($this,'add_menu_page'));
		}
		
		public function add_menu_page() {
			add_menu_page (   $this->args['page_title']
							, $this->args['menu_title']
							, $this->args['capability']
							, $this->args['menu_slug']
							, $this->args['func'] );
		}
	}
}