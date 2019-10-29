<?php

namespace ColdTrick\FileTools;

use Elgg\Groups\Tool;

class Groups {
	
	/**
	 * Add the folder management option to groups (if enabled)
	 *
	 * @param \Elgg\Hook $hook         the name of the hook
	 *
	 * @return void|array
	 */
	public static function tools(\Elgg\Hook $hook) {
		
		if (elgg_get_plugin_setting('use_folder_structure', 'file_tools') !== 'yes') {
			return;
		}
		
		$return_value = $hook->getValue();
		
		$return_value[] = new Tool('file_tools_structure_management', [
			'label' => elgg_echo('file_tools:group_tool_option:structure_management'),
		]);;
		
		return $return_value;
	}
}
