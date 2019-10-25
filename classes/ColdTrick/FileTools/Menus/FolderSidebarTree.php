<?php

namespace ColdTrick\FileTools\Menus;

class FolderSidebarTree {
	
	/**
	 * Set folder sidebar tree menu
	 *
	 * @param string         $hook        the name of the hook
	 * @param string         $type        the type of the hook
	 * @param ElggMenuItem[] $return_value current return value
	 * @param array          $params      supplied params
	 *
	 * @return void|ElggMenuItem[]
	 */
	public static function register($hook, $type, $return_value, $params) {
		
		$container = elgg_extract('container', $params, elgg_get_page_owner_entity());
		if (!($container instanceof \ElggUser) && !($container instanceof \ElggGroup)) {
			return;
		}
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'root',
			'text' => elgg_echo('file_tools:list:folder:main'),
			'href' => '#',
			'id' => '0',
			'rel' => 'root',
			'priority' => 0,
		]);
		
		$folders = new \ElggBatch('elgg_get_entities', [
			'type' => 'object',
			'subtype' => \FileToolsFolder::SUBTYPE,
			'container_guid' => $container->guid,
			'limit' => false,
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
				'text' => $folder->getDisplayName(),
				'href' => "#{$folder->guid}",
				'priority' => (int) $folder->order,
				'parent_name' => $parent_name,
			]);
		}
		
		return $return_value;
	}
}
