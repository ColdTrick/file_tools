<?php
	$page_owner = $vars["page_owner_entity"];
	
	echo elgg_view('input/securitytoken');
	
	$form_body .= '<label>'.elgg_echo("file_tools:upload:form:choose").'</label><br />';
	$form_body .= elgg_view("input/file",array('internalname' => 'zip_file')).'<br />';
	
	
	$folders = file_tools_get_folders(page_owner_entity()->guid);
	
	$form_body .= '<label>'.elgg_echo("file_tools:forms:edit:parent") . '</label>	<br />';
	$form_body .= elgg_view("input/folder_select", array("internalname" => "parent_guid", "value" => $tags, "internalid" => "file_tools_file_parent_guid")) . '<br />';
	
	$form_body .= '<label>' . elgg_echo('access') . '</label><br />';
	$form_body .= elgg_view('input/access', array('internalname' => 'access_id', 'internalid' => 'file_tools_file_access_id')) . '<br />';
	
	$action = $vars['url'] . 'action/file_tools/import/zip';

	$form_body .= elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => page_owner_entity()->guid));
	
	$form_body .= elgg_view('input/submit', array('value' => elgg_echo('upload')));
	
	
	
	
	$form = elgg_view('input/form', array(	'internalid' 	=> 'file_tools', 
											'internalname' 	=> 'file_tools', 
											'action' 		=> $action, 
											'enctype' 		=> 'multipart/form-data', 
											'body' 			=> $form_body));
	
	echo elgg_view('page_elements/contentwrapper', array('body' => $form));