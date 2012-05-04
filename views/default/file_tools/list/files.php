<?php

	$files = $vars["files"];
	$folder = $vars["folder"];
	
	$folder_content = elgg_view("file_tools/list/folder", $vars);	
	
	if(!empty($files))
	{
		
		$url = parse_url($_SERVER['REQUEST_URI']);
		$baseurl = $url["path"];
		
		if(!empty($folder))
		{
			$baseurl .= "/" . $folder->getGUID();
		}

		$files_content = elgg_view_entity_list($files, $vars, 0, false, false, false);
	}
	
?>
<div id="file_tools_list_files">
	<div id="file_tools_list_files_overlay"></div>
	<?php echo $folder_content . $files_content;
	
	if(!$files_content)
	{
		echo elgg_echo("file_tools:list:files:none");
	}
	
	?>
</div>

<?php 
if(elgg_get_page_owner_entity()->canEdit() || (elgg_get_page_owner_entity() instanceof ElggGroup && elgg_get_page_owner_entity()->isMember()))
{?>
<script type="text/javascript">

	$(function(){
		$("#file_tools_list_files .file_tools_file").draggable({
			"revert": "invalid",
			"opacity": 0.7,
			"cursor": "move"
		}).css("cursor", "move");
	});

</script>
<?php 
} 