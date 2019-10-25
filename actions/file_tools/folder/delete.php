<?php

$folder_guid = (int) get_input('guid');
if (empty($folder_guid)) {
	return elgg_error_response(elgg_echo('error:missing_data'));
}

$folder = get_entity($folder_guid);
if (!$folder instanceof FileToolsFolder || !$folder->canDelete()) {
	return elgg_error_response(elgg_echo('actionunauthorized'));
}

if (!$folder->delete()) {
	return elgg_error_response(elgg_echo('file_tools:actions:delete:error:delete'));
}

return elgg_ok_response('', elgg_echo('file_tools:actions:delete:success'));
