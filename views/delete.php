<?php if (!defined('IN_CMS')) { exit(); } ?>

<h1><?=__('Delete album')?></h1>

<p>
<form action="<?=get_url('plugin/gallery/delete/'.$album->id)?>" method="post" accept-charset="utf-8">
	
	<input type="hidden" name="id" value="<?=$album->id?>" />
	<h3><?=html_encode($album->name)?></h3>
	
	<?=__('If you continue you will delete this album with all its images.')?>
	
	<p>
		<input type="submit" value="<?=__('Delete')?>" />
		<a href="<?=get_url('plugin/gallery')?>"><?=__('Cancel')?></a>
	</p>
</form>
</p>