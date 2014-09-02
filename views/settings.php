<?php if (!defined('IN_CMS')) { exit(); } ?>

<h1><?php echo __('Settings'); ?></h1>
<p>
	<form id="gallery_settings" action="<?=get_url('plugin/gallery/settings');?>" method="post">
		<table class="full-width-table">
			<tr>
				<td width="230"><?=__('Path');?></td>
				<td>
					<?=URI_PUBLIC?>public/
					<input type="text" name="settings[path]" value="<?=html_encode($settings['path'])?>" class="form-input" />
				</td>
			</tr>
			<tr>
				<td><?=__('Detect image title');?></td>
				<td>
					<input type="checkbox" name="settings[detect_title]" value="true"<?=($settings['detect_title']) == 'true' ? ' checked="checked"' : null?> />
					<input type="text" name="settings[exif_title_field]" value="<?=html_encode($settings['exif_title_field'])?>" class="form-input" placeholder="ImageDescription" />
					<small>(<?=__('Default Exif value')?>: ImageDescription)</small>
				</td>
			</tr>
			<tr>
				<td><?=__('Detect image description');?></td>
				<td>
					<input type="checkbox" name="settings[detect_description]" value="true"<?=($settings['detect_description']) == 'true' ? ' checked="checked"' : null?> />
					<input type="text" name="settings[exif_description_field]" value="<?=html_encode($settings['exif_description_field'])?>" class="form-input" placeholder="UserComment" />
					<small>(<?=__('Default Exif value')?>: UserComment)</small>
				</td>
			</tr>
			<tr>
				<td><?=__('Use filename as title');?></td>
				<td>
					<label><input type="radio" name="settings[filename_as_title]" value="true"<?=html_encode($settings['filename_as_title']) == 'true' ? ' checked="checked"' : null?> /> <?=__('Yes')?></label>
					<label><input type="radio" name="settings[filename_as_title]" value="false"<?=html_encode($settings['filename_as_title']) == 'false' ? ' checked="checked"' : null?> /> <?=__('No')?></label>
				</td>
			</tr>
			<tr>
				<td><?=__('Gallery page id');?></td>
				<td>
					<input type="checkbox" name="settings[add_route]" value="true"<?=($settings['add_route']) == 'true' ? ' checked="checked"' : null?> />
					<input type="text" name="settings[route_page_id]" value="<?=html_encode($settings['route_page_id'])?>" class="form-input" placeholder="<?=__('Page id')?>" />
					<small>(<?=__('Page id for the gallery route')?> <code>Dispatcher::addRoute</code>)</small>
				</td>
			</tr>
		</table>
		
		<br>
		<br>
		
		<h3><?=__('Image resize options')?></h3>
		
		<table class="full-width-table" id="img_setting_table">
			<thead>
				<tr>
					<th><?=__('Name')?></th>
					<th><?=__('Resize method')?></th>
					<th><?=__('Width')?></th>
					<th><?=__('Height')?></th>
					<th><?=__('Quality')?></th>
					<!-- <th><?=__('Image type')?></th> -->
					<th><?=__('Retina')?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8" style="text-align: center;">
						<button type="button" id="add_image_setting"><?=__('Add image')?></button>
					</td>
				</tr>
				<tr id="img_setting_tempalte" class="hidden">
					<td>
						<input type="text" name="img_settings[#ID#][name]" value="" class="form-input full-width" placeholder="<?=__('Name')?>">
					</td>
					<td>
						<select name="img_settings[#ID#][method]" class="form-input full-width">
							<option value="">- <?=__('none')?> -</option>
							<option value="resizeDeform">resizeDeform(w, h)</option>
							<option value="resizeFill">resizeFill(w, h)</option>
							<option value="resizeFit">resizeFit(w, h)</option>
							<option value="resizeWidth">resizeWidth(w)</option>
							<option value="resizeHeight">resizeHeight(h)</option>
							<option value="resizeMax">resizeMax(w, h)</option>
							<option value="resizeLongEdge">resizeLongEdge(w)</option>
							<option value="resizeScale">resizeScale(p)</option>
						</select>
					</td>
					<td>
						<input type="text" name="img_settings[#ID#][width]" value="" class="form-input full-width" placeholder="<?=__('Width')?>">
					</td>
					<td>
						<input type="text" name="img_settings[#ID#][height]" value="" class="form-input full-width" placeholder="<?=__('Height')?>">
					</td>
					<td>
						<input type="text" name="img_settings[#ID#][quality]" value="" class="form-input full-width" placeholder="<?=__('Quality')?>">
					</td>
					<!-- <td>
						<select name="img_settings[#ID#][type]" class="form-input full-width">
							<option value="jpg">JPEG</option>
							<option value="png">PNG</option>
							<option value="gif">GIF</option>
						</select>
					</td> -->
					<td>
						<input type="checkbox" name="img_settings[#ID#][retina]" value="true">
					</td>
					<td>
						<button type="button" onclick="ImgSetting.removeTr(this)"><?=__('Remove')?></button>
					</td>
				</tr>
			</tfoot>
		</table>
		
		<p>
			<input type="submit" value="<?=__('Save')?>" id="">
			<a href="<?=get_url('plugin/gallery');?>"><?=__('Cancel')?></a>
		</p>
	</form>
</p>

<script type="text/javascript">

(function($) {
	
	window.guid = (function() {
		function s4() {
			return Math.floor((1 + Math.random()) * 0x10000)
			.toString(16)
			.substring(1);
		}
		return function() {
			return s4() + s4();
		};
	})();
	
	$(document).ready(function() {
		
		var getSettingImages = $.parseJSON('<?=$settings['images']?>');
		
		var ImgSetting = window.ImgSetting = {
			addTr: function() {
				var tr = $('#img_setting_tempalte').clone().attr('id', null).removeClass('hidden');
				var uniqueId = guid();
				tr.find('input, select, textarea').each(function() {
					$(this).attr('name', $(this).attr('name').replace('#ID#', uniqueId));
				});
				tr.appendTo($('#img_setting_table tbody'));
				return tr;
			},
			removeTr: function(tr) {
				$(tr).parents('tr').remove();
			},
			fill: function() {
				$.each(getSettingImages, function(index, img) {
					var tr = ImgSetting.addTr();
					tr.find('input[name*=name]').first().attr('value', img.name);
					tr.find('input[name*=width]').first().attr('value', img.width);
					tr.find('input[name*=height]').first().attr('value', img.height);
					tr.find('input[name*=quality]').first().attr('value', img.quality);
					tr.find('select[name*=method] option[value='+img.method+']').first().attr('selected', 'selected');
					// tr.find('select[name*=type] option[value='+img.type+']').first().attr('selected', 'selected');
					if(img.retina === 'true') {
						tr.find('input[name*=retina][value=true]').first().attr('checked', 'checked');
					}
				});
			}
		}
		
		ImgSetting.fill(getSettingImages);
		
		$('#add_image_setting').click(function(event) {
			event.preventDefault();
			ImgSetting.addTr();
		});
		
	});
	
})(jQuery);

</script>