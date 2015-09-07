<?php
/**
 * Elgg file browser uploader
 *
 * @package ElggFile
 */

$page_owner = elgg_get_page_owner_entity();
$container_guid = $page_owner->getGUID();
$site_url = elgg_get_site_url();

// load CSS and JS
elgg_load_css("jquery.uploadify");
elgg_require_js("jquery.uploadify");
elgg_require_js('file_tools/uploadify');

?>

<fieldset>
	<div>
		<label><?php echo elgg_echo("file:file"); ?></label>

		<div id="uploadify-queue-wrapper" class="mbm">
			<span><?php echo elgg_echo("file_tools:upload:form:info"); ?></span>
		</div>

		<div>
			<?php
				echo elgg_view("input/file", array("id" => "uploadify-button-wrapper", "name" => "upload"));
				echo elgg_view("input/button", array("value" => elgg_echo('file_tools:forms:empty_queue'), "class" => "elgg-button-action hidden", "id" => "file-tools-uploadify-cancel"));
			?>
		</div>
	</div>

	<?php if (file_tools_use_folder_structure()) { ?>
	<div>
		<label><?php echo elgg_echo("file_tools:forms:edit:parent"); ?><br />
		<?php
			echo elgg_view("input/folder_select", array("name" => "folder_guid", "value" => get_input('parent_guid'), "id" => "file_tools_file_parent_guid"));
		?>
		</label>
	</div>
	<?php }?>

	<div>
		<label>
			<?php echo elgg_echo('access'); ?><br />
			<?php echo elgg_view('input/access', array('name' => 'access_id', 'id' => 'file_tools_file_access_id')); ?>
		</label>
	</div>

	<div class="elgg-foot">
		<?php
			echo elgg_view('input/securitytoken');
			echo elgg_view("input/hidden", array("name" => "container_guid", "value" => $container_guid));
			echo elgg_view("input/hidden", array("name" => "PHPSESSID", "value" => session_id()));

			echo elgg_view("input/submit", array("value" => elgg_echo("save")));
		?>
	</div>
</fieldset>
