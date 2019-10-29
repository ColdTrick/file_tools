<?php
use Elgg\Database\Select;
use Elgg\Database\QueryBuilder;
use Elgg\Database\Clauses\OrderByClause;

/**
 * Individual's or group's files
 *
 * @package ElggFile
 */

$username = elgg_extract('username', $vars);
if ($username) {
	$user = get_user_by_username($username);
	$guid = $user->guid;
} else {
	// Backward compatibility
	$guid = elgg_extract('guid', $vars);
}

elgg_entity_gatekeeper($guid);

elgg_group_tool_gatekeeper('file', $guid);

$owner = get_entity($guid);

elgg_push_collection_breadcrumbs('object', 'file', $owner);

elgg_register_title_button('file', 'add', 'object', 'file');

$folder_guid = (int) get_input('folder_guid', 0);

$limit = (int) elgg_get_plugin_setting('list_length', 'file_tools');
$offset = (int) get_input('offset', 0);

$sort_by = 'e.time_created';
if (($owner instanceof ElggGroup) && !empty($owner->file_tools_sort)) {
	$sort_by = $owner->file_tools_sort;
} elseif ($site_sort_default = elgg_get_plugin_setting('sort', 'file_tools')) {
	$sort_by = $site_sort_default;
}

$direction = 'asc';
if (($owner instanceof ElggGroup) && !empty($owner->file_tools_sort_direction)) {
	$direction = $owner->file_tools_sort_direction;
} elseif ($site_sort_direction_default = elgg_get_plugin_setting('sort_direction', 'file_tools')) {
	$direction = $site_sort_direction_default;
}

$params = [];

if ($owner->guid == elgg_get_logged_in_user_guid()) {
	// user looking at own files
	$params['filter_context'] = 'mine';
} else if ($owner instanceof ElggUser) {
	// someone else's files
	// do not show select a tab when viewing someone else's posts
	$params['filter_context'] = 'none';
}

// files
$files_options = [
	'type' => 'object',
	'subtype' => 'file',
	'limit' => false,
	'container_guid' => $owner->guid,
];

switch ($sort_by) {
	case 'simpletype':
		$files_options['order_by_metadata'] = [
			'name' => 'mimetype',
			'direction' => $direction,
		];
		break;
	case 'oe.title':
		$files_options['order_by_metadata'] = [
			'name' => 'title',
			'direction' => $direction,
		];
		break;
	default:
		$files_options['order_by'] = new OrderByClause('e.time_created', $direction);
		break;
}

$folder = false;
if (!empty($folder_guid)) {
	$folder = get_entity($folder_guid);
	if ($folder instanceof \FileToolsFolder && ($folder->getContainerGUID() === $owner->guid)) {
		$files_options['relationship'] = FileToolsFolder::RELATIONSHIP;
		$files_options['relationship_guid'] = $folder_guid;
		$files_options['inverse_relationship'] = false;
	} else {
		$folder = false; // just to be save
	}
}
if (empty($folder)) {
	// e.guid can only be a file that has no relation with a folder
	$files_options['wheres'] = [
		function (QueryBuilder $qb, $main_alias) {
			$sub_query = $qb->subquery('entity_relationships', 'r');
			$sub_query->select('r.guid_two')
				->where($qb->compare('r.relationship', '=', FileToolsFolder::RELATIONSHIP, ELGG_VALUE_STRING));
			
			return $qb->compare("{$main_alias}.guid", 'NOT IN', $sub_query->getSQL());
		},
	];
}

// get the files
$files = elgg_get_entities($files_options);

$content = elgg_format_element('div', [
	'id' => 'file_tools_list_files_container',
	'class' => 'elgg-content',
]);

$title = elgg_echo('collection:object:file:owner', [$owner->getDisplayName()]);

// make sidebar
$sidebar = elgg_view('file_tools/list/tree', [
	'folder' => $folder,
	'folders' => file_tools_get_folders($owner->guid),
]);

$sidebar .= elgg_view('file_tools/sidebar/info');
$sidebar .= elgg_view('page/elements/tagcloud_block', [
	'subtypes' => 'file',
	'container_guid' => $owner->guid,
]);

$params['content'] = elgg_view('file_tools/list/files', [
	'files' => $files,
	'folder_guid' => $folder_guid,
]);
$params['title'] = $title;
$params['sidebar'] = $sidebar;
$params['entity'] = get_entity($folder_guid);

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
