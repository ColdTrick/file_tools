<?php

	$page_owner = get_input('page_owner', get_loggedin_userid());
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
			
			if(!unzip($file, $parent_guid, $container_guid))
			{
				register_error(elgg_echo('file_tools:error:nofilesextracted'));
			}
			else
			{
				system_message(elgg_echo('file_tools:error:fileuploadsuccess'));
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
	
	forward('pg/file/owner/' . get_entity($page_owner)->username . '#' . $parent_guid);