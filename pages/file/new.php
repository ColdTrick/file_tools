<?php 

	gatekeeper();

	$page_owner = elgg_get_page_owner_entity();
	
	if(!empty($page_owner))	{
		// set page owner & context
		elgg_set_context("file");
		
		// build breadcrumb
		elgg_push_breadcrumb(elgg_echo("file"), "file/all");
		if(elgg_instanceof($page_owner, "group", null, "ElggGroup")){
			elgg_push_breadcrumb($page_owner->name, "file/group/" . $page_owner->getGUID());
		} else {
			elgg_push_breadcrumb($page_owner->name, "file/owner/" . $page_owner->username);
		}
		elgg_push_breadcrumb(elgg_echo("file:upload"));
		
		// get data
		// build page elements
		$title_text = elgg_echo("file:upload");
		
		$body = elgg_view("file_tools/forms/upload", array("page_owner_entity" => $page_owner));
		
		// build page
		$page_data = elgg_view_layout("content", array(
			"title" => $title_text,
			"content" => $body,
			"filter" => ""
		));
		
		// draw page
		echo elgg_view_page($title_text, $page_data);
	} else {
		forward();
	}