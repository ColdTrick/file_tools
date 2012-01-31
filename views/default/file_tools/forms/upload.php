<?php
	/**
	 * Elgg file browser uploader
	 * 
	 * @package ElggFile
	 */

	global $CONFIG;
	
	$action = "file/upload";
	
	if (defined('ACCESS_DEFAULT'))
	{
		$access_id = ACCESS_DEFAULT;
	}
	else
	{
		$access_id = 0;
	}
	
	$container_guid = page_owner_entity()->getGUID();
?>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/swfupload/swfupload.js"></script>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/swfupload/swfupload.queue.js"></script>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/swfupload/fileprogress.js"></script>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/swfupload/handlers.js"></script>
<script type="text/javascript">


	function file_start_upload()
	{
		if(swfu.startUpload())
		{
			console.log('upload');
		}
		else
		{
			console.log('no upload');
		}

		return false;
	}

	function uploadComplete(file)
	{
		if (this.getStats().files_queued === 0)
		{
			var returnUrl = "<?php echo $vars["url"] . "pg/file/owner/" . page_owner_entity()->username . "#"; ?>";
			var folder_guid = $('#file_tools_file_parent_guid').val();
			window.location.href = returnUrl + folder_guid;
		}
	}
	
	var swfu;
	var filetypes = "<?php echo file_tools_allowed_extensions(true);?>";

	$(document).ready(function()
	{
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

		if($('#spanButtonPlaceHolder').length)
		{
			swfu = new SWFUpload(settings);
		}
		$('#file_tools_submit_file_upload').click(function(e)
		{		
			swfu.addPostParam('container_guid', '<?php echo page_owner(); ?>');
			swfu.addPostParam('access_id', 		$('#file_tools_file_access_id').val());
			swfu.addPostParam('tags', 			$('#file_tools_file_tags').val());
			swfu.addPostParam('folder_guid', 	$('#file_tools_file_parent_guid').val());
			swfu.startUpload();
			
			e.preventDefault();
		});
	});
</script>

<div class="contentWrapper">
	<form id="file_tools_file_upload_form" action="<?php echo $vars['url']; ?>action/<?php echo $action; ?>" enctype="multipart/form-data" method="post">
		<p>
			<label>
			<?php
				echo elgg_view('input/securitytoken');
				
				echo elgg_echo("file:file");
			?>
			<br />
						
			<div class="fieldset flash" id="fsUploadProgress">
				<span class="legend"><?php echo elgg_echo("file_tools:upload:form:info"); ?></span>
			</div>
			
			
			<div class="flash_wrapper"><span id="spanButtonPlaceHolder"></span></div>
			<input id="btnCancel" class="submit_button" type="button" value="<?php echo elgg_echo('Empty queue'); ?>" onclick="swfu.cancelQueue();" />
			
			</label>
		</p>
			
		<p>
			<?php
				echo "<input type=\"hidden\" name=\"container_guid\" value=\"{$container_guid}\" />";
				
				if (isset($vars['entity']))
				{
					echo "<input type=\"hidden\" name=\"file_guid\" value=\"{$vars['entity']->getGUID()}\" />";
				}	
			?>
		</p>

		
		<p>
			<label><?php echo elgg_echo("tags"); ?><br />
			<?php
				echo elgg_view("input/tags", array("internalname" => "tags", "value" => $tags, "internalid" => "file_tools_file_tags"));		
			?>
			</label>
		</p>

		<?php if(get_plugin_setting("user_folder_structure", "file_tools") == "yes"){?>
		<p>
			<label><?php echo elgg_echo("file_tools:forms:edit:parent"); ?><br />
			<?php
				echo elgg_view("input/folder_select", array("internalname" => "parent_guid", "value" => get_input('parent_guid'), "internalid" => "file_tools_file_parent_guid"));		
			?>
			</label>
		</p>
		<?php }?>
		<p>
			<label>
				<?php echo elgg_echo('access'); ?><br />
				<?php echo elgg_view('input/access', array('internalname' => 'access_id', 'value' => $access_id, 'internalid' => 'file_tools_file_access_id')); ?>
			</label>
		</p>
		
		<p>
			<input id="file_tools_submit_file_upload" type="submit" value="<?php echo elgg_echo("save"); ?>" />
		</p>
	</form>
</div>
