<?php
require_once(dirname(__FILE__) . '/lib/functions.php');

$composer_path = '';
if (is_dir(__DIR__ . '/vendor')) {
	$composer_path = __DIR__ . '/';
}

return [
	'settings' => [
		'list_length' => 50,
		'user_folder_structure' => 'no',
	],
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'folder',
			'class' => FileToolsFolder::class,
		],
	],
	'views' => [
		'default' => [
			'js/jquery.serializejson.js' => $composer_path . 'vendor/bower-asset/jquery.serializeJSON/jquery.serializejson.min.js',
			'js/jstree/' => $composer_path . 'vendor/bower-asset/jstree/dist/',
			'js/jquery.hashchange.js' =>  __DIR__ . '/vendors/hashchange/jquery.hashchange.js',
		],
	],
	'actions' => [
		'file_tools/groups/save_sort' => [],
		'file_tools/folder/edit' => [],
		'file_tools/folder/delete' => [],
		'file_tools/folder/reorder' => [],
		'file_tools/upload/zip' => [],
		'file_tools/file/hide' => [],
		'file_tools/file/move' => [],
		'file_tools/file/bulk_delete' => [],
	],
];
