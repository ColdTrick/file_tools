<?php

namespace ColdTrick\FileTools\Menus;

class FolderBreadcrumb {
	
	/**
	 * Set folder breadcrumb menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:file_tools_folder_breadcrumb'
	 *
	 * @return void|ElggMenuItem[]
	 */
	public static function register(\Elgg\Hook $hook) {
		
		$container = elgg_get_page_owner_entity();
		
		$return_value = $hook->getValue();
		
		/* @var $folder \ElggObject */
		$folder = $hook->getEntityParam();
		if ($folder instanceof \FileToolsFolder) {
			$container = $folder->getContainerEntity();
			
			$priority = 9999999;
			
			$parent_guid = (int) $folder->parent_guid;
			while (!empty($parent_guid)) {
				$parent = get_entity($parent_guid);
				if (!$parent instanceof \FileToolsFolder) {
					break;
				}
				
				$priority--;
				
				$return_value[] = \ElggMenuItem::factory([
					'name' => "folder_{$parent->guid}",
					'text' => $parent->getDisplayName(),
					'href' => $parent->getURL(),
					'priority' => $priority,
				]);
				$parent_guid = (int) $parent->parent_guid;
			}
		}
		
		// make main folder item
		$main_folder_options = [
			'name' => 'main_folder',
			'text' => elgg_echo('file_tools:list:folder:main'),
			'priority' => 0,
		];
		
		if ($container instanceof \ElggGroup) {
			$main_folder_options['href'] = elgg_generate_url('collection:object:file:group', [
				'guid' => $container->guid,
			]);
		} else {
			$main_folder_options['href'] = elgg_generate_url('collection:object:file:owner', [
				'username' => $container->username,
			]);
		}
		
		$return_value[] = \ElggMenuItem::factory($main_folder_options);
		
		return $return_value;
	}
}
