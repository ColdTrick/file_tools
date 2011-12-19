<?php

	global $CONFIG;

	define("FILE_TOOLS_SUBTYPE", 		"folder");
	define("FILE_TOOLS_RELATIONSHIP", 	"folder_of");
	define("FILE_TOOLS_BASEURL", 		$CONFIG->wwwroot."pg/file_tools/");

	require_once(dirname(__FILE__) . "/lib/functions.php");
	require_once(dirname(__FILE__) . "/lib/events.php");
	require_once(dirname(__FILE__) . "/lib/hooks.php");

	function file_tools_init()
	{
		// extend CSS
		elgg_extend_view("css", "file_tools/css");
		
		// extend object view of file
		elgg_extend_view("object/file", "file_tools/extend/file", 499);
		elgg_extend_view("object/folder", "file_tools/extend/folder", 499);
		
		// register page handler for nice URL's
		register_page_handler("file_tools", "file_tools_page_handler");
		
		// make our own URLs for folders
		register_entity_url_handler("file_tools_folder_url_handler", "object", FILE_TOOLS_SUBTYPE);
		
		// make our own URLs for folder icons
		register_plugin_hook("entity:icon:url", "object", "file_tools_folder_icon_hook");
		
		// register group option to allow management of file tree structure
		add_group_tool_option("file_tools_structure_management", elgg_echo("file_tools:group_tool_option:structure_management"));
		
		// take over default file view?
		if(get_plugin_setting("replace_file", "file_tools") == "yes")
		{
			file_tools_replace_page_handler("file", "file_tools_file_page_handler");
		}
		
		// add widget
		add_widget_type("file_tools", elgg_echo("widgets:file_tools:title"), elgg_echo("widgets:file_tools:description"), "dashboard,profile,groups");
		if(is_callable("add_widget_title_link"))
		{
			add_widget_title_link("file_tools", "[BASEURL]pg/file_tools/list/[GUID]");
		}
	}

	function file_tools_pagesetup()
	{
		global $CONFIG;
		
		$context = get_context();
		$page_owner = page_owner_entity();
		
		if($context == "file")
		{
			elgg_extend_view("categories", "file_tools/extend/categories");
		}
		
		if(get_plugin_setting("replace_file", "file_tools") != "yes")
		{
			if($context == "file" && !empty($page_owner))
			{
				if($page_owner->getGUID() == get_loggedin_userid())
				{
					add_submenu_item(elgg_echo("file_tools:menu:mine"), $CONFIG->wwwroot . "pg/file_tools/list/" . $page_owner->getGUID());
				}
				else
				{
					add_submenu_item(sprintf(elgg_echo("file_tools:menu:user"), $page_owner->name), $CONFIG->wwwroot . "pg/file_tools/list/" . $page_owner->getGUID());
				}
			}
			
			if(($context == "groups") && ($page_owner instanceof ElggGroup) && ($page_owner->file_enable != "no"))
			{
				if($page_owner instanceof ElggGroup)
				{
					add_submenu_item(elgg_echo("file_tools:menu:group"), $CONFIG->wwwroot . "pg/file_tools/list/" . $page_owner->getGUID());
				}
			}
		}
		
		/*
		 * import 
		 */
		add_submenu_item(elgg_echo('file:all'), $CONFIG->wwwroot . "pg/file/all/");
		if (can_write_to_container($_SESSION['guid'], page_owner()) && isloggedin())
		{
			add_submenu_item(elgg_echo('file:upload'), $CONFIG->wwwroot . "pg/file/new/". $page_owner->username);
			add_submenu_item(elgg_echo('file_tools:upload:new'), $CONFIG->wwwroot . "pg/file_tools/import/zip/".$page_owner->username);
		}
	}
	
	function file_tools_page_handler($page)
	{
		switch($page[0])
		{
			case "list":
				if(!empty($page[1]))
				{
					set_input("page_owner", $page[1]);
					
					if(get_input("folder_guid", false) !== false)
					{
						set_input("draw_page", false);
					}
					
					if(array_key_exists(2, $page))
					{
						set_input("folder_guid", $page[2]);
					}
				}
				include(dirname(__FILE__) . "/pages/list.php");
				break;
			case "reorder":
				include(dirname(__FILE__) . "/procedures/reorder.php");
				break;
			case "folder":
				if($page[1] == 'new')
				{
					if(!empty($page[2]))
					{
						set_input("page_owner", $page[2]);
					}
					include(dirname(__FILE__) . "/pages/new.php");
					break;
				}
				elseif($page[1] == 'edit')
				{
					if(!empty($page[2]))
					{
						set_input("folder_guid", $page[2]);
						
						include(dirname(__FILE__) . "/pages/folder/edit.php");
						break;
					}
				}
			case "import":
				if(!empty($page[2]))
				{
					set_input("username", $page[2]);
				}
				include(dirname(__FILE__) . "/pages/import/zip.php");
				break;
			case "proc":
				if(file_exists(dirname(__FILE__)."/procedures/".$page[1]."/".$page[2].".php"))
				{
					include(dirname(__FILE__)."/procedures/".$page[1]."/".$page[2].".php");					
				} 
				else 
				{
					echo json_encode(array('valid' => 0));
					exit;
				}
				break;
			default:
				forward("pg/file_tools/list/" . get_loggedin_userid());
		}
	}
	
	function file_tools_file_page_handler($page)
	{
		switch($page[0])
		{
			case "owner":
				if(!empty($page[1]))
				{
					$username = $page[1];
					
					if(stristr($username, "group:"))
					{
						list($dummy, $guid) = explode(":", $username);
						set_input("page_owner", $guid);
					}
					elseif($user = get_user_by_username($username))
					{
						set_input("page_owner", $user->getGUID());
					}
					
					include(dirname(__FILE__) . "/pages/list.php");
				}
				break;
			default:
				file_tools_fallback_page_handler($page, "file");
				break;
		}
	}
	
	function file_tools_folder_url_handler($entity)
	{
		global $CONFIG;
		
		return $CONFIG->wwwroot . "pg/file_tools/list/" . $entity->getContainer() . "#" . $entity->getGUID();
	}

	register_elgg_event_handler("init", "system", "file_tools_init");
	register_elgg_event_handler("pagesetup", "system", "file_tools_pagesetup");
	
	// register events
	register_elgg_event_handler("create", "object", "file_tools_object_handler");
	register_elgg_event_handler("update", "object", "file_tools_object_handler");
	register_elgg_event_handler("delete", "object", "file_tools_object_handler_delete");
	
	// register plugin hooks
	register_plugin_hook("permissions_check:metadata", "object", "file_tools_can_edit_metadata_hook");
	register_plugin_hook("access:collections:write", "all", "file_tools_write_acl_plugin_hook", 550);
	
	// register actions
	register_action("file_tools/folder/edit", false, dirname(__FILE__) . "/actions/folder/edit.php");
	register_action("file_tools/folder/delete", false, dirname(__FILE__) . "/actions/folder/delete.php");
	
	
	//register_action("file_tools/import/upload", false,dirname(__FILE__)."/actions/import/upload.php");
	register_action("file_tools/import/zip", false,dirname(__FILE__)."/actions/import/zip.php");
	register_action("file_tools/folder/delete", false,dirname(__FILE__)."/actions/folder/delete.php");
	/*register_action("file_tools/import/delete", false,dirname(__FILE__)."/actions/zip/delete.php");
	register_action("file_tools/import/bulkdelete", false,dirname(__FILE__)."/actions/zip/bulkdelete.php");*/