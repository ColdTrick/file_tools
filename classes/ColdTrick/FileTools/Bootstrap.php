<?php

namespace ColdTrick\FileTools;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
	
	/**
	 * {@inheritdoc}
	 */
	public function init() {

		elgg_register_css('jstree', elgg_get_simplecache_url('js/jstree/themes/default/style.min.css'));
		
		// register page handler for nice URL's
		elgg_register_page_handler('file_tools', '\ColdTrick\FileTools\PageHandler::fileTools');
		
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
		elgg_extend_view('css/elgg', 'css/file_tools/site.css');
		elgg_extend_view('groups/edit', 'file_tools/group_settings');
	
		// register ajax views
		elgg_register_ajax_view('object/folder/file_tree_content');
	}

	/**
	 * Init events
	 *
	 * @return void
	 */
	protected function initEvents() {
		elgg_register_event_handler('create', 'object', '\ColdTrick\FileTools\ElggFile::create');
		elgg_register_event_handler('update', 'object', '\ColdTrick\FileTools\ElggFile::update');
	}
	
	/**
	 * Register plugin hooks
	 *
	 * @return void
	 */
	protected function initRegisterHooks() {
		$hooks = $this->elgg()->hooks;
		
		$hooks->registerHandler('entity:icon:url', 'object', '\ColdTrick\FileTools\Folder::getIconURL');
		$hooks->registerHandler('container_permissions_check', 'object', '\ColdTrick\FileTools\Folder::canWriteToContainer');
		$hooks->registerHandler('route', 'file', '\ColdTrick\FileTools\Router::file');
		
		$hooks->registerHandler('entity:url', 'object', '\ColdTrick\FileTools\Widgets::wigetGetURL');
		$hooks->registerHandler('handlers', 'widgets', '\ColdTrick\FileTools\Widgets::getHandlers');
		
		$hooks->registerHandler('register', 'menu:entity', '\ColdTrick\FileTools\Menus\Entity::registerFile');
		$hooks->registerHandler('register', 'menu:file_tools_folder_breadcrumb', '\ColdTrick\FileTools\Menus\FolderBreadcrumb::register');
		$hooks->registerHandler('register', 'menu:file_tools_folder_sidebar_tree', '\ColdTrick\FileTools\Menus\FolderSidebarTree::register');
		
		$hooks->registerHandler('tool_options', 'group', '\ColdTrick\FileTools\Groups::tools');
	}
}
