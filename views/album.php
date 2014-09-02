<?php if(!defined('IN_CMS')) { exit(); } ?>

<h1><?=html_encode($album->name)?></h1>

<p><div id="gallery-sortable" class="clearfix">
<?php
	
	$images = GalleryImage::findByAlbumId($album->id);
	
	if(!count($images)) {
		echo '<i>'.__('There are no images in this album.').'</i>';
	}
	
	foreach($images as $image) {
		// style="background-image: url('.URI_PUBLIC.'public/'.Plugin::getSetting('path', 'gallery').$image->thumb_filename.')"
		echo '<div class="gallery-image" data-id="'.$image->id.'"><a href="'.get_url('plugin/gallery/image/'.$image->id).'"><img src="'.$image->getImage('thumb').'"></a></div>';
	}
?>
</div></p>

<script type="text/javascript" charset="utf-8">
	
$(document).ready(function() {
	$('#gallery-sortable').sortable({
		// disabled: true,
		tolerance: 'intersect',
		// containment: '#main',
		// placeholder: 'placeholder',
		// revert: true,
		// handle: '.handle',
		cursor: 'crosshair',
		distance: '15',
		stop: function(event, ui) {
			// var order = $(ui.item.parent()).sortable('serialize', {key: 'snippets[]'});
			var order = [];
			$('#gallery-sortable > div').each(function() {
				order.push($(this).data('id'));
			});
			$.post('<?=get_url('plugin/gallery/image_reorder')?>', {data : order});
		}
	});
});
	
</script>