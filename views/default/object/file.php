<?php
	/**
	 * Elgg file browser.
	 * File renderer.
	 * 
	 * @package ElggFile
	 */

	global $CONFIG;
	
	$file 			= $vars['entity'];
		
	$file_guid 		= $file->getGUID();
	$tags 			= $file->tags;
	$title 			= $file->title;
	$desc 			= $file->description;
	$owner 			= $vars['entity']->getOwnerEntity();
	
	if(get_plugin_usersetting('file_tools_time_display') == 'date')
	{
		$friendlytime 	= date('d-m-Y G:i', $vars['entity']->time_created);
	}
	else
	{
		$friendlytime 	= elgg_view_friendly_time($vars['entity']->time_created);
	}
	
	
	$mime 			= $file->simpletype;

	if (!$title)
	{
		$title = elgg_echo('untitled');
	}
	elseif(strlen($title) > 20)
	{
		$title 	= substr($title, 0, 20) . '..';
	}

	$download_url = $vars['url'] . "mod/file/download.php?file_guid=" . $file_guid;
	$delete_url = $vars["url"] . "action/file/delete?file=" . $file->getGUID();
	$edit_url = $vars["url"] . "pg/file/edit/" . $file->getGUID();
	
	if(strpos($mime, 'image') !== false)
	{
		$file_icon = '<img src="' . $vars['url'] . 'mod/file/thumbnail.php?file_guid=' . $file_guid . '&size=small" />';
	}
	else
	{
		$file_icon = elgg_view("file/icon", array("mimetype" => $mime, 'thumbnail' => $file->thumbnail, 'file_guid' => $file_guid, 'size' => 'small'));
		//$file->getIcon();
	}
	
	if(get_context() == 'search')
	{
	?>
	<div class="file_tools_file">
	
		<div class="file_tools_file_title">
			<div class="file_tools_file_icon"><?php echo $file_icon;?></div>
			
			<a href="<?php echo $file->getURL();?>"><?php echo $title; ?></a>
		</div>
		
		<div class="file_tools_file_etc">
			<?php echo $friendlytime;?>
		</div>
		
		<div class="file_tools_file_actions">
			<?php echo elgg_view("output/url", array("href" => $download_url, "text" => elgg_echo("Download")));?> | 
			<?php echo elgg_view("output/url", array("href" => $edit_url, "text" => elgg_echo("edit")));?> |
			<?php
				$js = "onclick=\"if(!confirm('". elgg_echo('question:areyousure') . "')){ return false; }\""; 
				echo elgg_view("output/url", array("href" => $delete_url, "text" => elgg_echo("delete"), "js" => $js, "is_action" => true));
			?>
		</div>
		
		<?php /*echo $friendlytime;?> <?php echo $mime;?> <a href="<?php echo $vars['url']; ?>mod/file/download.php?file_guid=<?php echo $file_guid; ?>">Download</a> */?>
		 
	</div>
	<?php 
	}
	else
	{
		// Start main version
	?>
	<div class="filerepo_file">
		<div class="filerepo_icon">
					<a href="<?php echo $vars['url']; ?>mod/file/download.php?file_guid=<?php echo $file_guid; ?>"><?php 
						
						echo elgg_view("file/icon", array("mimetype" => $mime, 'thumbnail' => $file->thumbnail, 'file_guid' => $file_guid)); 
						
					?></a>					
		</div>
		
		<div class="filerepo_title_owner_wrapper">
		<?php
			//get the user and a link to their gallery
			$user_gallery = $vars['url'] . "mod/file/search.php?md_type=simpletype&subtype=file&tag=image&owner_guid=" . $owner->guid . "&search_viewtype=gallery";
		?>
		<div class="filerepo_user_gallery_link"><a href="<?php echo $user_gallery; ?>"><?php echo sprintf(elgg_echo("file:user:gallery"),''); ?></a></div>
		<div class="filerepo_title"><h2><a href="<?php echo $vars['url']; ?>mod/file/download.php?file_guid=<?php echo $file_guid; ?>"><?php echo $title; ?></a></h2></div>
		<div class="filerepo_owner">
				<?php

					echo elgg_view("profile/icon",array('entity' => $owner, 'size' => 'tiny'));
				
				?>
				<p class="filerepo_owner_details"><b><a href="<?php echo $vars['url']; ?>pg/file/owner/<?php echo $owner->username; ?>"><?php echo $owner->name; ?></a></b><br />
				<small><?php echo $friendlytime; ?></small></p>
		</div>
		</div>

		
		<div class="filerepo_maincontent">
		
				<div class="filerepo_description"><?php echo elgg_view('output/longtext', array('value' => $desc)); ?></div>
				<div class="filerepo_tags">
<?php
	if (!empty($tags)) 
	{
?>
		<div class="object_tag_string"><?php

					echo elgg_view('output/tags',array('value' => $tags));
				
				?></div>
<?php
	}

	$categories = elgg_view('categories/view',$vars);
	if (!empty($categories))
	{
?>
		<div class="filerepo_categories">
			<?php

				echo $categories;
			
			?>
		</div>
<?php
	}
?>
				</div>
<?php 
	if (elgg_view_exists('file/specialcontent/' . $mime))
	{
		echo "<div class=\"filerepo_specialcontent\">".elgg_view('file/specialcontent/' . $mime, $vars)."</div>";
	}
	else if (elgg_view_exists("file/specialcontent/" . substr($mime,0,strpos($mime,'/')) . "/default"))
	{
		echo "<div class=\"filerepo_specialcontent\">".elgg_view("file/specialcontent/" . substr($mime,0,strpos($mime,'/')) . "/default", $vars)."</div>";
	}

?>
		
	<div class="filerepo_download"><p><a href="<?php echo $vars['url']; ?>mod/file/download.php?file_guid=<?php echo $file_guid; ?>"><?php echo elgg_echo("file:download"); ?></a></p></div>
		
<?php

	if ($file->canEdit())
	{
?>
	<div class="filerepo_controls">
		<p>
			<a href="<?php echo $vars['url']; ?>pg/file/edit/<?php echo $file->getGUID(); ?>"><?php echo elgg_echo('edit'); ?></a>&nbsp;
			<?php 
				echo elgg_view('output/confirmlink',array(				
					'href' => $vars['url'] . "action/file/delete?file=" . $file->getGUID(),
					'text' => elgg_echo("delete"),
					'confirm' => elgg_echo("file:delete:confirm"),
					'is_action' => true,
				));  
			?>
		</p>
	</div>
<?php		
	}
?>
	</div>
</div>

<?php

	if ($vars['full']) 
	{
		
		echo elgg_view_comments($file);
		
	}
}