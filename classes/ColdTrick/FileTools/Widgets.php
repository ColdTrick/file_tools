<?php

namespace ColdTrick\FileTools;

class Widgets {
	
	/**
	 * get the URL of a widget
	 *
	 * @param \Elgg\Hook $hook 'entity:url', 'object'
	 *
	 * @return void|string
	 */
	public static function widgetGetURL(\Elgg\Hook $hook) {
		
		if (!empty($hook->getValue())) {
			// url already set
			return;
		}
		
		$widget = $hook->getEntityParam();
		if (!$widget instanceof \ElggWidget) {
			return;
		}
		
		$owner = $widget->getOwnerEntity();
		
		switch ($widget->handler) {
			case 'file_tree':
			case 'filerepo':
				if ($owner instanceof \ElggUser) {
					return elgg_generate_url('collection:object:file:owner', ['username' => $owner->username]);
				} elseif ($owner instanceof \ElggGroup) {
					return elgg_generate_url('collection:object:file:group', ['guid' => $owner->guid]);
				}
				
				break;
			case 'group_files':
				return elgg_generate_url('collection:object:file:group', ['guid' => $owner->guid]);
			case 'index_file':
				return elgg_generate_url('collection:object:file:all');
		}
	}
	
	/**
	 * Get widgets types
	 *
	 * @param \Elgg\Hook $hook 'handlers', 'widgets'
	 *
	 * @return void|\Elgg\WidgetDefinition[]
	 */
	public static function getHandlers(\Elgg\Hook $hook) {
		
		$page_owner = elgg_get_page_owner_entity();
		
		$context = $hook->getParam('context');
		
		$return_value = $hook->getValue();
		
		switch ($context) {

			case 'groups':
				
				if (($page_owner instanceof \ElggGroup) && ($page_owner->files_enable === 'no')) {
					// no files for this group
					break;
				}
				
				//'id' => 'file_tree',
				// 'id' => 'group_files',
				break;
			
		}
		
		return $return_value;
	}
}
