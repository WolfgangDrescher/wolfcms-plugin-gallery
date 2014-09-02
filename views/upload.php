<?php if (!defined('IN_CMS')) { exit(); } ?>

<h1><?php echo __('Upload'); ?></h1>
<p>
	<?=__('Album')?>: <?=$album->name?>
	
	<form id="upload" method="post" action="<?=get_url('plugin/gallery/upload/'.$album->id)?>" enctype="multipart/form-data">
		<div id="drop">
			<a><?=__('Browse')?></a>
			<?=__('or drop here')?>
			<input type="file" name="upl" multiple />
			<input type="hidden" name="album_id" value="<?=$album->id?>" />
		</div>
		<ul><!-- The file uploads will be shown here --></ul>
	</form>
</p>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>

<script src="<?=PLUGINS_URI?>gallery/js/jquery.knob.js"></script>	

<script src="<?=PLUGINS_URI?>gallery/js/jQuery-File-Upload-9.7.0/js/vendor/jquery.ui.widget.js"></script>
<script src="<?=PLUGINS_URI?>gallery/js/jQuery-File-Upload-9.7.0/js/jquery.iframe-transport.js"></script>
<script src="<?=PLUGINS_URI?>gallery/js/jQuery-File-Upload-9.7.0/js/jquery.fileupload.js"></script>

<script src="<?=PLUGINS_URI?>gallery/js/upload.js"></script>