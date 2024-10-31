<div id="<?=$model->get('controller')->slug ?>" class="wrap">
	<div id="icon-themes" class="icon32"><br></div>
		
	<h2 class="nav-tab-wrapper">
		<div class="nav-tabs">
			<?=$model->get('controller')->makeMenu() ?>
		</div>
	</h2>
	
	<br />
	
	<?= SARequest::outputSessionMessage($model->get('controller')->slug) ?>