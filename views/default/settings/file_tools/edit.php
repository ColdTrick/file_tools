<?php 
	global $CONFIG;
	
	echo elgg_echo('file_tools:settings:allowed_extensions');
	
	if(!empty($vars['entity']->allowed_extensions))
	{
		$value = $vars['entity']->allowed_extensions;
	}
	else
	{
		$value = 'txt,jpg,jpeg,png,bmp,gif,pdf,doc,docx,xls,xlsx,pptx';
	}
	
	echo elgg_view('input/text', array('internalname' => 'params[allowed_extensions]', 'value' => $value)).'<br />';

	$settings = $vars["entity"];

	if($settings->replace_file != "yes")
	{
		$options = "<option value='no' selected='selected'>" . elgg_echo("option:no") . "</option>\n";
		$options .= "<option value='yes'>" . elgg_echo("option:yes") . "</option>\n";
	}
	else 
	{
		$options = "<option value='no'>" . elgg_echo("option:no") . "</option>\n";
		$options .= "<option value='yes' selected='selected'>" . elgg_echo("option:yes") . "</option>\n";
	}
	?>
	<div>
		<div><?php echo elgg_echo("file_tools:settings:replace_file"); ?></div>
		<select name="params[replace_file]">
			<?php echo $options; ?>
		</select>
		
	</div>