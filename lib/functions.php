<?php
/**
 * All helper functions are bundled here
 */

/**
 * Get the folders in a container
 *
 * @param int $container_guid the container to check
 *
 * @return bool|ElggObject[]
 */
function file_tools_get_folders($container_guid = 0) {
	
	$container_guid = (int) $container_guid;
	if (empty($container_guid)) {
		$container_guid = elgg_get_page_owner_guid();
	}
	
	if (empty($container_guid)) {
		return false;
	}
	
	$folders = elgg_get_entities([
		'type' => 'object',
		'subtype' => \FileToolsFolder::SUBTYPE,
		'container_guid' => $container_guid,
		'limit' => false,
	]);
	if (empty($folders)) {
		return false;
	}
	
	$parents = array();
	
	/* @var $folder ElggObject */
	foreach ($folders as $folder) {
		$parent_guid = (int) $folder->parent_guid;
		
		if (!empty($parent_guid)) {
			$temp = get_entity($parent_guid);
			if (!$temp instanceof \FileToolsFolder) {
				$parent_guid = 0;
			}
		}
		
		if (!array_key_exists($parent_guid, $parents)) {
			$parents[$parent_guid] = [];
		}
		
		$parents[$parent_guid][] = $folder;
	}
	
	return file_tools_sort_folders($parents, 0);
}

/**
 * Make folder select options
 *
 * @param array $folders folders to make the options for
 * @param int   $depth   current depth
 *
 * @return []
 */
function file_tools_build_select_options($folders, $depth = 0) {
	$result = [];
	
	if (empty($folders)) {
		return [];
	}
	
	foreach ($folders as $index => $level) {
		/**
		 * $level contains
		 * folder: the folder on this level
		 * children: potential children
		 *
		 */
		$folder = elgg_extract('folder', $level);
		if (!empty($folder)) {
			$result[$folder->guid] = str_repeat('-', $depth) . ' ' . $folder->title;
		}
		
		$childen = elgg_extract('children', $level);
		if (!empty($childen)) {
			$result += file_tools_build_select_options($childen, $depth + 1);
		}
	}
	
	return $result;
}

/**
 * Get folder selection options for widgets
 *
 * @param array  $folder       the folder to create for
 * @param string $internalname the name of the input field
 * @param array  $selected     the current selected values
 *
 * @return string
 */
function file_tools_build_widget_options($folder, $internalname = "", $selected = []) {
	$result = '';
	
	if (is_array($folder) && !array_key_exists('children', $folder)) {
		foreach ($folder as $folder_item) {
			$content = file_tools_build_widget_options($folder_item, $internalname, $selected);
			
			$result .= elgg_format_element('ul', [], $content);
		}
	} else {
		$folder_item = $folder['folder'];
		
		$content = elgg_view('input/checkbox', [
			'default' => false,
			'name' => $internalname,
			'label' => $folder_item->title,
			'label_tag' => 'span',
			'value' => $folder_item->guid,
			'checked' => in_array($folder_item->guid, $selected),
		]);
		
		if (!empty($folder['children'])) {
			$content .= file_tools_build_widget_options($folder['children'], $internalname, $selected);
		}
		
		$result .= elgg_format_element('li', [], $content);
	}
	
	return $result;
}

/**
 * Folder folders by their order
 *
 * @param array $folders     the folders to sort
 * @param int   $parent_guid the parent to sort for
 *
 * @return false|array
 */
function file_tools_sort_folders($folders, $parent_guid = 0) {
	
	if (!array_key_exists($parent_guid, $folders)) {
		return false;
	}
	
	$result = [];
	
	/* @var $subfolder ElggObject */
	foreach ($folders[$parent_guid] as $subfolder) {
		$children = file_tools_sort_folders($folders, $subfolder->guid);
		
		$order = (int) $subfolder->order;
		if (empty($order)) {
			$order = $subfolder->time_created;
		}
		
		while (array_key_exists($order, $result)) {
			$order++;
		}
		
		$result[$order] = [
			'folder' => $subfolder,
			'children' => $children,
		];
	}
	
	ksort($result);
	
	return $result;
}

/**
 * Get the subfolders of a folder
 *
 * @param ElggObject $folder the folder to get the subfolders for
 * @param bool       $list   output a list (default: false)
 *
 * @return bool|array|string
 */
function file_tools_get_sub_folders($folder = false, $list = false) {
	
	$options = [
		'type' => 'object',
		'subtype' => \FileToolsFolder::SUBTYPE,
		'limit' => false,
		'full_view' => false,
		'pagination' => false
	];

	$page_owner = elgg_get_page_owner_entity();

	if (($page_owner instanceof ElggGroup) && !empty($page_owner->file_tools_sort)) {
		// Each group has its own sorting settings
		$order_by = $page_owner->file_tools_sort;
		$order_direction = $page_owner->file_tools_sort_direction;
		
		switch ($order_by) {
			case 'simpletype':
				$options['order_by_metadata'] = [
					'name' => 'mimetype',
					'direction' => $order_direction,
				];
				break;
			case 'oe.title':
				$options['order_by_metadata'] = [
					'name' => 'title',
					'direction' => $order_direction,
				];
				break;
			default:
				$options['order_by'] = new OrderByClause('e.time_created', $order_direction);
				break;
		}
	} else {
		// Default to sorting by 'order' metadata
		$options['order_by_metadata'] = [
			'name' => 'order',
			'direction' => 'ASC',
			'as' => 'integer',
		];
	}

	if ($folder instanceof FileToolsFolder) {
		$options['container_guid'] = $folder->getContainerGUID();
		$parent_guid = $folder->guid;
	} else {
		$options['container_guid'] = $page_owner->guid;
		$parent_guid = 0;
	}

	$options['metadata_name_value_pairs'] = [
		'parent_guid' => $parent_guid,
	];

	if ($list) {
		return elgg_list_entities($options);
	}
	
	return elgg_get_entities($options);
}

/**
 * Get the allowed extensions for uploading
 *
 * @param bool $zip return zip upload dialog format
 *
 * @return bool|string
 */
function file_tools_allowed_extensions($zip = false) {
	
	$allowed_extensions_settings = elgg_get_plugin_setting('allowed_extensions', 'file_tools');
	$allowed_extensions_settings = string_to_tag_array($allowed_extensions_settings);
	
	if (!empty($allowed_extensions_settings)) {
		$result = $allowed_extensions_settings;
	} else {
		$result = ['txt','jpg','jpeg','png','bmp','gif','pdf','doc','docx','xls','xlsx','ppt','pptx','odt','ods','odp'];
	}
	
	if (!$zip) {
		return $result;
	}
	
	$result = implode(";*.", $result);
	
	return "*." . $result;
}

/**
 * Check for unique folder names
 *
 * @param string $title          the title to check
 * @param int    $container_guid the container to limit the search to
 * @param int    $parent_guid    optional parent guid
 *
 * @return false|ElggObject
 */
function file_tools_check_foldertitle_exists($title, $container_guid, $parent_guid = 0) {
	
	$entities_options = [
		'type' => 'object',
		'subtype' => \FileToolsFolder::SUBTYPE,
		'container_guid' => $container_guid,
		'limit' => 1,
		'metadata_name_value_pairs' => [
			'title' => $title,
		],
		'order_by_metadata' => [
			'name' => 'order',
			'direction' => 'ASC',
			'as' => 'integer'
		],
	];
	
	if (!empty($parent_guid)) {
		$entities_options['metadata_name_value_pairs'] = [
			'parent_guid' => $parent_guid,
		];
	}
	
	$entities = elgg_get_entities($entities_options);
	if (!empty($entities)) {
		return $entities[0];
	}
	
	return false;
}

/**
 * Create folders from a zip file structure
 *
 * @param zip_entry $zip_entry      the zip file entry
 * @param int       $container_guid the container where the folders need to be created
 * @param int       $parent_guid    the parent folder
 *
 * @return void
 */
function file_tools_create_folders($zip_entry, $container_guid, $parent_guid = 0) {
	
	$zip_entry_name = zip_entry_name($zip_entry);
	$filename = basename($zip_entry_name);
	
	if (substr($zip_entry_name, -1) != '/') {
		$zip_base = str_replace($filename, '', $zip_entry_name);
	} else {
		$zip_base = $zip_entry_name;
	}
	
	$zdir = substr($zip_base, 0, -1);
	if (empty($zdir)) {
		return;
	}
	
	$parent_guid = (int) $parent_guid;
	$container_entity = get_entity($container_guid);
	
	$access_id = get_input('access_id', false);
	if ($access_id === false) {
		if ($parent_guid !== 0) {
			$access_id = get_entity($parent_guid)->access_id;
		} else {
			if ($container_entity instanceof ElggGroup) {
				$access_id = $container_entity->group_acl;
			} else {
				$access_id = get_default_access($container_entity);
			}
		}
	}
	
	$sub_folders = explode('/', $zdir);
	if (count($sub_folders) == 1) {
		$entity = file_tools_check_foldertitle_exists($zdir, $container_guid, $parent_guid);
		
		if (!$entity) {
			$directory = new FileToolsFolder();
			$directory->owner_guid = elgg_get_logged_in_user_guid();
			$directory->container_guid = $container_guid;
			
			$directory->access_id = $access_id;
			
			$directory->title = $zdir;
			$directory->parent_guid = $parent_guid;
			
			$order = elgg_count_entities([
				'type' => 'object',
				'subtype' => \FileToolsFolder::SUBTYPE,
				'metadata_name_value_pairs' => [
					'name' => 'parent_guid',
					'value' => $parent_guid,
				],
			]);
			
			$directory->order = $order;
			
			$directory->save();
		}
	} else {
		$parent = $parent_guid;
		
		foreach ($sub_folders as $folder) {
			$entity = file_tools_check_foldertitle_exists($folder, $container_guid, $parent);
			if (!empty($entity)) {
				$parent = $entity->guid;
			} else {
				$directory = new FileToolsFolder();
				$directory->owner_guid = elgg_get_logged_in_user_guid();
				$directory->container_guid = $container_guid;
				
				$directory->access_id = $access_id;
				
				$directory->title = $folder;
				$directory->parent_guid = $parent;
				
				$order = elgg_count_entities([
					'type' => 'object',
					'subtype' => \FileToolsFolder::SUBTYPE,
					'metadata_name_value_pairs' => [
						'name' => 'parent_guid',
						'value' => $parent,
					],
				]);
				
				$directory->order = $order;
				
				$parent = $directory->save();
			}
		}
	}
}

/**
 * Unzip an uploaded zip file
 *
 * @param array $file           the $_FILES information
 * @param int   $container_guid the container to put the files/folders under
 * @param int   $parent_guid    the parrent folder
 *
 * @return bool
 */
function file_tools_unzip($file, $container_guid, $parent_guid = 0) {
	
	$container_guid = (int) $container_guid;
	$parent_guid = (int) $parent_guid;
	
	if (empty($file) || empty($container_guid)) {
		return false;
	}
	
	$container_entity = get_entity($container_guid);
	if (empty($container_entity)) {
		return false;
	}
	
	$extracted = false;
	$allowed_extensions = file_tools_allowed_extensions();
	
	$zipfile = elgg_extract('tmp_name', $file);
	
	$access_id = get_input('access_id', false);
	if ($access_id === false) {
		$parent_folder = get_entity($parent_guid);
		if ($parent_folder instanceof \FileToolsFolder) {
			$access_id = $parent_folder->access_id;
		} else {
			if ($container_entity instanceof ElggGroup) {
				$access_id = $container_entity->group_acl;
			} else {
				$access_id = get_default_access($container_entity);
			}
		}
	}
	
	// open the zip file
	$zip = zip_open($zipfile);
	while ($zip_entry = zip_read($zip)) {
		// open the zip entry
		zip_entry_open($zip, $zip_entry);
		
		// set some variables
		$zip_entry_name = zip_entry_name($zip_entry);
		$filename = basename($zip_entry_name);
		
		error_log($zip_entry_name);
		error_log($filename);
		
		// check for folder structure
		if (strlen($zip_entry_name) != strlen($filename)) {
			// there is a folder structure, check it and create missing items
			file_tools_create_folders($zip_entry, $container_guid, $parent_guid);
		}
		
		// extract the folder structure from the zip entry
		$folder_array = explode('/', $zip_entry_name);
		
		$parent = $parent_guid;
		foreach ($folder_array as $folder) {
			$folder = utf8_encode($folder);
			
			$entity = file_tools_check_foldertitle_exists($folder, $container_guid, $parent);
			if (!empty($entity)) {
				$parent = $entity->guid;
			} else {
				if ($folder !== end($folder_array)) {
					continue;
				}
				
				$prefix = 'file/';
				$extension_array = explode('.', $folder);
				
				$file_extension	= end($extension_array);
				
				if (!in_array(strtolower($file_extension), $allowed_extensions)) {
					continue;
				}
				
				$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
				
				$filestorename = elgg_strtolower(time() . $folder);
				
				// create the file
				$filehandler = new ElggFile();
				$filehandler->setFilename($prefix . $filestorename);
				
				$filehandler->title 			= $folder;
				$filehandler->originalfilename 	= $folder;
				$filehandler->owner_guid		= elgg_get_logged_in_user_guid();
				
				$filehandler->container_guid 	= $container_guid;
				$filehandler->access_id			= $access_id;
				
				if (!$filehandler->save()) {
					continue;
				}
				
				$filehandler->open('write');
				$filehandler->write($buf);
				$filehandler->close();
				
				$mime_type = $filehandler->detectMimeType($filehandler->getFilenameOnFilestore());
				
				// hack for Microsoft zipped formats
				$info = pathinfo($folder);
				$office_formats = ['docx', 'xlsx', 'pptx'];
				if ($mime_type == 'application/zip' && in_array($info['extension'], $office_formats)) {
					switch ($info['extension']) {
						case 'docx':
							$mime_type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
							break;
						case 'xlsx':
							$mime_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
							break;
						case 'pptx':
							$mime_type = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
							break;
					}
				}
				
				// check for bad ppt detection
				if ($mime_type == 'application/vnd.ms-office' && $info['extension'] == 'ppt') {
					$mime_type = 'application/vnd.ms-powerpoint';
				}
				
				$simple_type = elgg_get_file_simple_type($mime_type);
				
				$filehandler->setMimeType($mime_type);
				$filehandler->simpletype = $simple_type;
				
				if ($simple_type == 'image') {
					
					if ($filehandler->saveIconFromElggFile($filehandler)) {
						$filehandler->thumbnail = $filehandler->getIcon('small')->getFilename();
						$filehandler->smallthumb = $filehandler->getIcon('medium')->getFilename();
						$filehandler->largethumb = $filehandler->getIcon('large')->getFilename();
					}
				}
				
				set_input('folder_guid', $parent);
				
				$filehandler->save();
				
				$extracted = true;
				
				if (!empty($parent)) {
					add_entity_relationship($parent, FileToolsFolder::RELATIONSHIP, $filehandler->guid);
				}
			}
		}
		
		zip_entry_close($zip_entry);
	}
	
	zip_close($zip);
	
	return $extracted;
}

/**
 * Add a folder to a zip file
 *
 * @param ZipArchive &$zip_archive the zip file to add files/folder to
 * @param ElggObject $folder       the folder to add
 * @param string     $folder_path  the path of the current folder
 *
 * @return void
 */
function file_tools_add_folder_to_zip(ZipArchive &$zip_archive, ElggObject $folder, $folder_path = '') {
	
	if (!($zip_archive instanceof ZipArchive) || !$folder instanceof \FileToolsFolder) {
		return;
	}
	
	$folder_title = elgg_get_friendly_title($folder->title);
	
	$zip_archive->addEmptyDir($folder_path . $folder_title);
	$folder_path .= $folder_title . DIRECTORY_SEPARATOR;
	
	// add files from this folder to the zip
	$files = elgg_get_entities([
		'type' => 'object',
		'subtype' => 'file',
		'limit' => false,
		'relationship' => FileToolsFolder::RELATIONSHIP,
		'relationship_guid' => $folder->guid,
		'batch' => true,
	]);
	foreach ($files as $file) {
		// check if the file exists
		if ($zip_archive->statName($folder_path . $file->originalfilename) === false) {
			// doesn't exist, so add
			$zip_archive->addFile($file->getFilenameOnFilestore(), $folder_path . $file->originalfilename);
		} else {
			// file name exists, so create a new one
			$ext_pos = strrpos($file->originalfilename, '.');
			$file_name = substr($file->originalfilename, 0, $ext_pos) . '_' . $file->guid . substr($file->originalfilename, $ext_pos);
			
			$zip_archive->addFile($file->getFilenameOnFilestore(), $folder_path . $file_name);
		}
	}
	
	// check if there are subfolders
	$sub_folders = elgg_get_entities([
		'type' => 'object',
		'subtype' => \FileToolsFolder::SUBTYPE,
		'limit' => false,
		'metadata_name_value_pairs' => [
			'parent_guid' => $folder->guid,
		],
		'batch' => true,
	]);
	foreach ($sub_folders as $sub_folder) {
		file_tools_add_folder_to_zip($zip_archive, $sub_folder, $folder_path);
	}
}
