<?php

if (!defined('IN_CMS')) { exit(); }

?>
<h1><?=__('Photo albums'); ?></h1>

<table class="index" border="0" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<th width="50"><?=__('Thumbnail')?></th>
			<th><?=__('Name')?></th>
			<th><?=__('Description')?></th>
			<th><?=__('Number of images')?></th>
			<th width="100"><?=__('Edit')?></th>
		</tr>
	</thead>
	<tbody id="gallery-sortable">
<?php
	foreach(GalleryAlbum::findAll() as $album) {
		echo '<tr data-id="'.$album->id.'">';
		echo '	<td>';
		echo '		<div class="gallery-thumb" style="background-image: url('.$album->getImage('thumb').');"></div>';
		echo '	</td>';
		echo '	<td>';
		echo '		<a href="'.get_url('plugin/gallery/album/'.$album->id).'">'.html_encode($album->name).'</a>';
		echo '	</td>';
		echo '	<td>';
		echo '		'.html_encode($album->description);
		echo '	</td>';
		echo '	<td>';
		echo Record::countFrom('GalleryImage', ' album_id = :album_id ', array(':album_id' => $album->id));
		echo '	</td>';
		echo '	<td>';
		echo '		<a href="'.get_url('plugin/gallery/upload/'.$album->id).'"><img src="'.URL_PUBLIC.'wolf/icons/action-upload-16.png" alt="upload images"/></a>';
		echo '		<a href="'.get_url('plugin/gallery/edit/'.$album->id).'"><img src="'.URL_PUBLIC.'wolf/icons/action-rename-16.png" alt="rename album"/></a>';
		echo '		<a href="'.get_url('plugin/gallery/delete/'.$album->id).'"><img src="'.URL_PUBLIC.'wolf/icons/action-delete-16.png" alt="delete album"/></a>';
		echo '	</td>';
		echo '</tr>';
	}
?>
	</tbody>
</table>

<script type="text/javascript">

$('#gallery-sortable').sortable({
	// disabled: true,
	tolerance: 'intersect',
	// containment: '#main',
	// placeholder: 'placeholder',
	// revert: true,
	// handle: '.handle',
	distance: '15',
	stop: function(event, ui) {
		// var order = $(ui.item.parent()).sortable('serialize', {key: 'snippets[]'});
		var order = [];
		$('#gallery-sortable > tr').each(function() {
			order.push($(this).data('id'));
		});
		$.post('<?=get_url('plugin/gallery/album_reorder')?>', {data : order});
	}
});
</script>