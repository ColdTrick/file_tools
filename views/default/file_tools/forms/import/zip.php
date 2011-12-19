<?php
	echo elgg_view('input/securitytoken');
	
	$form_body .= '<label>'.elgg_echo("file_tools:upload:form:choose").'</label><br />';
	$form_body .= elgg_view("input/file",array('internalname' => 'zip_file')).'<br />';
	
	
	$folders = file_tools_get_folders(page_owner_entity()->guid);
	$options = file_tools_build_select_options($folders, get_input('folder_guid'));
	
	$form_body .= '<select name="file_tools_parent_guid">' . $options . '</select><br />';
	
	$action = $vars['url'].'action/file_tools/import/zip';

	$form_body .= elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => page_owner_entity()->guid));
	
	$form_body .= elgg_view('input/submit', array('value' => elgg_echo('upload')));
	
	
	
	
	$form = elgg_view('input/form', array(	'internalid' 	=> 'file_tools', 
											'internalname' 	=> 'file_tools', 
											'action' 		=> $action, 
											'enctype' 		=> 'multipart/form-data', 
											'body' 			=> $form_body));
	
	echo elgg_view('page_elements/contentwrapper', array('body' => $form));
