<?php

	global $CONFIG;
	
	$user_guid = get_loggedin_userid();
	
	$zip_dir = $CONFIG->dataroot . 'file_tools/zip_temp/';
	
	if(!file_exists($zip_dir))
	{
		mkdir($zip_dir, 0777, true);
	}
	
	$zip_filename = $zip_dir . $user_guid . '_' . time() . '.zip';
	
	if($get_guids = get_input('guids'))
	{
		$guids = explode('-', $get_guids);
		$zip = new ZipArchive();
		
		if($zip->open($zip_filename, ZIPARCHIVE::CREATE) !== true)
		{
		    register_error("cannot open <$zip_filename>\n");
		    forward(REFERER);
		}
		
		foreach($guids as $guid)
		{
			if($entity = get_entity($guid))
			{
				$entity_subtype = $entity->getSubtype();
				
				if($entity_subtype == 'file')
				{
					if($zip->statName($entity->originalfilename) === false){
						$zip->addFile($entity->getFilenameOnFilestore(), $entity->originalfilename);
					} else {
						$ext_pos = strrpos($entity->originalfilename, ".");
						$file_name = substr($entity->originalfilename, 0, $ext_pos) . "_" . $entity->getGUID() . substr($entity->originalfilename, $ext_pos);
						$zip->addFile($entity->getFilenameOnFilestore(), $file_name);
					}
				}
				elseif($entity_subtype == 'folder')
				{
					$zip->addEmptyDir($entity->title);
					if($main_files = file_tools_has_files($entity->getGUID()))
					{
						foreach($main_files as $guid)
						{
							if($file = get_entity($guid))
							{
								if($zip->statName( $entity->title . '/' . $file->originalfilename) === false){
									$zip->addFile($file->getFilenameOnFilestore(), $entity->title . '/' . $file->originalfilename);
								} else {
									$ext_pos = strrpos($file->originalfilename, ".");
									$file_name = substr($file->originalfilename, 0, $ext_pos) . "_" . $file->getGUID() . substr($file->originalfilename, $ext_pos);
									$zip->addFile($file->getFilenameOnFilestore(), $entity->title . '/' . $file_name);
								}
							}
						}
					}
	
					foreach(file_tools_get_zip_structure($entity, $entity->title) as $directory)
					{
						$zip->addEmptyDir($directory['directory']);
						
						if($directory['files'])
						{
							foreach($directory['files'] as $guid)
							{
								if($entity = get_entity($guid))
								{
									if($zip->statName( $directory['directory'] . '/' . $entity->originalfilename) === false){
										$zip->addFile($entity->getFilenameOnFilestore(), $directory['directory'] . '/' . $entity->originalfilename);
									} else {
										$ext_pos = strrpos($entity->originalfilename, ".");
										$file_name = substr($entity->originalfilename, 0, $ext_pos) . "_" . $entity->getGUID() . substr($entity->originalfilename, $ext_pos);
										$zip->addFile($entity->getFilenameOnFilestore(), $directory['directory'] . '/' . $file_name);
									}
								}
							}
						}
					}
				}
			}
		}
		
		$zip->close();
		
		if(file_exists($zip_filename))
		{
			header('Pragma: public');
			header('Content-type: application/zip');
			header('Content-Disposition: attachment; filename="' . $user_guid . '_files.zip"');
			header('Content-Length: ' . filesize($zip_filename));
			
			ob_clean();
			flush();
			readfile($zip_filename);
			
			unlink($zip_filename);
		}
		else
		{
		    register_error("cannot find zip file");
		    forward(REFERER);
		}
	}
	
	exit;