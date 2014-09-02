<?php if (!defined('IN_CMS')) { exit(); } ?>

<h1><?=__('Edit image')?></h1>

<style type="text/css" media="screen">
	
</style>
<p>
	<form action="<?=get_url('plugin/gallery/image/'.$image->id)?>" method="post" accept-charset="utf-8">
		<div class="clearfix">
			<div style="float: right;">
<?php
	$prev = $image->getPrevImg();
	if($prev) {
		echo ' <a href="'.get_url('plugin/gallery/image/'.$prev->id).'">'.__('previous').'</a> ';
		// echo ' | ';
	}
	echo ' <small>('.$image->getPosition().' / '.$image->getCount().')</small> ';
	$next = $image->getNextImg();
	if($next) {
		// echo ' | ';
		echo ' <a href="'.get_url('plugin/gallery/image/'.$next->id).'">'.__('next').'</a> ';
	}
?>
			</div>
			<div class="clearfix"></div>
			<div>
				<table class="full-width-table">
					<tr valign="top">
						<td width="150px"><!-- <label for="name"><?=__('Image')?></label> --></td>
						<td>
							<img src="<?=$image->getImage('img');?>" style="max-height: 200px;" />
						</td>
					</tr>
					<tr valign="top">
						<td width="150px"><label for="name"><?=__('Name')?></label></td>
						<td><input type="text" name="name" value="<?=html_encode($image->name);?>" id="name" class="form-input full-width"/></td>
					</tr>
					<tr valign="top">
						<td><label for="description"><?=__('Description')?></label></td>
						<td><textarea rows="10" name="description" id="description" class="form-input full-width"><?=html_encode($image->description);?></textarea></td>
					</tr>
<?php

function getFilesize($file,$digits = 2) {
	if(is_file($file)) {
		$fileSize = filesize($file);
		$sizes = array('TB','GB','MB','KB','B');
		$total = count($sizes);
		while ($total-- && $fileSize > 1024) {
			$fileSize /= 1024;
		}
		return round($fileSize, $digits).' '.$sizes[$total];
	}
	return false;
}

$files = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator($image->getPathAbsolute(), RecursiveDirectoryIterator::SKIP_DOTS)
);
if(count($files)):
?>
					<tr valign="top">
						<td><label for="description"><?=__('Informations')?></label></td>
						<td>
							<table class="full-width-table">
								<tr>
									<th><?=__('Filename')?></th>
									<th><?=__('Filesize')?></th>
									<th><?=__('Size')?></th>
								</tr>
<?php
foreach($files as $img) {
	$size = getimagesize($img->getPathname());
	echo '<tr>'."\n";
	echo '<td><a href="'.$image->getPath().$img->getFilename().'">'.$img->getFilename().'</a></td> ';
	echo '<td align="right">'.getFilesize($img->getPathname()).'</td> ';
	echo '<td align="right">'.$size[0].' x '.$size[1].'</td> ';
	echo '</tr>'."\n";
}
?>
							</table>
						</td>
					</tr>
<?php
endif;
?>
				</table>
			</div>
		</div>
		<p>
			<input type="submit" value="<?=__('Save')?>" />
<?php $next = $image->getNextImg(); if($next): ?>
			<input type="submit" name="next" value="<?=__('Save and next')?>" />
<?php endif; ?>
			<a href="<?=get_url('plugin/gallery/remove/'.$image->id)?>" onclick="return confirm('<?=__('Are you sure you want to delete this image?');?>');"><?=__('Remove')?></a>
			<a href="<?=get_url('plugin/gallery/album/'.$image->album_id)?>"><?=__('Cancel')?></a>
		</p>
	</form>
</p>