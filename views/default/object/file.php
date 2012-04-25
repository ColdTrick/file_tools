<?php
	/**
	 * Elgg file browser.
	 * File renderer.
	 * 
	 * @package ElggFile
	 */

	global $CONFIG;
	
	$context = elgg_get_context();
	
	$file = $vars['entity'];

	$file_guid = $file->getGUID();
	$tags = $file->tags;
	$title = $file->title;
	$desc = $file->description;
	$owner = $file->getOwnerEntity();

	$time_preference = "date";
	
	if($user_time_preference = elgg_get_plugin_user_setting('file_tools_time_display')){
		$time_preference = $user_time_preference;
	} elseif($site_time_preference = elgg_get_plugin_setting("file_tools_default_time_display", "file_tools")) {
		$time_preference = $site_time_preference;
	}
	
	if($time_preference == 'date')	{
		$friendlytime = date('d-m-Y G:i', $vars['entity']->time_created);
	} else {
		$friendlytime = elgg_view_friendly_time($vars['entity']->time_created);
	}

	$mime = $file->mimetype;

	if (!$title) {
		$title = elgg_echo('untitled');
	} elseif(strlen($title) > 18) {
		$title 	= substr($title, 0, 18) . '..';
	}
	
	$download_url = $vars['url'] . "mod/file/download.php?file_guid=" . $file_guid;
	$delete_url = $vars["url"] . "action/file/delete?guid=" . $file_guid;
	$edit_url = $vars["url"] . "file/edit/" . $file_guid;

	if(strpos($mime, 'image') !== false) {
		$file_icon = '<img src="' . $vars['url'] . 'mod/file/thumbnail.php?file_guid=' . $file_guid . '&size=small" />';
	} else {
		$file_icon = elgg_view("icon/object/file", array("entity" => $file, 'size' => 'small'));
	}
	
	if(!$vars["full"] && elgg_get_context() == "search")
	{
		echo elgg_view("input/hidden", array("name" => "file_guid", "value" => $file->getGUID()));
	}
	
	if($vars["full"] == false || $context == "search")
	{
		if(elgg_get_plugin_setting("user_folder_structure", "file_tools") == "yes" && get_input('search_viewtype') !== "gallery")
		{
			
			?>
			<div class="file_tools_file" id="file_<?php echo $file_guid; ?>">
				<div class="file_tools_file_title">
					<div class="file_tools_file_icon"><?php echo $file_icon;?></div>
					<a href="<?php echo $file->getURL();?>"><?php echo $title; ?></a>
				</div>
				<?php 		

				if(!empty($file->show_in_widget))
				{
					$hide = 'hide';
				}
				else
				{
					$hide = 'show';
				}
						
				if(!in_array($context, array('widget', 'search')))
				{
					?>
					<div class="file_tools_file_etc"><?php echo $friendlytime;?> <span><?php echo file_tools_get_file_extension($file);?></span></div>
					<?php 
					$margin = 'style="margin-right: 10px;"';
				}
				?>
				
				<div <?php echo $margin;?> class="file_tools_file_actions">
					<span><?php echo elgg_echo('file_tools:file:actions');?></span>
					<ul>
						<li><?php echo elgg_view("output/url", array("href" => $download_url, "text" => elgg_echo("Download")));?></li>
						<?php 
						if($file->canEdit())
						{
							echo '<li>' . elgg_view("output/url", array("href" => $edit_url, "text" => elgg_echo("edit")));?></li>
							<?php
							$js = "onclick=\"if(!confirm('". elgg_echo('question:areyousure') . "')){ return false; }\""; 
							echo '<li>' . elgg_view("output/url", array("href" => $delete_url, "text" => elgg_echo("delete"), "js" => $js, "is_action" => true)) . '</li>';
							
							if($context != 'search')
							{
							?><li><a href="<?php echo elgg_add_action_tokens_to_url($vars["url"] . 'action/file_tools/file/hide?guid='.$file->getGUID() . '&hide=' . $hide); ?>"><?php echo elgg_echo('widget:file_tools:' . $hide . '_file'); ?></a></li><?php
							} 
						}
						?>
					</ul>
				</div>
				<?php 
				
				if(!in_array($context, array('widget', 'search')))
				{?>
					<input style="float: right;" type="checkbox" name="file_tools_file_action_check" value="<?php echo $file->getGUID(); ?>" />
				<?php
				}
				?>
			</div><?php
		}
		elseif (get_input('search_viewtype') == "gallery") {
			echo "<div class=\"filerepo_gallery_item\">";
			if ($vars['entity']->smallthumb) {
				echo "<p class=\"filerepo_title\">" . $file->title . "</p>";
				echo "<p><a href=\"{$file->getURL()}\"><img src=\"{$vars['url']}mod/file/thumbnail.php?size=medium&file_guid={$vars['entity']->getGUID()}\" border=\"0\" /></a></p>";
				echo "<p class=\"filerepo_timestamp\"><small><a href=\"{$vars['url']}file/owner/{$owner->username}\">{$owner->username}</a> {$friendlytime}</small></p>";

				//get the number of comments
				$numcomments = $vars['entity']->countComments();
				if ($numcomments)
					echo "<p class=\"filerepo_comments\"><a href=\"{$file->getURL()}\">" . sprintf(elgg_echo("comments")) . " (" . $numcomments . ")</a></p>";

				
				//if the user can edit, display edit and delete links
				if ($file->canEdit()) {
					echo "<div class=\"filerepo_controls\"><p>";
					echo "<a href=\"{$vars['url']}file/edit/{$file->getGUID()}\">" . elgg_echo('edit') . "</a>&nbsp;";
					echo elgg_view('output/confirmlink',array(
						
							'href' => $vars['url'] . "action/file/delete?guid=" . $file->getGUID(),
							'text' => elgg_echo("delete"),
							'confirm' => elgg_echo("file:delete:confirm"),
							'is_action' => true,
						
						));
					echo "</p></div>";
				}
					
			
			} else {
				echo "<p class=\"filerepo_title\">{$title}</p>";
				echo "<a href=\"{$file->getURL()}\">" . elgg_view("file/icon", array("mimetype" => $mime, 'thumbnail' => $file->thumbnail, 'file_guid' => $file_guid, 'size' => 'large')) . "</a>";
				echo "<p class=\"filerepo_timestamp\"><small><a href=\"{$vars['url']}file/owner/{$owner->username}\">{$owner->name}</a> {$friendlytime}</small></p>";
				//get the number of comments
				$numcomments = $file->countComments();
				if ($numcomments)
					echo "<p class=\"filerepo_comments\"><a href=\"{$file->getURL()}\">" . sprintf(elgg_echo("comments")) . " (" . $numcomments . ")</a></p>";


			}
			echo "</div>";
			
		} else {
			$info = "<p><a href=\"{$file->getURL()}\">{$title}</a> <a style=\"float: right; margin-right:10px;\" href=\"{$vars['url']}mod/file/download.php?file_guid={$file_guid}\">download</a></p>";
			$info .= "<p class=\"owner_timestamp\"><a href=\"{$vars['url']}file/owner/{$owner->username}\">{$owner->name}</a> {$friendlytime}";
			$numcomments = $file->countComments();			
				if ($numcomments)
				{
					$info .= ", <a href=\"{$file->getURL()}\">" . sprintf(elgg_echo("comments")) . " (" . $numcomments . ")</a>";
				}			
			$info .= "</p>";

			$icon = "<a href=\"{$file->getURL()}\">" . $file_icon . "</a>";

			echo elgg_view('page/components/image_block', array('image' => $icon, 'body' => $info));
		}
	}
	else
	{
		$mime = $file->mimetype;
	//echo 'Start main version<br />';
	?>
	<div class="filerepo_file">
		<div class="filerepo_icon">
			<a href="<?php echo $vars['url']; ?>mod/file/download.php?file_guid=<?php echo $file_guid; ?>">
			<?php
			
				echo elgg_view("icon/object/file", array('entity' => $file));
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
				echo elgg_view_entity_icon($owner, 'tiny');
			?>
			<p class="filerepo_owner_details"><b><a href="<?php echo $vars['url']; ?>file/owner/<?php echo $owner->username; ?>"><?php echo $owner->name; ?></a></b><br />
			<small><?php echo $friendlytime; ?></small></p>
		</div>
		</div>

		<div class="filerepo_maincontent">

			<div class="filerepo_description"><?php echo elgg_view('output/longtext', array('value' => $desc)); ?></div>
			<div class="filerepo_tags">
<?php
	if (!empty($tags)) 
	{
		?><div class="object_tag_string"><?php
			echo elgg_view('output/tags',array('value' => $tags));				
		?></div><?php
	}

	$categories = elgg_view('output/categories',$vars);
	if (!empty($categories))
	{
		?><div class="filerepo_categories"><?php
			echo $categories;
		?></div><?php
	}

	?></div><?php

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
		<p><a href="<?php echo $vars['url']; ?>file/edit/<?php echo $file->getGUID(); ?>"><?php echo elgg_echo('edit'); ?></a>&nbsp;
		<?php 
			echo elgg_view('output/confirmlink',array(				
				'href' => $vars['url'] . "action/file/delete?guid=" . $file->getGUID(),
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
}