<?php
/**
 * Exposes settings to internet browser as an AMD module
 */

$settings = [
	'allowed_extensions' => file_tools_allowed_extensions(true),
	'readable_file_size_limit' => file_tools_get_readable_file_size_limit(),
];

?>
define(<?php echo json_encode($settings); ?>);