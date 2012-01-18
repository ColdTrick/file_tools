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
<div class="contentWrapper">
	<form id="file_tools_file_upload_form" action="<?php echo $vars['url']; ?>action/<?php echo $action; ?>" enctype="multipart/form-data" method="post">
		<p>
			<label>
			<?php
				echo elgg_view('input/securitytoken');
				
				echo elgg_echo("file:file");
			?>
			<br />
			<?php
				//echo elgg_view("input/file",array('internalname' => 'upload', 'internalid' => 'swfUpload'));			
			?>
			
			<div class="fieldset flash" id="fsUploadProgress">
				<span class="legend"></span>
			</div>
			
			<div>
				<div class="flash_wrapper"><span id="spanButtonPlaceHolder"></span></div>
				<input id="btnCancel" class="submit_button" type="button" value="<?php echo elgg_echo('Empty queue'); ?>" onclick="swfu.cancelQueue();" />
				
			</div>
			
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

		
		<p>
			<label><?php echo elgg_echo("file_tools:forms:edit:parent"); ?><br />
			<?php
				echo elgg_view("input/folder_select", array("internalname" => "parent_guid", "value" => get_input('parent_guid'), "internalid" => "file_tools_file_parent_guid"));		
			?>
			</label>
		</p>
		
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
