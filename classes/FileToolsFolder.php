<?php
/**
 * FileToolsFolder
 */
class FileToolsFolder extends \ElggObject {
	const SUBTYPE = 'folder';
	const RELATIONSHIP = 'folder_of';

	/**
	 * initializes the default class attributes
	 *
	 * @return void
	 */
	protected function initializeAttributes() {
		parent::initializeAttributes();

		$this->attributes['subtype'] = self::SUBTYPE;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function delete($recursive = true) {
		
		// remove subfolders
		$this->removeSubFolders();
		
		if (get_input('files') === 'yes') {
			// removed files in this folder
			$this->removeFolderContents();
		}
		
		return parent::delete($recursive);
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getURL() {
		$container = $this->getContainerEntity();
	
		if ($container instanceof \ElggGroup) {
			return "file/group/{$container->guid}/all#{$this->guid}";
		}
		
		return "file/owner/{$container->username}#{$this->guid}";
	}
	
	/**
	 * Remove all the child folders of the current folder
	 *
	 * @return void
	 */
	protected static function removeSubFolders() {
		
		$batch = new \ElggBatch('elgg_get_entities', [
			'type' => 'object',
			'subtype' => \FileToolsFolder::SUBTYPE,
			'container_guid' => $this->getContainerGUID(),
			'limit' => false,
			'metadata_name_value_pairs' => [
				'name' => 'parent_guid',
				'value' => $this->guid,
			],
			'wheres' => [
				"(e.guid <> {$this->guid})", // prevent deadloops
			],
		]);
		$batch->setIncrementOffset(false);
		foreach ($batch as $folder) {
			$folder->delete();
		}
	}
	
	/**
	 * Remove all the files in this folder
	 *
	 * @return void
	 */
	protected static function removeFolderContents() {
		
		$batch = new \ElggBatch('elgg_get_entities', [
			'type' => 'object',
			'subtype' => 'file',
			'container_guid' => $this->getContainerGUID(),
			'limit' => false,
			'relationship' => \FileToolsFolder::RELATIONSHIP,
			'relationship_guid' => $this->guid,
		]);
		$batch->setIncrementOffset(false);
		foreach ($batch as $file) {
			$file->delete();
		}
	}
}
