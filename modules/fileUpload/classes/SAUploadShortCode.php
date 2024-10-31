<?php
class SAUploadShortCode {
	private static $instance;
	
	public static function getInstance() {
		if (! isset ( self::$instance )) {
			self::$instance = new SAUploadShortCode();
		}
	
		return self::$instance;
	}
	
	public function init(){
		add_shortcode( 'sa_file_upload_button' , array(&$this,'sa_file_upload_form_button'));
	}
	
	public function sa_file_upload_form () { 
		global $saManager;
		
		wp_enqueue_style ('fileuploadform-css',$saManager->getSaPluginUrl().'/fileUpload/resources/fileuploadform.css');
		wp_enqueue_script('fileuploadform-js',$saManager->getSaPluginUrl().'/fileUpload/resources/fileuploadform.js');
		
		?>
		<form id="fileForm" enctype="multipart/form-data" method="post" style="display:none;">
			<input type="file" name="safile[]" />
			<input type="hidden" name="fileFormfileName" id="fileFormfileName"/>
			<input type="hidden" name="action" value="ajaxSaFileUplad" />
			
			<div class="wp-core-ui wp-media-buttons add-file-btn">		
				<input type="submit" value="이미지 추가" class="button"/>
			</div>
			
			<div id="fileForm_progress">
				<img src="<?=WP_CONTENT_URL ?>/../wp-includes/images/wpspin-2x.gif" alt="" />
			</div>
			<div id="fileForm_prev_image_view">
				<ul id="fileForm_prev_image_view_box">
				</ul>
			</div>
				
			<input type="hidden" name="type" value="image"/>
			<?php wp_nonce_field('safile_upload_ajax_nonce'); ?>
		</form>
	<?php }
	
	public function sa_file_upload_form_button () { 
		add_action('get_footer', array($this,'sa_file_upload_form')); ?>
		<div class="wp-core-ui wp-media-buttons">
			<a href="#fileForm" class="fancybox button add_media">
				<span class="wp-media-buttons-icon"></span>이미지삽입
			</a>
		</div>
	<?php }
}