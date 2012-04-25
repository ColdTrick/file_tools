<?php 

	$action = get_input('hide');
	$file_guid = get_input('guid');
	
	if(($file = get_entity($file_guid)) && ($file->getSubtype() == 'file'))
	{
		if($file->canEdit())
		{
			if($action == 'show')
			{
				$file->show_in_widget = time();
			}
			elseif($action == 'hide')
			{
				unset($file->show_in_widget);
			}
			
			$file->save();
		}
	}
	
	$options = array(
		"type" => "object",
		"subtype" => FILE_TOOLS_SUBTYPE,
		"container_guid" => $file->getOwner(),
		"limit" => false,
		"relationship" => FILE_TOOLS_RELATIONSHIP,
		"relationship_guid" => $file->getGUID(),
		"inverse_relationship" => true
	);
	
	if(stristr($_SERVER["HTTP_REFERER"], "file")){
	
		$folders = elgg_get_entities_from_relationship($options);
		if(!empty($folders)){
			$folder = $folders[0];
			forward($folder->getURL());
		}
	
	}
	forward(REFERER);