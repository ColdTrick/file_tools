<?php 

	$folder = $vars["folder"];
	$page_owner = $vars["page_owner_entity"];
	
	if(!empty($folder))
	{
		$title = $folder->title;
		$desc = $folder->description;
		
		if(!empty($folder->parent_guid))
		{
			$parent = $folder->parent_guid;
		}
		else
		{
			$parent = 0;
		}
		
		$access_id = $folder->access_id;
		
		$form_data = elgg_view("input/hidden", array("name" => "guid", "value" => $folder->getGUID()));
		
		$submit_text = elgg_echo("update");
	}
	else
	{
		$title = "";
		$desc = "";
		
		$parent = get_input("folder_guid", 0);
		
		if(!empty($parent) && ($parent_entity = get_entity($parent)))
		{
			$access_id = $parent_entity->access_id;
		}
		else
		{
			if($page_owner instanceof ElggGroup)
			{
				$access_id = $page_owner->group_acl;
			}
			else
			{
				$access_id = ACCESS_DEFAULT;
			}
		}
		
		$submit_text = elgg_echo("save");
	}
	$form_data .= elgg_view("input/hidden", array("name" => "page_owner", "value" => $page_owner->getGUID()));

	$form_data .= "<div><label>" . elgg_echo("file_tools:forms:edit:title") . "</label></div>\n";
	$form_data .= elgg_view("input/text", array("name" => "title", "value" => $title));
	
	$form_data .= "<div><label>" . elgg_echo("file_tools:forms:edit:description") . "</label></div>\n";
	$form_data .= elgg_view("input/longtext", array("name" => "description", "value" => $desc));
	
	$form_data .= "<div><label>" . elgg_echo("file_tools:forms:edit:parent") . "</label></div>\n";
	$form_data .= elgg_view("input/folder_select", array("name" => "file_tools_parent_guid", "folder" => $folder, "value" => $parent, "owner_guid" => $page_owner->getGUID(), 'type' => 'folder'));
	
	// set context to influence access
	$context = elgg_get_context();
	elgg_set_context("file_tools");
	
	$form_data .= "<div><label>" . elgg_echo("access") . "</label></div>\n";
	$form_data .= elgg_view("input/access", array("name" => "access_id", "value" => $access_id));
	
	// restore context
	elgg_set_context($context);
	
	if(!empty($folder))
	{
		$form_data .= "<div id='file_tools_edit_form_access_extra'>";
		$form_data .= "<div>" . elgg_view("input/checkboxes", array("options" => array(elgg_echo("file_tools:forms:edit:change_children_access") => "yes"), "value" => "yes", "name" => "change_children_access")) . "</div>";
		$form_data .= "<div>" . elgg_view("input/checkboxes", array("options" => array(elgg_echo("file_tools:forms:edit:change_files_access") => "yes"), "name" => "change_files_access")) . "</div>";
		$form_data .= "</div>"; 
	}
	
	
	$form_data .= "<div>";
	$form_data .= elgg_view("input/submit", array("value" => $submit_text));
	$form_data .= "</div>";
	
	$form = elgg_view("input/form", array("body" => $form_data,
											"action" => $vars["url"] . "action/file_tools/folder/edit",
											"id" => "file_tools_edit_form"));
?>
<div class="contentWrapper">
	<?php echo $form; ?>
</div>