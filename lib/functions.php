<?php

	function file_tools_get_file_extension($file)
	{
		$result = '';
		if($file->getSubtype() == 'file')
		{
			if($filename = $file->getFilename())
			{
				$exploded_filename = explode('.', $filename);
				
				$result = end($exploded_filename);
			}
		}
		
		return strtolower($result);
	}

	function file_tools_get_widget_files($owner)
	{
		$options = array(
			"type" => "object",
			"subtype" => 'file',
			"owner_guid" => $owner,
			"limit" => false,
			"metadata_name_value_pairs" => array('name' => 'show_in_widget', 'value' => '0', 'operand' => '>')
		);
		
		if($entities = elgg_get_entities_from_metadata($options))
		{
			return $entities;
		}
		
		return false;
	}
	
	function file_tools_get_zip_structure($folder, $prepend)
	{
		global $CONFIG;
		$entries = array();
		
		if($prepend){
			$prepend .= '/';
		}
		
		if(!$folder)
		{
			$container_guid = page_owner();
			$parent_guid = 0;
		}
		else
		{
			$container_guid = $folder->getContainer();
			$parent_guid = $folder->getGUID();
		}
		
		$options = array(
			"type" => "object",
			"subtype" => FILE_TOOLS_SUBTYPE,
			//"container_guid" => $container_guid,
			"limit" => false,
			"metadata_name" => "parent_guid",
			"metadata_value" => $parent_guid,
		);
		
		// voor de hoogste map de sub bestanden nog ophalen		
		if($entities = elgg_get_entities_from_metadata($options))
		{
			foreach($entities as $subfolder)
			{
				$title = $prepend . $subfolder->title;
				$entries[] = array('directory' => $title, 'files' => file_tools_has_files($subfolder->getGUID()));
				
				$entries = array_merge($entries, file_tools_get_zip_structure($subfolder, $title));
			}
		}
		
		return $entries;
	}
	
	function file_tools_has_files($folder)
	{
		$files_options = array(
			"type" => "object",
			"subtype" => "file",
			"limit" => false,
			//"container_guid" => get_loggedin_userid(),
			"relationship" => FILE_TOOLS_RELATIONSHIP,
			"relationship_guid" => $folder,
			"inverse_relationship" => false			
		);
		
		$file_guids = array();
		
		if($files = elgg_get_entities_from_relationship($files_options))
		{
			foreach($files as $file)
			{
				$file_guids[] = $file->getGUID();
			}
			
			return $file_guids;
		}
		
		return false;
	}

	function file_tools_get_folders($container_guid = 0)
	{
		$result = false;
		
		if(empty($container_guid))
		{
			$container_guid = page_owner();
		}
		
		if(!empty($container_guid))
		{
			$options = array(
				"type" => "object",
				"subtype" => FILE_TOOLS_SUBTYPE,
				"container_guid" => $container_guid,
				"limit" => false
			);

			if($folders = elgg_get_entities($options))
			{
				$parents = array();

				foreach($folders as $folder)
				{
					$parent_guid = $folder->parent_guid; 
					
					if(!empty($parent_guid))
					{
						if($temp = get_entity($parent_guid))
						{
							if($temp->getSubtype() != FILE_TOOLS_SUBTYPE)
							{
								$parent_guid = 0;
							}
						}
						else
						{
							$parent_guid = 0;
						}
					}
					else
					{
						$parent_guid = 0;
					}
					
					if(!array_key_exists($parent_guid, $parents))
					{
						$parents[$parent_guid] = array();
					}
					
					$parents[$parent_guid][] = $folder;
				}
				
				$result = file_tools_sort_folders($parents, 0);				
			}
		}
		return $result;
	}
	
	function file_tools_build_select_options($folder, $selected = 0, $niveau = 0, $skip = false)
	{
		$result = "";
		
		if(is_array($folder) && !array_key_exists("children", $folder))
		{
			foreach($folder as $folder_item)
			{
				$result .= file_tools_build_select_options($folder_item, $selected, $niveau, $skip);
			}
		}
		else
		{
			$folder_item = $folder["folder"];
			
			if($skip != $folder_item->getGUID())
			{
				if($selected == $folder_item->getGUID())
				{
					$result .= "<option value='" . $folder_item->getGUID() . "' selected='selected'>" . str_repeat("-", $niveau) . " " .  $folder_item->title . "</option>";
				}
				else
				{
					$result .= "<option value='" . $folder_item->getGUID() . "'>" . str_repeat("-", $niveau) . " " .  $folder_item->title . "</option>";
				}
				
				if(!empty($folder["children"]))
				{
					$result .= file_tools_build_select_options($folder["children"], $selected, $niveau + 1, $skip);
				}
			}
		}

		return $result;
	}
	
	function file_tools_build_widget_options($folder, $internalname = "", $selected = array())
	{
		$result = "";
		
		if(is_array($folder) && !array_key_exists("children", $folder))
		{
			foreach($folder as $folder_item)
			{
				$result .= "<ul>";
				$result .= file_tools_build_widget_options($folder_item, $internalname, $selected);
				$result .= "</ul>";
			}
		}
		else
		{
			$folder_item = $folder["folder"];
			
			$result .= "<li>";
			if(in_array($folder_item->getGUID(), $selected))
			{
				$result .= "<input type='checkbox' name='" . $internalname . "' value='" . $folder_item->getGUID() . "' checked='checked'> " .  $folder_item->title;
			}
			else
			{
				$result .= "<input type='checkbox' name='" . $internalname . "' value='" . $folder_item->getGUID() . "'> " .  $folder_item->title;
			}
			
			if(!empty($folder["children"]))
			{
				$result .= file_tools_build_widget_options($folder["children"], $internalname, $selected);
			}
			$result .= "</li>";
		}
		
		return $result;
	}
	
	function file_tools_sort_folders($folders, $parent_guid = 0)
	{		
		$result = false;
		
		if(array_key_exists($parent_guid, $folders))
		{
			$result = array();
			
			foreach($folders[$parent_guid] as $subfolder)
			{
				$children = file_tools_sort_folders($folders, $subfolder->getGUID());
				
				$order = $subfolder->order;
				if(empty($order))
				{
					$order = 0;
				}
				
				while(array_key_exists($order, $result))
				{
					$order++;
				}
				
				$result[$order] = array(
					"folder" => $subfolder,
					"children" => $children
				);
			}
			
			ksort($result);
		}
		
		return $result;
	}
	
	function file_tools_get_sub_folders($folder = false)
	{
		global $CONFIG;
		$result = false;
		
		if(!$folder)
		{
			$container_guid = page_owner();
			$parent_guid = 0;
		}
		else
		{
			$container_guid = $folder->getContainer();
			$parent_guid = $folder->getGUID();
		}
		
		$options = array(
			"type" => "object",
			"subtype" => FILE_TOOLS_SUBTYPE,
			"container_guid" => $container_guid,
			"limit" => false,
			"metadata_name" => "parent_guid",
			"metadata_value" => $parent_guid,
			"order_by_metadata" => array('name' => 'order', 'direction' => 'ASC')
		);
		
		if($folders = elgg_list_entities_from_metadata($options))
		{
			$result = $folders;		
		}
		
		return $result;
	}
	
	function file_tools_display_folders($folder)
	{
		$result = "";
		
		if(is_array($folder) && !array_key_exists("children", $folder))
		{
			$result .= "<ul>";
			foreach($folder as $folder_item)
			{
				$result .= file_tools_display_folders($folder_item);
			}
			$result .= "</ul>";
		}
		elseif(!empty($folder["children"]))
		{
			
			$result .= "<li><a id='file_tools_tree_element_" . $folder["folder"]->getGUID() . "' title='" . $folder["folder"]->title . "' href='javascript:void(0);'>" . $folder["folder"]->title . "</a>";
			$result .= file_tools_display_folders($folder["children"]);
			$result .= "</li>";
		}
		elseif(array_key_exists("folder", $folder))
		{
			$folder = $folder["folder"];
			$result .= "<li><a id='file_tools_tree_element_" . $folder->getGUID() . "' title='" . $folder->title . "' href='javascript:void(0);'>" . $folder->title . "</a></li>";
		}
		
		return $result;
	}
	
	function file_tools_change_children_access($folder, $change_files = false)
	{
		
		if(!empty($folder) && ($folder instanceof ElggObject))
		{
			if($folder->getSubtype() == FILE_TOOLS_SUBTYPE)
			{
				// get children folders
				$options = array(
					"type" => "object",
					"subtype" => FILE_TOOLS_SUBTYPE,
					"container_guid" => $folder->getContainer(),
					"limit" => false,
					"metadata_name" => "parent_guid",
					"metadata_value" => $folder->getGUID()
				);
				
				if($children = elgg_get_entities_from_metadata($options))
				{
					foreach($children as $child)
					{
						$child->access_id = $folder->access_id;
						$child->save();
						
						file_tools_change_children_access($child, $change_files);
					}
				}
				
				if($change_files)
				{
					// change access on files in this folder
					file_tools_change_files_access($folder);
				}
			}
		}
	}
	
	function file_tools_change_files_access($folder)
	{
		if(!empty($folder) && ($folder instanceof ElggObject))
		{
			if($folder->getSubtype() == FILE_TOOLS_SUBTYPE)
			{
				// change access on files in this folder
				$options = array(
					"type" => "object",
					"subtype" => "file",
					"container_guid" => $folder->getContainer(),
					"limit" => false,
					"relationship" => FILE_TOOLS_RELATIONSHIP,
					"relationship_guid" => $folder->getGUID()
				);
				
				if($files = elgg_get_entities_from_relationship($options))
				{
					foreach($files as $file)
					{
						$file->access_id = $folder->access_id;
						$file->save();
					}
				}
			}
		}	
	}
	
	function file_tools_replace_page_handler($handler, $function)
	{
		global $CONFIG;
		
		if(!empty($CONFIG->pagehandler))
		{
			if(array_key_exists($handler, $CONFIG->pagehandler))
			{
				if(!isset($CONFIG->backup_pagehandler))
				{
					$CONFIG->backup_pagehandler = array();
				}
				
				$CONFIG->backup_pagehandler[$handler] = $CONFIG->pagehandler[$handler];
			}
		}
		
		return register_page_handler($handler, $function);
	}
	
	function file_tools_fallback_page_handler($page, $handler)
	{
		global $CONFIG;
		
		$result = false;
		
		if(!empty($CONFIG->backup_pagehandler))
		{
			if(array_key_exists($handler, $CONFIG->backup_pagehandler))
			{
				$function = $CONFIG->backup_pagehandler[$handler];
				
				if(is_callable($function))
				{
					$result = $function($page, $handler);
				}
			}
		}
		
		return $result;
	}

	/*
	 * 
	 */
	
	function file_tools_allowed_extensions($zip = false)
	{
		$result = false;
		
		$allowed_extensions_settings = trim(get_plugin_setting('allowed_extensions', 'file_tools'));
		
		if(!empty($allowed_extensions_settings))
		{
			$allowed_extensions_settings = strtolower($allowed_extensions_settings);
			$allowed_extensions = explode(',', $allowed_extensions_settings);
			array_walk($allowed_extensions, 'file_tools_trim_array_values');
			
			$result = $allowed_extensions;	
		}
		else
		{
			$result = array('txt','jpg','jpeg','png','bmp','gif','pdf','doc','docx','xls','xlsx','pptx');
		}
		
		if(!$zip)
		{
			return $result;
		}
		
		$result = implode(';*.', $result);
		
		return '*.'.$result;
	}
	
	function file_tools_trim_array_values(&$value) 
	{ 
	    $value = trim($value); 
	}
	
	if (!function_exists("mime_content_type")) {
	   function mime_content_type($fn) {
	
	      static $mime_magic_data;
	
	      #-- fallback
	      $type = false;
	
	      #-- read in first 3K of given file
	      if (is_dir($fn)) {
	         return("httpd/unix-directory");
	      }
	      elseif (is_resource($fn) || ($fn = @fopen($fn, "rb"))) {
	         $bin = fread($fn, $maxlen=3072);
	         fclose($fn);
	      }
	      elseif (!file_exists($fn)) {
	         return false;
	      }
	      else {
	         return("application/octet-stream");   // give up
	      }
	
	      #-- use PECL::fileinfo when available
	      if (function_exists("finfo_buffer")) {
	         if (!isset($mime_magic_data)) {
	            $mime_magic_data = finfo_open(MAGIC_MIME);
	         }
	         $type = finfo_buffer($bin);
	         return($type);
	      }
	      
	      #-- read in magic data, when called for the very first time
	      if (!isset($mime_content_type)) {
	      
	         if ((file_exists($fn = ini_get("mime_magic.magicfile")))
	          or (file_exists($fn = "/usr/share/misc/magic.mime"))
	          or (file_exists($fn = "/etc/mime-magic"))   )
	         {
	            $mime_magic_data = array();
	
	            #-- read in file
	            $f = fopen($fn, "r");
	            $fd = fread($f, 1<<20);
	            fclose($f);
	            $fd = str_replace("       ", "\t", $fd);
	
	            #-- look at each entry
	            foreach (explode("\n", $fd) as $line) {
	
	               #-- skip empty lines
	               if (!strlen($line) or ($line[0] == "#") or ($line[0] == "\n")) {
	                  continue;
	               }
	
	               #-- break into four fields at tabs
	               $l = preg_split("/\t+/", $line);
	               @list($pos, $typestr, $magic, $ct) = $l;
	#print_r($l);
	
	               #-- ignore >continuing lines
	               if ($pos[0] == ">") {
	                  continue;
	               }
	               #-- real mime type string?
	               $ct = strtok($ct, " ");
	               if (!strpos($ct, "/")) {
	                  continue;
	               }
	
	               #-- mask given?
	               $mask = 0;
	               if (strpos($typestr, "&")) {
	                  $typestr = strtok($typestr, "&");
	                  $mask = strtok(" ");
	                  if ($mask[0] == "0") {
	                     $mask = ($mask[1] == "x") ? hexdec(substr($mask, 2)) : octdec($mask);
	                  }
	                  else {
	                     $mask = (int)$mask;
	                  }
	               }
	
	               #-- strip prefixes
	               if ($magic[0] == "=") {
	                  $magic = substr($magic, 1);
	               }
	
	               #-- convert type
	               if ($typestr == "string") {
	                  $magic = stripcslashes($magic);
	                  $len = strlen($magic);
	                  if ($mask) { 
	                     continue;
	                  }
	               }
	               #-- numeric values
	               else {
	
	                  if ((ord($magic[0]) < 48) or (ord($magic[0]) > 57)) {
	#echo "\nmagicnumspec=$line\n";
	#var_dump($l);
	                     continue;  #-- skip specials like  >, x, <, ^, &
	                  }
	
	                  #-- convert string representation into int
	                  if ((strlen($magic) >= 4) && ($magic[1] == "x")) {
	                     $magic = hexdec(substr($magic, 2));
	                  }
	                  elseif ($magic[0]) {
	                     $magic = octdec($magic);
	                  }
	                  else {
	                     $magic = (int) $magic;
	                     if (!$magic) { continue; }   // zero is not a good magic value anyhow
	                  }
	
	                  #-- different types               
	                  switch ($typestr) {
	
	                     case "byte":
	                        $len = 1;
	                        break;
	                        
	                     case "beshort":
	                        $magic = ($magic >> 8) | (($magic & 0xFF) << 8);
	                     case "leshort":
	                     case "short":
	                        $len = 2;
	                        break;
	                     
	                     case "belong":
	                        $magic = (($magic >> 24) & 0xFF)
	                               | (($magic >> 8) & 0xFF00)
	                               | (($magic & 0xFF00) << 8)
	                               | (($magic & 0xFF) << 24);
	                     case "lelong":
	                     case "long":
	                        $len = 4;
	                        break;
	
	                     default:
	                        // date, ldate, ledate, leldate, beldate, lebelbe...
	                        continue;
	                  }
	               }
	               
	               #-- add to list
	               $mime_magic_data[] = array($pos, $len, $mask, $magic, trim($ct));
	            }
	         }
	#print_r($mime_magic_data);
	      }
	      
	      
	      #-- compare against each entry from the mime magic database
	      foreach ($mime_magic_data as $def) {
	
	         #-- entries are organized as follows
	         list($pos, $len, $mask, $magic, $ct) = $def;
	         
	         #-- ignored entries (we only read first 3K of file for opt. speed)
	         if ($pos >= $maxlen) {
	            continue;
	         }
	
	         $slice = substr($bin, $pos, $len);
	         #-- integer comparison value
	         if ($mask) {
	            $value = hexdec(bin2hex($slice));
	            if (($value & $mask) == $magic) {
	               $type = $ct;
	               break;
	            }
	         }
	         #-- string comparison
	         else {
	            if ($slice == $magic) {
	               $type = $ct;
	               break;
	            }
	         }
	      }// foreach
	      
	      #-- built-in defaults
	      if (!$type) {
	      
	         #-- some form of xml
	         if (strpos($bin, "<"."?xml ") !== false) {
	            return("text/xml");
	         }
	         #-- html
	         elseif ((strpos($bin, "<html>") !== false) || (strpos($bin, "<HTML>") !== false)
	         || strpos($bin, "<title>") || strpos($bin, "<TITLE>")
	         || (strpos($bin, "<!--") !== false) || (strpos($bin, "<!DOCTYPE HTML ") !== false)) {
	            $type = "text/html";
	         }
	         #-- mail msg
	         elseif ((strpos($bin, "\nReceived: ") !== false) || strpos($bin, "\nSubject: ")
	         || strpos($bin, "\nCc: ") || strpos($bin, "\nDate: ")) {
	            $type = "message/rfc822";
	         }
	         #-- php scripts
	         elseif (strpos($bin, "<"."?php") !== false) {
	            return("application/x-httpd-php");
	         }
	         #-- plain text, C source or so
	         elseif (strpos($bin, "function ") || strpos($bin, " and ")
	         || strpos($bin, " the ") || strpos($bin, "The ")
	         || (strpos($bin, "/*") !== false) || strpos($bin, "#include ")) {
	            return("text/plain");
	         }
	
	         #-- final fallback
	         else {
	            $type = "application/octet-stream";
	         }
	      }
	      
	      
	
	      #-- done
	      return $type;
	   }
	}
	
	
	
	
	
	function check_foldertitle_exists($title, $parent_guid = 0, $container_guid)
	{
		global $CONFIG;
		
		$result = false;
		
		$entities_options = array(
						'type' => 'object',
						'subtype' => 'folder',
            	 		'owner_guid' => $container_guid,
						'limit' => 1,
						'joins' => array(
										"JOIN {$CONFIG->dbprefix}objects_entity oe 	ON e.guid = oe.guid",
										"JOIN {$CONFIG->dbprefix}metadata datao 	ON datao.entity_guid = e.guid",
										"JOIN {$CONFIG->dbprefix}metadata datapg 	ON datapg.entity_guid = e.guid",
		
										"JOIN {$CONFIG->dbprefix}metastrings msno 	ON datao.name_id = msno.id",
										"JOIN {$CONFIG->dbprefix}metastrings msvo 	ON datao.value_id = msvo.id",
		
										"JOIN {$CONFIG->dbprefix}metastrings msnpg 	ON datapg.name_id = msnpg.id",
										"JOIN {$CONFIG->dbprefix}metastrings msvpg 	ON datapg.value_id = msvpg.id",
									),
						'wheres' => array(
										"oe.title 		= '$title'",
										"msno.string 	= 'order'",
										"msnpg.string 	= 'parent_guid'",
										"msvpg.string 	= '$parent_guid'",
									),
						'order_by' => "msvo.string ASC"
					);
					
		if($entities = elgg_get_entities($entities_options))
		{
			$result = $entities[0];
		}
		
		return $result;
	}
	
	function file_tools_create_folders($zip_entry, $parent_guid, $container_guid)
	{
		$zdir = substr(zip_entry_name($zip_entry), 0, -1);
		$container_entity = get_entity($container_guid);
		
		if($container_entity instanceof ElggUser)
		{
			$access_id = get_default_access();
		}
		elseif($container_entity instanceof ElggGroup)
		{
			$access_id = $container_entity->group_acl;
		}
	            
		$sub_folders = explode('/', $zdir);
		$count = count($sub_folders);
		
		if($count == 1)
		{
			$zdir = sanitise_string($zdir);
			$entity = check_foldertitle_exists($zdir, $parent_guid, $container_guid);

			if(!$entity)
			{
				$directory = new ElggObject();
				$directory->subtype = 'folder';
				$directory->owner_guid = $container_guid;
				$directory->container_guid = $container_guid;
				
				$directory->access_id = $access_id;
						
				$directory->title = $zdir;
				$directory->description = $zdir;
				$directory->parent_guid = $parent_guid;
						
				$order = elgg_get_entities_from_metadata(array(
					"type" => "object",
					"subtype" => 'folder',
					"metadata_name" => "parent_guid",
					"metadata_value" => $parent_guid,
					"count" => true
				));
						
				$directory->order = $order;
						
				$directory->save();
            }
		}
		else
		{
			$parent = $parent_guid;
			foreach($sub_folders as $folder)
			{
				$folder = sanitise_string($folder);
				if($entity = check_foldertitle_exists($folder, $parent, $container_guid))
				{
					$parent = $entity->getGUID();
				}
				else
				{
					$directory = new ElggObject();
					$directory->subtype = 'folder';
					$directory->owner_guid = $container_guid;
					$directory->container_guid = $container_guid;
					
					$directory->access_id = $access_id;
						
					
					$directory->title = $folder;
					$directory->description = $folder;
					$directory->parent_guid = $parent;
						
					$order = elgg_get_entities_from_metadata(array(
						"type" => "object",
						"subtype" => 'folder',
						"metadata_name" => "parent_guid",
						"metadata_value" => $parent,
						"count" => true
					));
							
					$directory->order = $order;
							
					$directory->save();
            	}
			}
		}
	}
	
	function unzip($file, $parent_guid, $container_guid)
	{
		$extracted = false;
		
		$change_access_parent_guid = $parent_guid;
		
		$allowed_extensions = file_tools_allowed_extensions();
		
		$zipfile = $file['tmp_name'];
		
		$container_entity = get_entity($container_guid);
		
		if($parent_guid != 0)
		{
			$access_id 				= get_entity($parent_guid);
		}
		else
		{
			if($container_entity instanceof ElggUser)
			{
				$access_id = get_default_access();
			}
			elseif($container_entity instanceof ElggGroup)
			{
				$access_id = $container_entity->group_acl;
			}
		}
		
	    $zip = zip_open($zipfile);
	    while ($zip_entry = zip_read($zip))
	    {
	        zip_entry_open($zip, $zip_entry);
	        if (substr(zip_entry_name($zip_entry), -1) == '/') 
	        {
				file_tools_create_folders($zip_entry, $parent_guid, $container_guid);
	        }
	        else 
	        {
	            $folder_array = explode('/', zip_entry_name($zip_entry));
	            
	            $parent = $parent_guid;
	            foreach($folder_array as $folder)
	            {
	            	$folder = sanitize_string($folder);
		            if($entity = check_foldertitle_exists($folder, $parent, $container_guid))
					{
						$parent = $entity->getGUID();
					}
					else
					{
						if($folder == end($folder_array))
						{
							$prefix = "file/";
							$extension_array = explode('.', $folder);
							
							$file_extension				= end($extension_array);
							$file_size 					= zip_entry_filesize($zip_entry);							
							
							if(in_array(strtolower($file_extension), $allowed_extensions))
							{
								$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							
								
								$filehandler = new ElggFile();
								$filehandler->setFilename($prefix . $folder);
																
								$filehandler->title 			= $folder;
								$filehandler->originalfilename 	= $folder;	
								$filehandler->owner_guid		= get_loggedin_userid();
								
								$filehandler->container_guid 	= $container_guid;
								$filehandler->access_id			= $access_id;
								

								$filehandler->open("write");
								$filehandler->write($buf);
								
								$mime_type = mime_content_type($filehandler->getFilenameOnFilestore());
								$simple_type = explode('/', $mime_type);
								
								$filehandler->setMimeType($mime_type);
								$filehandler->simpletype = $simple_type[0];
								
								if($simple_type[0] == "image")
								{
									$filestorename = elgg_strtolower(time().$folder);
									
									$thumbnail = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),60,60, true);
									if ($thumbnail) {
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
									if ($thumbsmall) {
										$thumb->setFilename($prefix."smallthumb".$filestorename);
										$thumb->open("write");
										$thumb->write($thumbsmall);
										$thumb->close();
										$filehandler->smallthumb = $prefix."smallthumb".$filestorename;
										unset($thumbsmall);
									}
									
									$thumblarge = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),600,600, false);
									if ($thumblarge) {
										$thumb->setFilename($prefix."largethumb".$filestorename);
										$thumb->open("write");
										$thumb->write($thumblarge);
										$thumb->close();
										$filehandler->largethumb = $prefix."largethumb".$filestorename;
										unset($thumblarge);
									}
								}
								
								set_input('folder_guid', $parent);
								
								$filehandler->save();
								
								$test_guid = $filehandler->getGUID();
								
								$filehandler->close();
								
								add_entity_relationship($container_guid, 'folder_of', $filehandler->getGUID());
								
								$extracted = true;
								
								if($parent == 0)
								{
									remove_entity_relationships($test_guid, FILE_TREE_RELATIONSHIP, true);
								}
							}
						}
					}
	            }
	        }
	        
	        zip_entry_close($zip_entry);
	    }
	    
	    zip_close($zip);
	    
	    $change_access_parent_folder = get_entity($change_access_parent_guid);
	    
	    file_tools_change_children_access($change_access_parent_folder);
	    
	    return $extracted;
	}