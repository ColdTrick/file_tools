<?php 

	gatekeeper();
	
	$page_owner = elgg_get_page_owner_entity();
	
	if(!empty($page_owner) && (($page_owner instanceof ElggUser) || ($page_owner instanceof ElggGroup)))
	{
		// set page owner & context
		elgg_set_context("file");
		
		// get data
		// build page elements
		$title_text = elgg_echo("file_tools:new:title");
		$title = elgg_view_title($title_text);
		
		$body = elgg_view("file_tools/forms/edit", array("page_owner_entity" => $page_owner));
		
		// build page
		$page_data = $title . $body;
		
		// draw page
		echo elgg_view_page($title_text, elgg_view_layout("one_sidebar", array('content' => $page_data)));
	}
	else
	{
		forward();
	}