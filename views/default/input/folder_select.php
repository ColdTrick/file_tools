<?php 

	$folder_guid = (int)get_input('folder_guid');

	$owner_guid = $vars["owner_guid"];

	$name 		= $vars["name"];
	$id 		= $vars["id"];
	$js 		= $vars["js"];
	$class 		= $vars["class"];
	
	$value 		= $vars["value"];
	$current_folder	= $vars["folder"];
	
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
		$folder_guid = null;
	}
	
	if(empty($owner_guid))
	{
		$owner_guid = elgg_get_page_owner_guid();
	}

	$folders = file_tools_get_folders($owner_guid);

	$options = "<option value='0'>" . elgg_echo("file_tools:input:folder_select:main") . "</option>\n";
	
	if(!empty($folders))
	{
		$current_folder_guid = false;
		if($vars["folder"]){
			$current_folder_guid = $vars["folder"]->getGUID();
		}
		$options .= file_tools_build_select_options($folders, $value, 1, $current_folder_guid);
	}
	
?>
<select name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="<?php echo $class; ?>" <?php echo $js; ?>>
	<?php echo $options; ?>
</select>