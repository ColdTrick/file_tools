<?php

namespace ColdTrick\FileTools;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritdoc}
	 */
	public function init() {
		$this->initViews();
		$this->initEvents();
		$this->initRegisterHooks();
	}

	/**
	 * Init views
	 *
	 * @return void
	 */
	protected function initViews() {
		// extend views
		elgg_extend_view('groups/edit', 'file_tools/group_settings');
		elgg_extend_view('elgg.css', 'file_tools/site.css');
	
		// register ajax views
		elgg_register_ajax_view('object/folder/file_tree_content');
		elgg_register_ajax_view('file_tools/list/files');
		elgg_register_ajax_view('forms/file_tools/folder/edit');
	}

	/**
	 * Init events
	 *
	 * @return void
	 */
	protected function initEvents() {
		elgg_register_event_handler('create', 'object', '\ColdTrick\FileTools\ElggFile::setFolderGUID');
		elgg_register_event_handler('update', 'object', '\ColdTrick\FileTools\ElggFile::setFolderGUID');
	}
	
	/**
	 * Register plugin hooks
	 *
	 * @return void
	 */
	protected function initRegisterHooks() {
		$hooks = $this->elgg()->hooks;

		$hooks->registerHandler('view_vars', 'resources/file/owner', '\ColdTrick\FileTools\Views::useFolderStructure');
		$hooks->registerHandler('prepare', 'menu:file_tools_folder_sidebar_tree', '_elgg_setup_vertical_menu');
		$hooks->registerHandler('entity:url', 'object', '\ColdTrick\FileTools\Widgets::widgetGetURL');
// 		$hooks->registerHandler('handlers', 'widgets', '\ColdTrick\FileTools\Widgets::getHandlers');
		
		$hooks->registerHandler('register', 'menu:entity', '\ColdTrick\FileTools\Menus\Entity::registerFile');
		$hooks->registerHandler('register', 'menu:file_tools_folder_breadcrumb', '\ColdTrick\FileTools\Menus\FolderBreadcrumb::register');
		$hooks->registerHandler('register', 'menu:file_tools_folder_sidebar_tree', '\ColdTrick\FileTools\Menus\FolderSidebarTree::register');
// 		$hooks->registerHandler('register', 'menu:filter', '\ColdTrick\FileTools\Menus\Filter::addZipUpload');
		$hooks->registerHandler('register', 'menu:title', '\ColdTrick\FileTools\Menus\Title::updateFileAdd');
		
		$hooks->registerHandler('tool_options', 'group', '\ColdTrick\FileTools\Groups::tools');
	}
}
