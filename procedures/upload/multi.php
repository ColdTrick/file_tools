<?php

	$session_id = $_POST['PHPSESSID'];
	
	session_id($session_id);
	
	require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/engine/start.php');

	$tags 		= get_input('tags');
	$access_id 	= (int) get_input("access_id");
	$folder_guid = (int) str_replace('file_tools_tree_element_','',get_input('folder_guid', 0));
	
	$container_guid = (int) get_input('container_guid', 0);
	if ($container_guid == 0)
	{
		$container_guid = elgg_get_logged_in_user_guid();
	}
	
	$tags = explode(',', $tags);
	
	foreach($_FILES as $uploaded_file)
	{		
		$file = new FilePluginFile();
		$file->subtype 			= "file";
		$file->title 			= $uploaded_file['name'];
		$file->description 		= $uploaded_file['name'];
		$file->access_id 		= $access_id;
		$file->container_guid 	= $container_guid;
		$file->owner_guid 		= elgg_get_logged_in_user_guid();
		$file->tags 			= $tags;
		$file->folder_guid 		= $folder_guid;
		
		$prefix = "file/";
		
		$filestorename = elgg_strtolower(time() . $uploaded_file['name']);
				
		$file->setFilename($prefix . $filestorename);
		$file->setMimeType(mime_content_type($uploaded_file['tmp_name']));
		$file->originalfilename = $uploaded_file['name'];
		$file->simpletype = get_general_file_type(mime_content_type($uploaded_file['tmp_name']));
		
		$file->open("write");
		$file->close();
		
		move_uploaded_file($uploaded_file['tmp_name'], $file->getFilenameOnFilestore());
		
		$guid = $file->save();
		
		if($guid && $file->simpletype == "image")
		{
			$thumbnail = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 60, 60, true);
			if ($thumbnail)
			{
				$thumb = new ElggFile();
				$thumb->setMimeType($uploaded_file['type']);
				
				$thumb->setFilename($prefix . "thumb" . $filestorename);
				$thumb->open("write");
				$thumb->write($thumbnail);
				$thumb->close();
				
				$file->thumbnail = $prefix . "thumb" . $filestorename;
				unset($thumbnail);
			}
			
			$thumbsmall = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 153, 153, true);
			if ($thumbsmall)
			{
				$thumb->setFilename($prefix . "smallthumb" . $filestorename);
				$thumb->open("write");
				$thumb->write($thumbsmall);
				$thumb->close();
				$file->smallthumb = $prefix . "smallthumb" . $filestorename;
				unset($thumbsmall);
			}
			
			$thumblarge = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 600, 600, false);
			if ($thumblarge)
			{
				$thumb->setFilename($prefix . "largethumb" . $filestorename);
				$thumb->open("write");
				$thumb->write($thumblarge);
				$thumb->close();
				$file->largethumb = $prefix . "largethumb" . $filestorename;
				unset($thumblarge);
			}
		}
	}
	
	exit();