<?php
	/**
	 * Elgg file browser uploader
	 * 
	 * @package ElggFile
	 */

	$page_owner = elgg_extract("page_owner_entity", $vars, elgg_get_page_owner_entity());
	$container_guid = $page_owner->getGUID();
	
	if(elgg_instanceof($page_owner, "group", null, "ElggGroup")){
		$return_url = $vars["url"] . "file/group/" . $page_owner->getGUID();
	} else {
		$return_url = $vars["url"] . "file/owner/" . $page_owner->username;
	}
?>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/swfupload/swfupload.js"></script>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/swfupload/swfupload.queue.js"></script>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/swfupload/fileprogress.js"></script>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/swfupload/handlers.js"></script>
<script type="text/javascript">


	function file_start_upload() {
		if(swfu.startUpload()) {
			console.log('upload');
		} else {
			console.log('no upload');
		}

		return false;
	}

	function uploadComplete(file) {
		if (this.getStats().files_queued === 0) {
			var returnUrl = "<?php echo $return_url; ?>";
			var folder_guid = $('#file_tools_file_parent_guid').val();
			window.location.href = returnUrl + folder_guid;
		}
	}
	
	var swfu;
	var filetypes = "<?php echo file_tools_allowed_extensions(true);?>";

	$(document).ready(function() {
		var settings = {
			flash_url : "<?php echo $vars['url']; ?>mod/file_tools/vendors/swfupload/swfupload.swf",
			//upload_url: "<?php echo $vars['url']; ?>pg/file_tools/proc/upload/multi",
			upload_url: "<?php echo $vars['url']; ?>mod/file_tools/procedures/upload/multi.php",
			post_params: {
				"PHPSESSID" 	: '<?php echo session_id();?>'
			},
			file_size_limit : "200 MB",
			file_types : filetypes,
			file_upload_limit : 100, 
			file_queue_limit : 0,
			custom_settings : {
				progressTarget : "fsUploadProgress",
				cancelButtonId : "btnCancel"
			},
			prevent_swf_caching: true,
			debug: false,
			
			// Button settings
			button_width: "100",
			button_height: "25",
			button_placeholder_id: "spanButtonPlaceHolder",
			button_text: "<span class=\"selectFilesButton\"><?php echo elgg_echo('file_tools:forms:browse'); ?></span>",
			button_text_style: ".selectFilesButton { color: #FFFFFF; fontSize: 12; textAlign: left; fontFamily: Arial; fontWeight: bold; }",
			button_text_left_padding: 2,
			button_text_top_padding: 2,
			button_cursor : SWFUpload.CURSOR.HAND,
			button_window_mode : SWFUpload.WINDOW_MODE.TRANSPARENT,
			
			// The event handler functions are defined in handlers.js
			swfupload_preload_handler : preLoad,
			swfupload_load_failed_handler : loadFailed,
			file_queued_handler : fileQueued,
			file_queue_error_handler : fileQueueError,
			file_dialog_complete_handler : fileDialogComplete,
			upload_start_handler : uploadStart,
			upload_progress_handler : uploadProgress,
			upload_error_handler : uploadError,
			upload_success_handler : uploadSuccess,
			upload_complete_handler : uploadComplete,
			queue_complete_handler : queueComplete, // Queue plugin event
			swfupload_load_failed_handler: swfuploadLoadFailed
		
		};

		if($('#spanButtonPlaceHolder').length) {
			swfu = new SWFUpload(settings);
		}
		$('#file_tools_submit_file_upload').click(function(e) {		
			swfu.addPostParam('container_guid', '<?php echo $container_guid; ?>');
			swfu.addPostParam('access_id', 		$('#file_tools_file_access_id').val());
			swfu.addPostParam('tags', 			$('#file_tools_file_tags').val());
			swfu.addPostParam('folder_guid', 	$('#file_tools_file_parent_guid').val());
			swfu.startUpload();
			
			e.preventDefault();
		});
	});
</script>


<form id="file_tools_file_upload_form" action="<?php echo $vars['url']; ?>action/file/upload" enctype="multipart/form-data" method="post" class="elgg-form">
	<div>
		<label>
			<?php
				echo elgg_view('input/securitytoken');
				
				echo elgg_echo("file:file");
			?>
			<br />
						
			<div class="fieldset flash" id="fsUploadProgress">
				<span class="legend"><?php echo elgg_echo("file_tools:upload:form:info"); ?></span>
			</div>
			
			
			<div class="flash_wrapper">
				<span id="spanButtonPlaceHolder"></span>
			</div>
			
			<div class="clearfix"></div>
			
			<input id="btnCancel" class="elgg-button elgg-button-action" type="button" value="<?php echo elgg_echo('file_tools:forms:empty_queue'); ?>" onclick="swfu.cancelQueue();" />
		
		</label>
	</div>
	
	<div>
		<?php
			echo elgg_view("input/hidden", array("name" => "container_guid", "value" => $container_guid));	
			
			if (isset($vars['entity'])){
				echo elgg_view("input/hidden", array("name" => "file_guid", "value" => $vars['entity']->getGUID()));
			}	
		?>
	</div>

	
	<div>
		<label><?php echo elgg_echo("tags"); ?><br />
		<?php
			echo elgg_view("input/tags", array("name" => "tags", "value" => $tags, "id" => "file_tools_file_tags"));		
		?>
		</label>
	</div>

	<?php if(elgg_get_plugin_setting("user_folder_structure", "file_tools") == "yes"){ ?>
	<div>
		<label><?php echo elgg_echo("file_tools:forms:edit:parent"); ?><br />
		<?php
			echo elgg_view("input/folder_select", array("internalname" => "parent_guid", "value" => get_input('parent_guid'), "internalid" => "file_tools_file_parent_guid"));		
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
		<?php echo elgg_view("input/submit", array("value" => elgg_echo("save"), "id" => "file_tools_submit_file_upload")); ?>
	</div>
</form>
