<?php

namespace ColdTrick\FileTools\Menus;

class FolderSidebarTree {
	
	/**
	 * Set folder sidebar tree menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:file_tools_folder_sidebar_tree'
	 *
	 * @return void|ElggMenuItem[]
	 */
	public static function register(\Elgg\Hook $hook) {
		
		$return_value = $hook->getValue();
		
		$container = $hook->getParam('container', elgg_get_page_owner_entity());
		if (!($container instanceof \ElggUser) && !($container instanceof \ElggGroup)) {
			return;
		}
		
		if ($container instanceof \ElggGroup) {
			$root_href = elgg_generate_url('collection:object:file:group', [
				'guid' => $container->guid,
			]);
		} else {
			$root_href = elgg_generate_url('collection:object:file:owner', [
				'username' => $container->username,
			]);
		}
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'root',
			'text' => elgg_echo('file_tools:list:folder:main'),
			'href' => $root_href,
			'id' => 'folder-0',
			'rel' => 'root',
			'priority' => 0,
		]);
		
		$folders = elgg_get_entities([
			'type' => 'object',
			'subtype' => \FileToolsFolder::SUBTYPE,
			'container_guid' => $container->guid,
			'limit' => false,
			'batch' => true,
		]);
		
		/* @var $folder \ElggObject */
		foreach ($folders as $folder) {
			
			$parent_name = 'root';
			if ($folder->parent_guid) {
				$temp = get_entity($folder->parent_guid);
				if ($temp instanceof \FileToolsFolder) {
					$parent_name = "folder_{$temp->guid}";
				}
			}
			
			$return_value[] = \ElggMenuItem::factory([
				'name' => "folder_{$folder->guid}",
				'id' => "folder-{$folder->guid}",
				'text' => $folder->getDisplayName(),
				'href' => $folder->getURL(),
				'priority' => (int) $folder->order,
				'parent_name' => $parent_name,
			]);
		}
		
		return $return_value;
	}
}
