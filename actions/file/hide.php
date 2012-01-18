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
				$file->show_in_widget = 0;
			}
			
			$file->save();
		}
	}
	
	forward(REFERER);