<?php
	$plugin = $vars["entity"];
	
	$options = array(
		"date" => elgg_echo("file_tools:usersettings:time:date"),
		"days" => elgg_echo("file_tools:usersettings:time:days")
	);
?>
<div>
	<?php echo elgg_echo("file_tools:usersettings:time");?> 
	<?php
	echo echo elgg_echo("file_tools:usersettings:time:description")
	if(empty($plugin->file_tools_time_display))
	{
		 $file_tools_time_display_value = get_plugin_setting("file_tools_default_time_display", "file_tools");
	}
	else
	{
		$file_tools_time_display_value = $plugin->file_tools_time_display;
	}
	
	echo elgg_view("input/pulldown", array("internalname" => "params[file_tools_time_display]", "options_values" => $options, "value" => $file_tools_time_display_value));?>
</div>