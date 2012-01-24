<?php 

	gatekeeper();
	
	$page_owner = page_owner_entity();
	
	if(!empty($page_owner) && (($page_owner instanceof ElggUser) || ($page_owner instanceof ElggGroup)))
	{
		// set page owner & context
		set_context("file");
		
		// get data
		// build page elements
		$title_text = elgg_echo("file_tools:new:title");
		$title = elgg_view_title($title_text);
		
		$body = elgg_view("file_tools/forms/edit", array("page_owner_entity" => $page_owner));
		
		// build page
		$page_data = $title . $body;
		
		// draw page
		page_draw($title_text, elgg_view_layout("two_column_left_sidebar", "", $page_data));
	}
	else
	{
		forward();
	}