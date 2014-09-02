<?php if (!defined('IN_CMS')) { exit(); } ?>


<div id="sidebar">
	<p class="button">
		<a href="<?=get_url('plugin/gallery'); ?>">
			<img src="<?=URL_PUBLIC?>wolf/icons/file-image-32-ns.png" align="middle" alt="page icon">
			<?=__('Photo albums')?>
		</a>
	</p>
	<p class="button">
		<a href="<?=get_url('plugin/gallery/add');?>">
			<img src="<?=URL_PUBLIC?>wolf/icons/action-add-32-ns.png" align="middle" alt="page icon">
			<?=__('Create album')?>
		</a>
	</p>
	<p class="button">
		<a href="<?=get_url('plugin/gallery/settings'); ?>">
			<img src="<?=URL_PUBLIC?>wolf/icons/settings-32-ns.png" align="middle" alt="page icon">
			<?=__('Settings')?>
		</a>
	</p>
</div>


<!-- <div class="box" id="menu-sidebar">
	<h2>Lorem ipsum</h2> dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
</div> -->