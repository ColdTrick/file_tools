<?php 

	$plugin = $vars["entity"];
	
	$options = array(
		"date" => elgg_echo("file_tools:usersettings:time:date"),
		"days" => elgg_echo("file_tools:usersettings:time:days")
	);
	
	echo "<div>";
	echo elgg_echo("file_tools:usersettings:time");
	echo "&nbsp;" . elgg_view("input/pulldown", array("internalname" => "params[file_tools_time_display]", "options_values" => $options, "value" => $plugin->file_tools_time_display));
	echo "</div>";
