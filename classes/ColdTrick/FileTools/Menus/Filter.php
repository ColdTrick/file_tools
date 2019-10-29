<?php

namespace ColdTrick\FileTools\Menus;

class Filter {
	
	/**
	 * Add zip upload tab
	 *
	 * @param \Elgg\Hook $hook 'register', 'menu:filter'
	 *
	 * @return void|ElggMenuItem[]
	 */
	public static function addZipUpload(\Elgg\Hook $hook) {
		if (!$hook->getParam('identifier') === 'file') {
			return;
		}
		
		$segments = $hook->getParam('segments');
		
		if (!in_array(elgg_extract(0, $segments), ['upload_zip', 'add'])) {
			return;
		}
		
		$value = $hook->getValue();
		$value[] = \ElggMenuItem::factory([
			'name' => 'file',
			'text' => elgg_echo('add:object:file'),
			'href' => elgg_generate_url('add:object:file', [
				'guid' => $segments[1],
				'folder_guid' => get_input('folder_guid'),
			]),
		]);
		$value[] = \ElggMenuItem::factory([
			'name' => 'zip',
			'text' => elgg_echo('add:object:file:zip'),
			'href' => elgg_generate_url('add:object:file:zip', [
				'guid' => $segments[1],
				'folder_guid' => get_input('folder_guid'),
			]),
		]);
		
		return $value;
	}
}
