<?php

namespace ColdTrick\FileTools\Menus;

class Title {
	
	/**
	 * Add folder_guid to file upload menu item
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:title'
	 *
	 * @return void|ElggMenuItem[]
	 */
	public static function updateFileAdd(\Elgg\Hook $hook) {
		$entity = $hook->getEntityParam();
		if (!$entity instanceof \FileToolsFolder) {
			return;
		}
		
		$add = $hook->getValue()->get('add');
		$add->setHref(elgg_http_add_url_query_elements($add->getHref(), [
			'folder_guid' => $entity->guid,
		]));
	}
}
