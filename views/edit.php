<?php if (!defined('IN_CMS')) { exit(); } ?>

<h1><?=$album->id==''?__('Create album'):__('Edit album')?></h1>

<p>
<form action="<?=get_url('plugin/gallery/edit/'.$album->id)?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">
	
	<!-- <input type="hidden" name="id" value="<?=$album->id?>" /> -->
	
	<table class="full-width-table">
		<tr valign="top">
			<td class="label" width="20%">
				<label for="name"><?=__('Name')?></label>
			</td>
			<td class="field">
				<input type="text" name="name" value="<?=html_encode($album->name)?>" id="name" class="form-input full-width"/>
			</td>
		</tr>
		<tr valign="top">
			<td class="label">
				<label for="description"><?=__('Description')?></label>
			</td>
			<td class="field">
				<textarea rows="6" name="description" id="description" class="form-input full-width"><?=html_encode($album->description);?></textarea>
			</td>
		</tr>
		<tr valign="top">
			<td class="label">
				<label for="image"><?=__('Gallery image')?></label>
			</td>
			<td class="field">
				<input type="file" name="image" value="" id="image" />
<?php if($album->getImage('thumb')): ?>
				<div style="margin-top: 10px;">
					<img src="<?=$album->getImage('thumb')?>" style="max-height: 200px;"/>
				</div>
<?php endif; ?>
			</td>
		</tr>
	</table>
	
	<p>
		<input type="submit" value="<?=__('Save')?>" />
		<a href="<?=get_url('plugin/gallery')?>"><?=__('Cancel')?></a>
	</p>
</form>
</p>