<?php

namespace ColdTrick\FileTools;

class Folder {
	
	/**
	 * Get the icon URL for a folder
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param string $return_value current return value
	 * @param array  $params       supplied params
	 *
	 * @retrun void|string
	 */
	public static function getIconURL($hook, $type, $return_value, $params) {
		
		$entity = elgg_extract('entity', $params);
		$size = elgg_extract('size', $params, 'small');
		if (!elgg_instanceof($entity, 'object', \FileToolsFolder::SUBTYPE)) {
			return;
		}
		
		switch ($size) {
			case 'topbar':
			case 'tiny':
			case 'small':
				return elgg_normalize_url("mod/file_tools/_graphics/folder/{$size}.png");
				break;
			default:
				return elgg_normalize_url('mod/file_tools/_graphics/folder/medium.png');
				break;
		}
	}
	
	/**
	 * Can create a folder in a group
	 *
	 * @param string $hook         the name of the hook
	 * @param string $type         the type of the hook
	 * @param string $retrun_value current return value
	 * @param array  $params       supplied params
	 *
	 * @retrun void|bool
	 */
	public static function canWriteToContainer($hook, $type, $return_value, $params) {
		
		$subtype = elgg_extract('subtype', $params);
		if ($subtype !== \FileToolsFolder::SUBTYPE) {
			return;
		}
		
		$container = elgg_extract('container', $params);
		$user = elgg_extract('user', $params);
		if (!($container instanceof \ElggGroup) || !($user instanceof \ElggUser)) {
			return;
		}
		
		if ($container->canEdit($user->guid)) {
			// admins, group owners and group admins can create folder all the time
			return true;
		}
		
		if (!$container->isMember($user)) {
			// user is not a group member
			return false;
		}
		
		if ($container->file_tools_structure_management_enable === 'no') {
			// file management is disabled
			return false;
		}
	}
}
