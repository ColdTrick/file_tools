<?php 

	$folder = $vars["folder"];
	
	?>
	
	<div class="contentWrapper" id="file_tools_list_folder">
		<?php echo elgg_view('file_tools/breadcrumb', array('entity' => $folder)); ?>
	</div>
	
<?php 

	if($sub_folders = file_tools_get_sub_folders($folder)) {
		echo $sub_folders;
	}