<?php

	$page_owner = get_input('page_owner', elgg_get_logged_in_user_guid());
	$container_guid = (int)get_input('container_guid', $page_owner);

	set_time_limit(0);

	$allowed_extensions = file_tools_allowed_extensions();
	
	$parent_guid = (int)get_input('parent_guid');
	
	if (isset($_FILES['zip_file']) && !empty($_FILES['zip_file']['name'])) 
	{
		$extracted = false;
		
		$extension_array = explode('.', $_FILES['zip_file']['name']);	
		
		if(end($extension_array) == 'zip')
		{		
			$prefix = "file/";
			$file = $_FILES['zip_file'];
			
			if(!file_tools_unzip($file, $parent_guid, $container_guid))
			{
				register_error(elgg_echo('file_tools:error:nofilesextracted'));
			}
			else
			{
				system_message(elgg_echo('file_tools:error:fileuploadsuccess'));
				forward('file/owner/' . get_entity($container_guid)->username . '#' . $parent_guid);
			}
		}
		else
		{
			register_error(elgg_echo('file_tools:error:nozipfilefound'));
		}
	}
	else
	{
		register_error(elgg_echo('file_tools:error:nofilefound'));
	}
	
	forward(REFERER);