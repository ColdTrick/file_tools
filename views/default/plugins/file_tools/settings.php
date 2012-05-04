<?php 
	$settings = $vars["entity"];
	
	// Allowed extensions
	echo elgg_echo('file_tools:settings:allowed_extensions');
	
	if(!empty($settings->allowed_extensions))	{
		$value = $settings->allowed_extensions;
	} else {
		$value = 'txt,jpg,jpeg,png,bmp,gif,pdf,doc,docx,xls,xlsx,pptx,odt,ods,odp';
	}
	
	echo elgg_view('input/text', array('name' => 'params[allowed_extensions]', 'value' => $value)).'<br />';
	
	// Use folder structure
	$options = array('no' => elgg_echo("option:no"), 'yes' => elgg_echo("option:yes"));
	
	?>
	<div>
		<?php echo elgg_echo("file_tools:settings:user_folder_structure"); ?>
	</div>
	<?php 
		echo elgg_view('input/dropdown', array('name' => 'params[user_folder_structure]"', 'value' => $settings->user_folder_structure, 'options_values' => $options));
	?>
	<div>
		<?php echo elgg_echo("file_tools:usersettings:time:default"); ?>
	</div>
	<?php
	 
		// Default time view
		$options = array("date" => elgg_echo("file_tools:usersettings:time:date"), "days" => elgg_echo("file_tools:usersettings:time:days"));
	
		echo elgg_view("input/dropdown", array("name" => "params[file_tools_default_time_display]", "options_values" => $options, "value" => $settings->file_tools_default_time_display));
		
	?>
	<div>
		<?php echo elgg_echo("file_tools:settings:sort:default"); ?>
	</div>
	<?php
	echo elgg_view('input/dropdown', array('name' => 'params[sort]',
											'value' =>  $settings->sort,
											'options_values' => array(
																'e.time_created' 	=> elgg_echo('file_tools:list:sort:time_created'), 
																'oe.title' 			=> elgg_echo('title'), 
																'oe.description'	=> elgg_echo('description'), 
																'simpletype' 		=> elgg_echo('file_tools:list:sort:type'))));
	echo "<br />";
	echo elgg_view('input/dropdown', array('name' => 'params[sort_direction]',
											'value' =>  $settings->sort_direction,
											'options_values' => array(
																'asc' 	=> elgg_echo('file_tools:list:sort:asc'), 
																'desc'	=> elgg_echo('file_tools:list:sort:desc')))); 
	