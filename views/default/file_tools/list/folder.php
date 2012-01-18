<?php 

	$folder = $vars["folder"];
	
	/*if($folder)
	{
		$delete_url = $vars["url"] . "action/file_tools/folder/delete?folder_guid=" . $folder->getGUID();
		$edit_url = $vars["url"] . "pg/file_tools/folder/edit/" . $folder->getGUID();
	
		?>
		<div class="contentWrapper" id="file_tools_list_folder">
		<?php 
		
		if($folder->canEdit())
		{
			?>
			<div id="file_tools_list_folder_actions">
				<?php echo elgg_view("output/url", array("href" => $edit_url, "text" => elgg_echo("edit")));?> |
				<?php
					$js = "onclick=\"if(confirm('". elgg_echo('question:areyousure') . "')){ file_tools_remove_folder_files(this); return true;} else { return false; }\""; 
					echo elgg_view("output/url", array("href" => $delete_url, "text" => elgg_echo("delete"), "js" => $js, "is_action" => true));
				?>
			</div>
			<?php 
		}
		?>
		<h3>
			<a class="file_tools_load_folder" rel="<?php echo $folder->parent_guid; ?>" href="javascript: void(0);"><img src="<?php echo $vars['url'] . 'mod/file_tools/_graphics/folder_back.png'?>" /></a> <?php echo $folder->title;?>
		</h3>
		<?php 
	}
	else
	{?>
		<div id="file_tools_list_folder" class="contentWrapper">
			<h3><?php echo elgg_echo('file_tools:input:folder_select:main'); ?></h3>
		
	<?php 
	}*/
	?>
	
	<div class="contentWrapper" id="file_tools_list_folder">
		<?php echo elgg_view('file_tools/breadcrumb', array('entity' => $folder)); ?>
	</div>
	
<?php 

	if($sub_folders = file_tools_get_sub_folders($folder))
	{
		echo $sub_folders;
	}