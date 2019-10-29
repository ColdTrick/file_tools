<?php
use ColdTrick\FileTools\Bootstrap;
use Elgg\Router\Middleware\Gatekeeper;

require_once(dirname(__FILE__) . '/lib/functions.php');

$composer_path = '';
if (is_dir(__DIR__ . '/vendor')) {
	$composer_path = __DIR__ . '/';
}

return [
	'bootstrap' => Bootstrap::class,
	'settings' => [
		'list_length' => 50,
		'use_folder_structure' => 'no',
	],
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'folder',
			'class' => FileToolsFolder::class,
		],
	],
	'routes' => [
		'add:object:folder' => [
			'path' => '/file_tools/folder/new/{guid}',
			'resource' => 'file_tools/folder/new',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'edit:object:folder' => [
			'path' => '/file_tools/folder/edit/{guid}',
			'resource' => 'file_tools/folder/edit',
			'middleware' => [
				Gatekeeper::class,
			],
		],
		'add:object:file:zip' => [
			'path' => '/file/upload_zip/{guid}',
			'resource' => 'file/upload_zip',
			'middleware' => [
				Gatekeeper::class,
			],
		],
	],
	'views' => [
		'default' => [
			'js/jquery.serializejson.js' => $composer_path . 'vendor/bower-asset/jquery.serializeJSON/jquery.serializejson.min.js',
		],
	],
	'actions' => [
		'file_tools/groups/save_sort' => [],
		'file_tools/folder/edit' => [],
		'file_tools/folder/reorder' => [],
		'file_tools/upload/zip' => [],
		'file_tools/file/move' => [],
		'file_tools/bulk_delete' => [],
		'file_tools/bulk_download' => [],
	],
	'widgets' => [
		'file_tree' => [
			'name' => elgg_echo('widgets:file_tree:title'),
			'description' => elgg_echo('widgets:file_tree:description'),
			'context' => ['profile', 'dashboard', 'groups'],
			'multiple' => true,
		],
		'index_file' => [
			'name' => elgg_echo('collection:object:file'),
			'description' => elgg_echo('widgets:index_file:description'),
			'context' => ['index'],
			'multiple' => true,
		],
		'group_files' => [
			'name' => elgg_echo('collection:object:file:group'),
			'description' => elgg_echo('widgets:group_files:description'),
			'context' => ['groups'],
		],
	],
];
