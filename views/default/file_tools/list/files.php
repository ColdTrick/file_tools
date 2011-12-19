<?php

	$files = $vars["files"];
	$folder = $vars["folder"];
	
	$old_context = get_context();
	set_context('search');
	
	$folder_content = elgg_view("file_tools/list/folder", $vars);	
	
	if(!empty($files))
	{
		
		$url = parse_url($_SERVER['REQUEST_URI']);
		$baseurl = $url["path"];
		
		if(!empty($folder))
		{
			$baseurl .= "/" . $folder->guid;
		}

		$files_content = elgg_view_entity_list($files, $vars, 0, false, false, false);
		
	}
	
	set_context($old_context);
?>
<div id="file_tools_list_files">
	<div id="file_tools_list_files_overlay"></div>
	<?php echo $folder_content . $files_content; ?>
</div>

<?php 
if(page_owner_entity()->canEdit() || (page_owner_entity() instanceof ElggGroup && page_owner_entity()->isMember()))
{?>
<script type="text/javascript">

	$(function(){
		$("#file_tools_list_files .file_tools_file, #file_tools_list_files .file_tools_folder").draggable({
			"revert": "invalid",
			"opacity": 0.7,
			"cursor": "move"
		}).css("cursor", "move");
	});

</script>
<?php 
} 
?>