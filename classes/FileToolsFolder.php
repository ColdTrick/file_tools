<?php
use Elgg\Database\QueryBuilder;

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
			return elgg_generate_url('collection:object:file:group', [
				'guid' => $container->guid,
				'folder_guid' => $this->guid,
			]);
		}
		
		return elgg_generate_url('collection:object:file:owner', [
			'username' => $container->username,
			'folder_guid' => $this->guid,
		]);
	}
	
	/**
	 * Recursivly change the access of subfolders (and files)
	 *
	 * @param bool $change_files include files in this folder (default: false)
	 *
	 * @return void
	 */
	public function updateChildAccess($change_files = false) {
		// get children folders
		$children = elgg_get_entities([
			'type' => 'object',
			'subtype' => \FileToolsFolder::SUBTYPE,
			'container_guid' => $this->getContainerGUID(),
			'limit' => false,
			'metadata_name_value_pairs' => [
				'parent_guid' => $this->guid,
			],
			'batch' => true,
		]);
		/* @var $child ElggObject */
		foreach ($children as $child) {
			$child->access_id = $this->access_id;
			$child->save();
			
			$child->updateChildAccess($change_files);
		}
		
		if ($change_files) {
			// change access on files in this folder
			$this->updateFileAccess();
		}
	}

	/**
	 * Change the access of all file in a folder
	 *
	 * @return void
	 */
	public function updateFileAccess() {
		// change access on files in this folder
		$files = elgg_get_entities([
			'type' => 'object',
			'subtype' => 'file',
			'container_guid' => $this->getContainerGUID(),
			'limit' => false,
			'relationship' => FileToolsFolder::RELATIONSHIP,
			'relationship_guid' => $this->guid,
			'batch' => true,
		]);
		
		// need to unregister an event listener
		elgg_unregister_event_handler('update', 'object', '\ColdTrick\FileTools\ElggFile::setFolderGUID');
		
		/* @var $file ElggFile */
		foreach ($files as $file) {
			$file->access_id = $this->access_id;
			$file->save();
		}
	}
	
	/**
	 * Remove all the child folders of the current folder
	 *
	 * @return void
	 */
	protected function removeSubFolders() {
		
		$batch = elgg_get_entities([
			'type' => 'object',
			'subtype' => \FileToolsFolder::SUBTYPE,
			'container_guid' => $this->getContainerGUID(),
			'limit' => false,
			'metadata_name_value_pairs' => [
				'name' => 'parent_guid',
				'value' => $this->guid,
			],
			'wheres' => [
				function (QueryBuilder $qb, $main_alias) {
					// prevent deadloops
					return $qb->compare("{$main_alias}.guid", '<>', $this->guid);
				},
			],
			'batch' => true,
			'batch_inc_offset' => false,
		]);
		
		foreach ($batch as $folder) {
			$folder->delete();
		}
	}
	
	/**
	 * Remove all the files in this folder
	 *
	 * @return void
	 */
	protected function removeFolderContents() {
		
		$batch = elgg_get_entities([
			'type' => 'object',
			'subtype' => 'file',
			'container_guid' => $this->getContainerGUID(),
			'limit' => false,
			'relationship' => \FileToolsFolder::RELATIONSHIP,
			'relationship_guid' => $this->guid,
			'batch' => true,
			'batch_inc_offset' => false,
		]);
		
		foreach ($batch as $file) {
			$file->delete();
		}
	}
}
