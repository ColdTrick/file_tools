<?php

$zip_filename = tempnam(sys_get_temp_dir(), 'download_');

$file_guids = get_input('file_guids');
$folder_guids = get_input('folder_guids');

if (empty($file_guids) && empty($folder_guids)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$zip = new ZipArchive();

if ($zip->open($zip_filename, ZIPARCHIVE::CREATE) !== true) {
	return elgg_error_response(elgg_echo('save:fail'));
}

// building the zip could take a while
set_time_limit(0);

// add files to the zip
if (!empty($file_guids)) {
	
	$files = elgg_get_entities([
		'type' => 'object',
		'subtype' => 'file',
		'guids' => $file_guids,
		'limit' => false,
		'batch' => true,
	]);
	/* @var $file ElggFile */
	foreach ($files as $file) {
		// check if the name exists in the zip
		if ($zip->statName($file->originalfilename) === false) {
			// doesn't exist, so add
			$zip->addFile($file->getFilenameOnFilestore(), $file->originalfilename);
		} else {
			// file name exists, so create a new one
			$ext_pos = strrpos($file->originalfilename, '.');
			$file_name = substr($file->originalfilename, 0, $ext_pos) . '_' . $file->guid . substr($file->originalfilename, $ext_pos);
			
			$zip->addFile($file->getFilenameOnFilestore(), $file_name);
		}
	}
}

// add folder (and their content) to the zip
if (!empty($folder_guids)) {
	$folders = elgg_get_entities([
		'type' => 'object',
		'subtype' => \FileToolsFolder::SUBTYPE,
		'guids' => $folder_guids,
		'limit' => false,
		'batch' => true,
	]);
	/* @var $folder ElggObject */
	foreach ($folders as $folder) {
		file_tools_add_folder_to_zip($zip, $folder);
	}
}

// done adding content, so save the zip
$zip->close();

if (!file_exists($zip_filename)) {
	return elgg_error_response(elgg_echo('save:fail'));
}

// output the correct headers
header('Pragma: public');
header('Content-type: application/zip');
header('Content-Disposition: attachment; filename="folder_contents.zip"');
header('Content-Length: ' . filesize($zip_filename));

ob_clean();
flush();
readfile($zip_filename);

// remove file from system
unlink($zip_filename);

exit();

