<?php
if(!function_exists('sa_register_setting')){
	/**
	 * 
	 * @param string $group
	 * @param array $args
	 * 	
	 * @uses sa_register_setting('sample',array( 'option1'
	 * 											,'option2'));
	 */
	function sa_register_setting($group,$args){
		if(is_array($args)){
			foreach($args as $arg){
				register_setting ( $group, $arg );
			}
		}
	}	
}

if(!function_exists('sa_save_alert')){
	function sa_save_alert($save_message){
		if(!isset($_SESSION)){
			session_start();
		}
		
		$_SESSION['save_message'] = $save_message;
	}
}

if(!function_exists('sa_head_set')){
	function sa_head_set(){
		wp_enqueue_script('jquery');
		
		add_action('wp_enqueue_scripts', 'sa_head_script');
		add_action('admin_enqueue_scripts', 'sa_head_script');
	}
}

if(!function_exists('sa_head_script')){
	function sa_head_script(){
		global $saManager;
		global $post;
		
		$page_id = null;
		
		if(is_object($post)){
			$page_id = get_the_ID();
			$page_id = strip_tags($page_id);
		}
		
		?>
		<script type="text/javascript">
			var SA_GLOBAL = {
				C_URL : "<?= $saManager->getSaDirUrl() ?>",
				HOME_URL : "<?= home_url() ?>",
				AJAX_URL : "<?= admin_url( 'admin-ajax.php' )?>",
				PAGE_ID : "<?= $page_id ?>"
			};
		</script>
		
		<?php 
			if(!empty($_SESSION['save_message'])) { 
				sa_alert($_SESSION['save_message']);
				unset($_SESSION['save_message']);
			}?>
		<?php 
	}
}

if(!function_exists('sa_head_title')){
	/**
	 * 기본 타이틀 설정
	 */
	function sa_head_title(){
		global $page, $paged;
		
		echo '<title>';
		
		wp_title( '|', true, 'right' );
		
		bloginfo( 'name' );
		
		$site_description = get_bloginfo( 'description', 'display' );
		
		if ( $site_description && ( is_home() || is_front_page() ) ){
			echo " | $site_description";
		}
				
		
		if ( $paged >= 2 || $page >= 2 ){
			echo ' | ' . sprintf( __( 'Page %s' ), max( $paged, $page ) );
		}
				
		echo '</title>';
	}
}

if(!function_exists('sa_comment_list')){
	/**
	 * 댓글 출력 
	 * @param callback function
	 * @uses wp_list_comments( array( 'callback' => 'sa_comment_list', 'style' => 'ol' ) );
	 */
	function sa_comment_list($comment, $args, $depth ){
		global $post;
		global $saManager;
		
		$commonView = SACommonView::getInstance();
		
		$defaults = array( 'back_view'   => dirname(__FILE__).'/views/comment/sa_comment_back.php' 
						  ,'default_view'=> dirname(__FILE__).'/views/comment/sa_comment_default.php' );
		
		$args = wp_parse_args ( $args, $defaults );

		$params = array('comment' => $comment,'post' => $post,'args'=>$args,'depth'=>$depth);
		
		switch ( $comment->comment_type ){
			case 'pingback' :
			case 'trackback' :
				$commonView->view($args['back_view'],$params);
			break;
			default :
				$commonView->view($args['default_view'],$params);
			break;
		}
	}
	
	function custom_comment_reply($content) {
		$content = str_replace('comment-reply-link', 'comment-reply-link btn btn-primary', $content);
		
		return $content;
	}
	
	add_filter('comment_reply_link', 'custom_comment_reply');
}

if(!function_exists('sa_get_head_meta')){
	/**
	 * 헤더 매타태그 등록
	 * 
	 * @uses sa_get_head_meta(array('description'=>'test','keywords'=>'test'));
	 * 
	 * @param array $args
	 */
	function sa_get_head_meta(array $args=array()){
		$default = array( 'description'=>'wordpress site'
						 ,'keywords'=>'wordpress'
						 ,'viewport'=>'width=device-width,initial-scale=0.27,minimum-scale=0.27,maximum-scale=1.0'
						 ,'Compatible' => 'IE=edge,chrome=1');
		$args = sa_parse_args($args,$default); ?>
		
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta http-equiv="X-UA-Compatible" 					content="<?php _e($args['Compatible']) ?>" />
		<meta name="format-detection" 						content="telephone=no">
		<meta name="apple-mobile-web-app-status-bar-style" 	content="black" />
		<meta name="apple-mobile-web-app-capable" 			content="yes" />
		<meta name="viewport" 	 							content="<?php _e($args['viewport']) ?>">
		<meta name="description" 							content="<?php _e($args['description']) ?>" />
		<meta name="keywords"    							content="<?php _e($args['keywords']) ?>">
	<?php 
	}
}

if(!function_exists('sa_get_nav_cnt')){
	function sa_get_nav_cnt($theme_location,$option=array()){
		$default = array(
				'echo' => false,
				'theme_location' => $theme_location,
				'depth' => 1
		);

		$option = sa_parse_args($option,$default);
		
		$menu_to_count = wp_nav_menu($option);
		
		$menu_items = substr_count($menu_to_count,'class="menu-item ');
		
		return $menu_items;
	}
}

if(!function_exists('sa_get_smof_option')){
	function sa_get_smof_option($name){
		if(!function_exists('of_get_options')){
			return null;
		}
		
		$smof_option = of_get_options();
		
		return isset($smof_option[$name]) ? $smof_option[$name] : null; 		
	}
}

if(!function_exists('sa_getCurrentUser')){
	/**
	 *  $current_user->user_login
	 *  $current_user->user_email
	 *  $current_user->user_firstname
	 *  $current_user->user_lastname
	 *  $current_user->display_name
	 *  $current_user->ID
	 */
	function sa_getCurrentUser(){
		global $current_user;
		
		get_currentuserinfo();
		
		return $current_user;
	}
}

if(!function_exists('sa_get_current_user_login_id')){
	function sa_get_current_user_login_id(){
		return sa_getCurrentUser()->get('user_login');	
	}
}

if(!function_exists('sa_get_current_user_data')){
	function sa_get_current_user_data(){
		return get_userdata(sa_getCurrentUser()->ID);
	}
}

if(!function_exists('sa_get_current_user_is_administrator')){
	function sa_get_current_user_is_administrator(){
		return sa_get_current_user_data()->roles[0] == 'administrator';
	}
}

if(!function_exists('sa_get_current_user_meta')){
	function sa_get_current_user_meta(){
		return get_user_meta(sa_getCurrentUser()->ID);
	}
}

if(!function_exists('sa_get_current_user_nm')){
	function sa_get_current_user_nm(){
		$meta = sa_get_current_user_meta(); 
		return $meta['nickname'][0];
	}
}

if(!function_exists('sa_get_sidebar')){
	function sa_get_sidebar($options = array() , $sidebar){
		if ( is_active_sidebar( $sidebar ) ) {
			echo SAHtml::html('div',$options , true);  
			
			dynamic_sidebar( $sidebar );
			
			echo SAHtml::_html('div');
		}
	}
}

if(!function_exists('sa_get_image')){
	function sa_get_image($name){
		global $saManager;
		
		return $saManager->getSaDirUrl().'/resources/images/'.$name; 	
	}
}

if(!function_exists('sa_body_class')){
	function sa_body_class( $class = '' ) {
		$c = is_front_page() ? ' main' : ' sub';
		
		$cc = join( ' ', get_body_class( $class ) );
		$cc .= $c;
		
		echo 'class="' . $cc . ' "';
	}
}

if(!function_exists('sa_content_nav')){
	function sa_content_nav( $html_id ) {
		global $wp_query;
	
		$html_id = esc_attr( $html_id );
	
		if ( $wp_query->max_num_pages > 1 ) : ?>
			<nav id="<?php echo $html_id; ?>" class="navigation" role="navigation">
				<div class="nav-previous alignleft"><?php next_posts_link( '<span class="meta-nav">&larr;</span> '.__('Older posts','')); ?></div>
				<div class="nav-next alignright"><?php previous_posts_link( __('Newer posts','').' <span class="meta-nav">&rarr;</span>'); ?></div>
			</nav><!-- #<?php echo $html_id; ?> .navigation -->
		<?php endif;
	}
}

/**
 * 
 * @param array $args
 * 				title,cat,sort_by,asc_sort_order,num,orderby,order
 */
if(!function_exists('sa_get_category')){
	function sa_get_category($args){
		$title 				 = sa_get_array_value($args,'title');
		$cat   				 = sa_get_array_value($args,'cat');
		$sort_by   			 = sa_get_array_value($args,'sort_by');
		$asc_sort_order   	 = sa_get_array_value($args,'asc_sort_order');
		$num   				 = sa_get_array_value($args,'num');
		$orderby   			 = sa_get_array_value($args,'orderby');
		$order   			 = sa_get_array_value($args,'order');
	
		if( !$title ) {
			$category_info = get_category($args["cat"]);
			$title = $category_info->name;
		}
	
		$valid_sort_orders = array('date', 'title', 'comment_count', 'rand');
		if ( in_array($sort_by, $valid_sort_orders) ) {
			$sort_by_ = $sort_by;
			$sort_order = (bool) $asc_sort_order ? 'ASC' : 'DESC';
		} else {
			$sort_by_ = 'date';
			$sort_order = 'DESC';
		}
	
		$cat_posts = new WP_Query(
				"showposts=" . $num .
				"&cat=" . $cat .
				"&orderby=" . $sort_by_ .
				"&order=" . $sort_order
		);
	
		return $cat_posts;
	}
}

if(!function_exists('sa_the_excerpt_max_charlength')){
	function sa_the_excerpt_max_charlength($charlength) {
		$excerpt = get_the_excerpt();
		$charlength++;
		
		if ( mb_strlen( $excerpt ) > $charlength ) {
			$subex = mb_substr( $excerpt, 0, $charlength - 5 );
			
			return $subex.'...';
		} 
		return null;
	}
}

if(!function_exists('sa_permerlink_wrapper')){
	function sa_permerlink_wrapper($id , $content , $option = array()){
		$default = array('href'=>get_permalink($id));
		$option  = sa_parse_args($option,$default);
		
		return SAHtml::content_html('a',$content,$option);
	}
}

if(!function_exists('sa_recent_post')){
	function sa_recent_post($options = array() , $wrapper_options=array()){
		$default = array('date-format'=>'Y-m-d','title-cut'=>25,'short-title-cut'=>12,'content-cut'=>55);
		
		$recent_posts = wp_get_recent_posts($options);
		$options = sa_parse_args($options,$default);
		
		$cnt = 0;
		$cutLength = 0;
		
		echo SAHtml::html('ul',$wrapper_options,true);
		
		foreach( $recent_posts as $recent ){
			if($cnt == 0) { $cutLength = $options['title-cut']; ?>
			
				<li class="top_recent_post">
			
			<?php }else   { $cutLength = $options['short-title-cut']; ?>
			
				<li class="short_recent_post">
			
			<?php }?>
					<h4>
						<a href="<?=get_permalink($recent['ID']) ?>" class="tooltips" title="자세한내용을 보시려면 Click">
							<?=sa_cut($recent["post_title"],$cutLength) ?>
						</a>
					</h4>
					<p>
						<?php if($cnt==0){?>
						<span class="recent_cont"><?=sa_cut($recent['post_content'],$options['content-cut']) ?></span>
						<?php }?>
						<span class="recent_date"><?=mysql2date($options['date-format'], $recent['post_date'])  ?></span>
					</p>
				</li>
			<?php 

			$cnt ++;
		}
		
		echo SAHtml::_html('ul');
	}
}

if(! function_exists('sa_get_upload_image')){
	function sa_get_upload_image($image){
		return WP_CONTENT_URL.'/uploads'.$image;
	}
}

if(! function_exists('sa_get_resource')){
	function sa_get_resource($file){
		global $saManager;
		
		return $saManager->getSaDirUrl().'/resources'.$file;
	}
}

if(! function_exists('sa_excerpt_filter')){
	$changeStr="";
	
	function sa_excerpt_filter($str){
		global $changeStr;
		
		$changeStr = $str;
		
		$sa_the_excerpt = create_function('$content', 'global $changeStr; return ereg_replace("\[\.\.\.\]",$changeStr,$content);');
		
		add_filter('the_excerpt', $sa_the_excerpt);
	}
}

if(! function_exists('sa_is_site_manager')){
	function sa_is_site_manager(){
		return current_user_can('manage_options'); 
	}
}


if(! function_exists('sa_ajax_login_init')){
	function sa_ajax_login_init(){
		add_action( 'wp_ajax_nopriv_ajaxlogin', 'sa_ajax_login' );
	}

	add_action('init', 'sa_ajax_login_init');
}

if(! function_exists('sa_ajax_login')){
	function sa_ajax_login(){
		check_ajax_referer( 'ajax-login-nonce', 'security' );
		
		$info = array();
		$info['user_login'] = $_POST['log'];
		$info['user_password'] = $_POST['pwd'];
		$info['remember'] = $_POST['rememberme'];
	
		$user_signon = wp_signon( $info, false );
		
		if ( is_wp_error($user_signon) ){
			echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
		} else {
			echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...')));
		}
	
		die();
	}
}

if(! function_exists('sa_login_form')){
	function sa_login_form($append){
	 	$form = wp_login_form(array('echo'=>false));
	 	$form = preg_replace("/<\\/form>/", '', $form);
		
	 	return $form.$append.'</form>';
	}
}	  

if(! function_exists('sa_get_contents_images')){
	function sa_get_contents_images($contents){
		$images = html_entity_decode($contents);
		$images = preg_replace('/(<p>)|(<\/p>)|(<br>)|(<br\/>)/', '', $images);
		$images = preg_replace('/<\/a>/', '</a>,', $images);
		
		$imagesList = explode(',', $images);
		
		$newList = array();
		
		foreach($imagesList as $image){
			if(!empty($image)){
				array_push($newList, $image);		
			}
		}
				
		return $newList;
	}
}

if(! function_exists('sa_ajax_address')){
	function sa_ajax_address(){
		if(!empty($_REQUEST['address_query'])){
			$result = get_post_code_xml_by_api($_REQUEST['address_query']);
			
			if ($result['error'] == false){
				$xml = new SimpleXMLElement($result['content']);
				echo print_postcode_json($xml);
			}
		}else{
			echo '{"result":"required address_query"}';
		}
		
		die();
	}
	
	add_action( 'wp_ajax_address_ajax', 'sa_ajax_address' );
	add_action( 'wp_ajax_nopriv_address_ajax', 'sa_ajax_address' );
}

if(! function_exists('sa_script')){
	function sa_script($script){
		echo '<script>(function($){$(document).ready(function(){'.$script.'});})(jQuery);</script>';
	}	
}

if(! function_exists('sa_alert')){
	function sa_alert($message){
		echo '<metah charset="utf-8"><script>alert("'.$message.'")</script>';
	}
}


if(! function_exists('sa_messi_alert')){
	function sa_messi_alert($message){
		sa_script('$.messi.alert("'.$message.'")');
	}
}

if(! function_exists('sa_get_first_image')){
	function sa_get_first_image ($postID){
		$args = array(
				'numberposts' => 1,
				'post_mime_type' => 'image',
				'post_parent' => $postID,
				'post_status' => null,
				'post_type' => 'attachment'
		);
	
		$attachments = get_children( $args );
		
		foreach($attachments as $attachment){
			return wp_get_attachment_thumb_url( $attachment->ID ); 
		}
	}
}

if(! function_exists('sa_get_currentUrl')){
	function sa_get_currentUrl(){
		return get_bloginfo('siteurl').SARequest::getRequestURI();
	}
}

if(! function_exists('sa_get_editable_roles')){
	function sa_get_editable_roles() {
		global $wp_roles;
	
		$all_roles = $wp_roles->roles;
		$editable_roles = apply_filters('editable_roles', $all_roles);
	
		return $editable_roles;
	}
}

if(! function_exists('sa_wp_dropdown_roles')){
	function sa_wp_dropdown_roles( $selected = false ) {
		$p = '';
		$r = '';
	
		$editable_roles = sa_get_editable_roles();
	
		echo 'a';
		
		foreach ( $editable_roles as $role => $details ) {
			$name = translate_user_role($details['name'] );
			
			if ( $selected == $role ) 
				$p = "\n\t<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
			else
				$r .= "\n\t<option value='" . esc_attr($role) . "'>$name</option>";
		}
		
		echo $p . $r;
	}
}

if(! function_exists('sa_wp_upload_url')){
	function sa_wp_upload_url(){
		$dir = wp_upload_dir();
		return $dir['baseurl'];
	}
}

if(! function_exists('sa_wp_upload_dir')){
	function sa_wp_upload_dir(){
		$dir = wp_upload_dir();
		return $dir['basedir'];
	}
}

if(! function_exists('sa_nonce_check')){
	function sa_nonce_check($nonce_name=''){
		$param = SARequest::getParameter($nonce_name);
		
		if ( !isset($param) || !wp_verify_nonce($param,$nonce_name) ){
			print 'Sorry, your nonce did not verify.';
			exit;
		}
	}
}

if(!function_exists('sa_get_wp_script_lists')){
	function sa_get_wp_script_lists() {
		return array (
				'jcrop',
				'swfobject',
				'swfupload',
				'swfupload-degrade',
				'swfupload-queue',
				'swfupload-handlers',
				'jquery',
				'jquery-form',
				'jquery-color',
				'jquery-masonry',
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-mouse',
				'jquery-ui-accordion',
				'jquery-ui-autocomplete',
				'jquery-ui-slider',
				'jquery-ui-tabs',
				'jquery-ui-sortable',
				'jquery-ui-draggable',
				'jquery-ui-droppable',
				'jquery-ui-selectable',
				'jquery-ui-position',
				'jquery-ui-datepicker',
				'jquery-ui-resizable',
				'jquery-ui-dialog',
				'jquery-ui-button',
				'jquery-effects-core',
				'jquery-effects-blind',
				'jquery-effects-bounce',
				'jquery-effects-clip',
				'jquery-effects-drop',
				'jquery-effects-explode',
				'jquery-effects-fade',
				'jquery-effects-fold',
				'jquery-effects-highlight',
				'jquery-effects-pulsate',
				'jquery-effects-scale',
				'jquery-effects-shake',
				'jquery-effects-slide',
				'jquery-effects-transfer',
				'wp-mediaelement',
				'schedule',
				'suggest',
				'thickbox',
				'hoverIntent',
				'jquery-hotkeys',
				'sack',
				'quicktags',
				'iris',
				'farbtastic',
				'colorpicker',
				'tiny_mce',
				'autosave',
				'wp-ajax-response',
				'wp-lists',
				'common',
				'editorremov',
				'editor-functions',
				'ajaxcat',
				'admin-categories',
				'admin-tags',
				'admin-custom-fields',
				'password-strength-meter',
				'admin-comments',
				'admin-users',
				'admin-forms',
				'xfn',
				'upload',
				'postbox',
				'slug',
				'post',
				'page',
				'link',
				'comment',
				'comment-reply',
				'admin-gallery',
				'media-upload',
				'admin-widgets',
				'word-count',
				'theme-preview',
				'json2',
				'plupload',
				'plupload-all',
				'plupload-html4',
				'plupload-html5',
				'plupload-flash',
				'plupload-silverlight',
				'underscore',
				'backbone'
		);
	}
}

if(!function_exists('sa_init_option')){
	function sa_init_option($name,$defaultValue=''){
		$option = get_option($name);
	
		if(empty($option)){
			add_option($name,$defaultValue);
		}
	
		return get_option($name);
	}	
}

if(!function_exists('sa_wp_editor')){
	function sa_wp_editor( $content, $editor_id, $settings = array() ) {
		wp_editor($content, $editor_id, $settings);
		
		echo '<span class="error">'. sa_get_array_value($_SESSION,'error_'.$editor_id).'</span>';
		
		unset($_SESSION['error_'.$editor_id]);
	}
}

if(!function_exists('sa_has_prev_post')){
	function sa_has_prev_post(){
		return get_adjacent_post(true, '', true);
	}
}

if(!function_exists('sa_has_next_post')){
	function sa_has_next_post(){
		return get_adjacent_post(true, '', false);
	}
}

if(!function_exists('sa_is_single_in_category')){
	function sa_is_single_in_category(){
		$category = get_the_category(get_the_ID());
		
		return is_single() && !empty($category);
	}
}

if(!function_exists('sa_get_current_category')){
	function sa_get_current_category(){
		$category = get_the_category(get_the_ID());
		return $category[0];
	}
}

if(!function_exists('sa_get_category_id_by_name')){
	function sa_get_category_id_by_name($name=''){
		$category_ids = get_all_category_ids();
		
		foreach($category_ids as $cat_id) {
			$cat_name = get_cat_name($cat_id);
			
			if($name == $cat_name){
				return $cat_id;
			}
		}
	}
}
