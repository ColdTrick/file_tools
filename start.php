<?php

	define("FILE_TOOLS_SUBTYPE", 		"folder");
	define("FILE_TOOLS_RELATIONSHIP", 	"folder_of");
	define("FILE_TOOLS_BASEURL", 		elgg_get_site_url() . "file_tools/");

	require_once(dirname(__FILE__) . "/lib/functions.php");
	require_once(dirname(__FILE__) . "/lib/events.php");
	require_once(dirname(__FILE__) . "/lib/hooks.php");
	require_once(dirname(__FILE__) . "/lib/page_handlers.php");

	function file_tools_init() {
		// extend CSS
		elgg_extend_view("css/elgg", "file_tools/css");
		if(elgg_get_plugin_setting("user_folder_structure", "file_tools") == "yes"){
			elgg_extend_view("forms/groups/edit", "file_tools/group_settings");
		}
				
		// register page handler for nice URL's
		elgg_register_page_handler("file_tools", "file_tools_page_handler");
		
		// make our own URLs for folders
		elgg_register_entity_url_handler("file_tools_folder_url_handler", "object", FILE_TOOLS_SUBTYPE);
		
		// make our own URLs for folder icons
		elgg_register_plugin_hook_handler("entity:icon:url", "object", "file_tools_folder_icon_hook");
		
		// register group option to allow management of file tree structure
		add_group_tool_option("file_tools_structure_management", elgg_echo("file_tools:group_tool_option:structure_management"));
		
		// add folder widget
		// need to keep file_tree for the widget name to be compatible with previous filetree plugin users
		elgg_register_widget_type ("file_tree", elgg_echo("widgets:file_tree:title"), elgg_echo("widgets:file_tree:description"), "dashboard,profile,groups");
		if(is_callable("widget_manager_add_widget_title_link")){
			widget_manager_add_widget_title_link("file_tree", "[BASEURL]file/owner/[USERNAME]");
		}
	}

	function file_tools_folder_url_handler($entity) {
		return elgg_get_site_url(). "file_tools/list/" . $entity->getContainer() . "#" . $entity->getGUID();
	}

	elgg_register_event_handler("init", "system", "file_tools_init");
	
	// register events
	elgg_register_event_handler("create", "object", "file_tools_object_handler");
	elgg_register_event_handler("update", "object", "file_tools_object_handler");
	elgg_register_event_handler("delete", "object", "file_tools_object_handler_delete");
	
	// register plugin hooks
	elgg_register_plugin_hook_handler("permissions_check:metadata", "object", "file_tools_can_edit_metadata_hook");
// 	elgg_register_plugin_hook_handler("access:collections:write", "all", "file_tools_write_acl_plugin_hook", 550);
	elgg_register_plugin_hook_handler("route", "file", "file_tools_file_route_hook");
	elgg_register_plugin_hook_handler("register", "menu:title", "file_tools_title_menu_register_hook");
	
	// register actions
	elgg_register_action("file_tools/groups/save_sort", dirname(__FILE__) . "/actions/groups/save_sort.php");
	elgg_register_action("file_tools/folder/edit", dirname(__FILE__) . "/actions/folder/edit.php");
	elgg_register_action("file_tools/folder/delete", dirname(__FILE__) . "/actions/folder/delete.php");	
	elgg_register_action("file_tools/import/zip", dirname(__FILE__) . "/actions/import/zip.php");
	elgg_register_action("file_tools/folder/delete", dirname(__FILE__) . "/actions/folder/delete.php");
	elgg_register_action("file_tools/file/hide", dirname(__FILE__) . "/actions/file/hide.php");