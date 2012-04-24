<?php
	$page_owner = $vars["page_owner_entity"];
	
	echo elgg_view('input/securitytoken');
	
	$form_body .= elgg_echo("file_tools:upload:form:zip:info").'<br /><br />';
	$form_body .= '<label>'.elgg_echo("file_tools:upload:form:choose").'</label><br />';
	$form_body .= elgg_view("input/file",array('name' => 'zip_file')).'<br />';
	
	$folders = file_tools_get_folders(page_owner_entity()->guid);
		if(get_plugin_setting("user_folder_structure", "file_tools") == "yes"){
		$form_body .= '<label>'.elgg_echo("file_tools:forms:edit:parent") . '</label>	<br />';
		$form_body .= elgg_view("input/folder_select", array("name" => "parent_guid", "value" => $tags, "id" => "file_tools_file_parent_guid")) . '<br />';
	}
	$form_body .= '<label>' . elgg_echo('access') . '</label><br />';
	$form_body .= elgg_view('input/access', array('name' => 'access_id', 'id' => 'file_tools_file_access_id')) . '<br />';
	
	$action = $vars['url'] . 'action/file_tools/import/zip';

	$form_body .= elgg_view('input/hidden', array('name' => 'container_guid', 'value' => elgg_get_page_owner_guid()));
	
	$form_body .= elgg_view('input/submit', array('value' => elgg_echo('upload')));
	
	
	
	
	$form = elgg_view('input/form', array(	'id' 	=> 'file_tools', 
											'name' 	=> 'file_tools', 
											'action' 		=> $action, 
											'enctype' 		=> 'multipart/form-data', 
											'body' 			=> $form_body));
	
	echo $form;