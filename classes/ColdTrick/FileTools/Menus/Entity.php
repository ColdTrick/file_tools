<?php

namespace ColdTrick\FileTools\Menus;

class Entity {
	
	/**
	 * Add items to the file entity menu
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:entity'
	 *
	 * @return void|ElggMenuItem[]
	 */
	public static function registerFile(\Elgg\Hook $hook) {
		
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \ElggFile) {
			return;
		}
		
		$return_value = $hook->getValue();
		
		$return_value[] = \ElggMenuItem::factory([
			'name' => 'download',
			'icon' => 'download',
			'text' => elgg_echo('download'),
			'href' => elgg_get_download_url($entity),
			'priority' => 200,
		]);
		
		return $return_value;
	}
}
