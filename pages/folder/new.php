<?php

elgg_gatekeeper();

$page_owner = elgg_get_page_owner_entity();
if (empty($page_owner) || (!elgg_instanceof($page_owner, "user") && !elgg_instanceof($page_owner, "group"))) {
	forward(REFERER);
}

// set page owner & context
elgg_set_context("file");

// get data
// build page elements
$title_text = elgg_echo("file_tools:new:title");

$form_vars = array(
	"id" => "file_tools_edit_form"
);
$body_vars = array(
	"page_owner_entity" => $page_owner
);

// draw page
if (elgg_is_xhr()) {
	echo "<div style='width: 550px; height:550px;'>";
	echo elgg_view_title($title_text);
	echo elgg_view_form("file_tools/folder/edit", $form_vars, $body_vars);
	echo "</div>";
} else {
	echo elgg_view_page($title_text, elgg_view_layout("one_sidebar", array(
		"title" => $title_text,
		"content" => elgg_view_form("file_tools/folder/edit", $form_vars, $body_vars)
	)));
}
