<?php

namespace ColdTrick\FileTools;

class ElggFile {
	
	/**
	 * Listen to the update event of a file
	 *
	 * @param \Elgg\Event $event 'create'|'update', 'object'
	 *
	 * @return void
	 */
	public static function setFolderGUID(\Elgg\Event $event) {
		$entity = $event->getObject();
		if (!$entity instanceof \ElggFile) {
			return;
		}
		
		$folder_guid = get_input('folder_guid', false);
		if ($folder_guid === false) {
			// folder_input was not present in form/action
			// maybe someone else did something with a file
			return;
		}
		
		$folder_guid = (int) $folder_guid;
		if (!empty($folder_guid)) {
			$folder = get_entity($folder_guid);
			if (!$folder instanceof \FileToolsFolder) {
				unset($folder_guid);
			}
		}
		
		// remove old relationships
		remove_entity_relationships($entity->guid, \FileToolsFolder::RELATIONSHIP, true);
		
		if (!empty($folder_guid)) {
			add_entity_relationship($folder_guid, \FileToolsFolder::RELATIONSHIP, $entity->guid);
		}
	}
}
