<?php

$group = $vars["entity"];
if(!empty($group)){
	$form_body = "<h3 class='settings'>" . elgg_echo("file_tools:settings:sort:default") . "</h3>";
	
	$form_body .= elgg_view("input/hidden", array("name" => "guid", "value" => $group->getGUID()));
	
	
	
	$sort_value = 'e.time_created';
	if($group->file_tools_sort){
		$sort_value = $group->file_tools_sort;
	} else {
		if($site_sort_default = elgg_get_plugin_setting("sort", "file_tools")){
			$sort_value = $site_sort_default;
		}
	}
	
	$form_body .= elgg_view('input/dropdown', array('name' => 'sort',
												'value' =>  $sort_value,
												'options_values' => array(
																	'e.time_created' 	=> elgg_echo('file_tools:list:sort:time_created'), 
																	'oe.title' 			=> elgg_echo('title'), 
																	'oe.description'	=> elgg_echo('description'), 
																	'simpletype' 		=> elgg_echo('file_tools:list:sort:type'))));
	$form_body .= "<br />";
	
	$sort_direction_value = 'asc';
	if($group->file_tools_sort_direction){
		$sort_direction_value = $group->file_tools_sort_direction;
	} else {
		if($site_direction_sort_default = elgg_get_plugin_setting("sort_direction", "file_tools")){
			$sort_direction_value = $site_direction_sort_default;
		}
	}
	
	$form_body .= elgg_view('input/dropdown', array('name' => 'sort_direction',
												'value' =>  $sort_direction_value,
												'options_values' => array(
																	'asc' 	=> elgg_echo('file_tools:list:sort:asc'), 
																	'desc'	=> elgg_echo('file_tools:list:sort:desc')))); 
	$form_body .= "<br />";
	$form_body .= elgg_view("input/submit", array("value" => elgg_echo("save")));
	
	$body = elgg_view("input/form", array("action" => $vars["url"] . "action/file_tools/groups/save_sort", "body" => $form_body));
	echo $body;
}