<?php

	//global $DB_QUERY_CACHE;
	global $ENTITY_CACHE;
	global $DB_PROFILE;
	
	//$db_query_cache_backup = $DB_QUERY_CACHE;
	$entity_cache_backup = $ENTITY_CACHE;
	
	set_time_limit(0);

	$allowed_extensions = file_tools_allowed_extensions();
	
	$container_guid = get_input('container_guid', get_loggedin_userid());
	
	$container_entity = get_entity($container_guid); 
	
	if($container_entity instanceof ElggUser)
	{
		$access_id = get_default_access();
	}
	elseif($container_entity instanceof ElggGroup)
	{
		$access_id = $container_entity->group_acl;
	}
	else
	{
		forward(REFERER);
	}
	
	if (isset($_FILES['zip_file']) && !empty($_FILES['zip_file']['name'])) 
	{
		$extracted = false;
		
		$extension_array = explode('.', $_FILES['zip_file']['name']);	
		
		if(end($extension_array) == 'zip')
		{		
			$prefix = "file/";
			$file = $_FILES['zip_file']['tmp_name'];
			
			if($zip = zip_open($file))
			{
				if (isset($CONFIG->register_objects['object']['file']))
				{
					$descr = $CONFIG->register_objects['object']['file'];
					unset($CONFIG->register_objects['object']['file']);
				}

				$zip_object = new UploadedZip();
					$zip_object->title = sanitise_string($_FILES['zip_file']['name']);
					$zip_object->description = 'Uploaded Zip';
									
					$zip_object->container_guid = $container_guid;								
					$zip_object->access_id 		= $access_id;
					
					$zip_object->save();
					
				while($zip_entry = zip_read($zip)) 
				{
					//if(zip_entry_filesize($zip_entry)>0)
					{
						$name_array = explode('/', zip_entry_name($zip_entry));						
						$extension_array = explode('.', end($name_array));
						
						$file_name 					= sanitise_string(end($name_array));
						$file_extension				= end($extension_array);
						$file_size 					= zip_entry_filesize($zip_entry);
						$file_size_compressed 		= zip_entry_compressedsize($zip_entry);
						$file_compression_method 	= zip_entry_compressionmethod($zip_entry);
		
						if(zip_entry_open($zip, $zip_entry, "r")) 
						{
							if(in_array(strtolower($file_extension), $allowed_extensions))
							{
								$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
								$filestorename = elgg_strtolower(time().$file_name);
								
								$filehandler = new ElggFile();
									$filehandler->setFilename($prefix . $filestorename);

									$filehandler->title 			= $file_name;
									$filehandler->originalfilename 	= $file_name;
									
									$filehandler->container_guid 	= $container_guid;
									$filehandler->owner_guid		= get_loggedin_userid();
									$filehandler->access_id 		= $access_id;
	
									$filehandler->open("write");
									$filehandler->write($buf);
								
									$mime_type = mime_content_type($filehandler->getFilenameOnFilestore());
									$simple_type = explode('/', $mime_type);
									
									$filehandler->setMimeType($mime_type);
									$filehandler->simpletype = $simple_type[0];
									
									$file_guid = $filehandler->save();
									
									if ($file_guid && $simple_type[0] == "image") 
									{
										$thumbnail = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),60,60, true);
										if ($thumbnail) 
										{
											$thumb = new ElggFile();
											$thumb->setMimeType($mime_type);
											
											$thumb->setFilename($prefix."thumb".$filestorename);
											$thumb->open("write");
											$thumb->write($thumbnail);
											$thumb->close();
											
											$filehandler->thumbnail = $prefix."thumb".$filestorename;
											unset($thumbnail);
										}
										
										$thumbsmall = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),153,153, true);
										if ($thumbsmall) 
										{
											$thumb->setFilename($prefix."smallthumb".$filestorename);
											$thumb->open("write");
											$thumb->write($thumbsmall);
											$thumb->close();
											$filehandler->smallthumb = $prefix."smallthumb".$filestorename;
											unset($thumbsmall);
										}
										
										$thumblarge = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),600,600, false);
										if ($thumblarge) 
										{
											$thumb->setFilename($prefix."largethumb".$filestorename);
											$thumb->open("write");
											$thumb->write($thumblarge);
											$thumb->close();
											$filehandler->largethumb = $prefix."largethumb".$filestorename;
											unset($thumblarge);
										}
									}
									
									
								
								$filehandler->close();
								
								invalidate_cache_for_entity($filehandler->getGUID());
								//$DB_QUERY_CACHE = $db_query_cache_backup;
								$ENTITY_CACHE = $entity_cache_backup;
								$DB_PROFILE = null; 
								
								zip_entry_close($zip_entry);
								
								$zip_object->addRelationship($filehandler->getGUID(), 'file_tools_uploaded_zip_file');
								
								$_SESSION['extracted_files'][] = $file_name;
								$extracted = true;
							}
						}
					}
				}
			
				zip_close($zip);
				
				if(isset($descr))
				{
					$CONFIG->register_objects['object']['file'] = $descr;
				}
				
				if($extracted)
				{
					system_message(elgg_echo('file_tools:error:fileuploadsuccess'));
				}
				else
				{
					register_error(elgg_echo('file_tools:error:nofilesextracted'));
				}
			}
			else
			{
				register_error(elgg_echo('file_tools:error:cantopenfile'));
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