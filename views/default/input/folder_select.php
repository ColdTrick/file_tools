<?php 

	$folder_guid = get_input('folder_guid');

	$owner_guid = $vars["owner_guid"];

	$name 		= $vars["internalname"];
	$id 		= $vars["internalid"];
	$js 		= $vars["js"];
	$class 		= $vars["class"];
	
	$value 		= $vars["value"];
	
	$type 		= $vars['type'];
	
	if($type == 'folder')
	{
		if(!$value)
		{
			if($folder_guid)
			{
				$value = get_entity($folder_guid)->parent_guid;
			}
		}
	}
	else
	{
		$value = $folder_guid;
	}
	
	if(empty($owner_guid))
	{
		$owner_guid = page_owner();
	}

	$folders = file_tools_get_folders($owner_guid);

	$options = "<option value='0'>" . elgg_echo("file_tools:input:folder_select:main") . "</option>\n";
	
	if(!empty($folders))
	{
		$options .= file_tools_build_select_options($folders, $value, 0, $folder_guid);
	}

?>
<select name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="<?php echo $class; ?>" <?php echo $js; ?>>
	<?php echo $options; ?>
</select>