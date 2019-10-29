<?php

namespace ColdTrick\FileTools;

use Elgg\ViewsService;

class Views {
	
	/**
	 * Bypass file/owner and file/group resource
	 *
	 * @param \Elgg\Hook $hook 'view_vars', 'resources/file/owner'
	 *
	 * @return array
	 */
	public static function useFolderStructure(\Elgg\Hook $hook) {
		
		if (elgg_get_plugin_setting('use_folder_structure', 'file_tools') !== 'yes') {
			return;
		}
		
		$result = $hook->getValue();
		$result[ViewsService::OUTPUT_KEY] = elgg_view_resource('file_tools/file/owner', $result);
		return $result;
	}
}
