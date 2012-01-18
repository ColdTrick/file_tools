<?php
$user = get_loggedin_user();

global $fancybox_js_loaded;
if(empty($fancybox_js_loaded))
{
$fancybox_js_loaded = true;
?>
<script type="text/javascript" src="<?php echo $vars["url"];?>mod/file_tools/vendors/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<?php }?>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/jstree/jquery.tree.js"></script>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/swfupload/swfupload.js"></script>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/swfupload/swfupload.queue.js"></script>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/swfupload/fileprogress.js"></script>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/swfupload/handlers.js"></script>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/hashchange/jquery.hashchange.js"></script>
<script type="text/javascript">

function file_tools_add_folder()
{
	var parent_guid = $("#file_tools_list_tree a.clicked").attr("id");
	var forward_url = "<?php echo $vars["url"]; ?>pg/file_tools/folder/new/<?php echo page_owner();?>"
	
	if(parent_guid)
	{
		forward_url = forward_url + "?parent_guid=" + parent_guid;
	}
	
	document.location.href = forward_url;
}

function file_tools_reorder(folder_guid, parent_guid, order)
{
	var reorder_url = "<?php echo $vars["url"];?>pg/file_tools/reorder";
	$.post(reorder_url, {"folder_guid": folder_guid, "parent_guid": parent_guid, "order": order}, function()
	{
		file_tools_load_folder($('input[name="file_tools_parent_guid"]').val());
	});
}

function file_tools_load_folder(folder_guid)
{
	$('input[name="file_tools_parent_guid"]').val(folder_guid);
	
	var folder_url = "<?php echo $vars["url"];?>pg/file_tools/list/<?php echo page_owner();?>?folder_guid=" + folder_guid + "&search_viewtype=<?php echo get_input("search_viewtype", "list"); ?>";
	$("#file_tools_list_files_container").load(folder_url);
}	

function file_tools_remove_folder_files(link)
{
	if(confirm("<?php echo elgg_echo("file_tools:folder:delete:confirm_files");?>"))
	{
		var cur_href = $(link).attr("href"); 
		$(link).attr("href", cur_href + "&files=yes");
	}
	return true;
}

function file_tools_show_form(form)
{
	$('.file_tools_form_toggle').hide();
	$('#'+form).toggle();
}

function file_tools_tree_folder_id(node, parent)
{
	if(parent == true)
	{
		var find = "a:first";
	}
	else
	{
		var find = "a";
	}
	
	var element_id = node.find(find).attr("id");
	return element_id.substring(24, element_id.length);
}

function file_tools_select_node(folder_guid, tree)
{
	tree = jQuery.tree.reference($("#file_tools_list_tree"));
	
	tree.select_branch($("#file_tools_tree_element_" + folder_guid));
	tree.open_branch($("#file_tools_tree_element_" + folder_guid));
}

$(function()
{
	<?php if(get_plugin_setting("user_folder_structure", "file_tools") != "no"){?>
	if(window.location.hash.substring(1) == '')
	{
		file_tools_load_folder(0);
	}
	
	$(window).hashchange(function()
	{
		file_tools_load_folder(window.location.hash.substring(1));
	});
	
	$('#inline_fancy_test').fancybox({
		width: 400, 
		height: 350, 
		autoDimensions: false,
		onComplete: function()
		{
			$('#file_tools_file_parent_guid').val($("#file_tools_list_tree a.clicked").attr("id").replace('file_tools_tree_element_', ''));
		}
	});

	$('a').click(function(e)
	{
		var link = $(this).attr('href');
		var i = (link + '').indexOf('pg/file_tools/file/new');

	    if(i === -1){}else
	    {
	    	//$('#inline_fancy_test').click();
	    	if($('input[name="file_tools_parent_guid"]').val() != undefined)
	    	{
				window.location = link + '?folder_guid=' + $('input[name="file_tools_parent_guid"]').val();
				e.preventDefault();
	    	}
	    }

		var i = (link + '').indexOf('pg/file_tools/import/zip');
	    if(i === -1){}else
	    {
	    	if($('input[name="file_tools_parent_guid"]').val() != undefined)
	    	{
				window.location = link + '?folder_guid=' + $('input[name="file_tools_parent_guid"]').val();
				e.preventDefault();
	    	}
	    }
	});
	<?php }?>

	$('#file_tools_list_new_folder_toggle').live('click', function()
	{
		file_tools_show_form('file_tools_list_new_folder');
	});

	$('.file_tools_close_form').live('click', function()
	{
		$(this).parent().hide();
	});

	$('.file_tools_load_folder').live('click', function()
	{
		folder_guid = $(this).attr('rel');
		file_tools_select_node(folder_guid);
	});

	$('select[name="file_view_time"]').change(function()
	{
		time_option = $(this).val();

		var folder_url = "<?php echo $vars["url"];?>pg/file_tools/list/<?php echo page_owner();?>?folder_guid=" + $('input[name="file_tools_parent_guid"]').val() + "&search_viewtype=<?php echo get_input("search_viewtype", "list"); ?>&sort_by=" + $('select[name="file_sort"]').val() + "&direction=" + $('select[name="file_sort_direction"]').val() + "&time_option=" + time_option;
		$("#file_tools_list_files_container").load(folder_url);
	});

	$('select[name="file_sort"], select[name="file_sort_direction"]').change(function()
	{
		var folder_url = "<?php echo $vars["url"];?>pg/file_tools/list/<?php echo page_owner();?>?folder_guid=" + $('input[name="file_tools_parent_guid"]').val() + "&search_viewtype=<?php echo get_input("search_viewtype", "list"); ?>&sort_by=" + $('select[name="file_sort"]').val() + "&direction=" + $('select[name="file_sort_direction"]').val();
		$("#file_tools_list_files_container").load(folder_url);
	});

	$('a#file_tools_action_bulk_delete').click(function()
	{
		checkboxes = $('input[name="file_tools_file_action_check"]:checked');
		
		if(checkboxes.length)
		{
			if(!confirm('<?php echo elgg_echo('question:areyousure');?>'))
			{
				return false;
			}
				
			data = [];
			$.each($('input[name="file_tools_file_action_check"]:checked'), function(i, value)
			{
				data.push($(value).val());
			});
			
			$.getJSON("<?php echo $vars["url"]; ?>pg/file_tools/proc/file/delete", {check: JSON.stringify(data)}, function(response)
			{
				$.each(response.deleted, function(i, guid)
				{
					$('div#file_' + guid).remove();
					$('#file_tools_list_tree li a#' + guid).parent().remove();
				});
	
				if(!response.valid)
				{
					alert('Not all files could be deleted.');
				}
			});
		}
		else
		{
			alert('First select some files.');
		}
	});

	$('a#file_tools_action_bulk_download').click(function()
	{		
		checkboxes = $('input[name="file_tools_file_action_check"]:checked');
		
		if(checkboxes.length)
		{				
			data = [];
			$.each($('input[name="file_tools_file_action_check"]:checked'), function(i, value)
			{
				data.push($(value).val());
			});

			window.location = '<?php echo $vars['url']; ?>pg/file_tools/file/download?guids=' + data.join('-');
		}
		else
		{
			alert('First select some files.');
		}
	});


	$('#file_tools_submit_file_upload').click(function(e)
	{		
		swfu.addPostParam('container_guid', '<?php echo page_owner(); ?>');
		swfu.addPostParam('access_id', 		$('#file_tools_file_access_id').val());
		swfu.addPostParam('tags', 			$('#file_tools_file_tags').val());
		//swfu.addPostParam('folder_guid', 	$("#file_tools_list_tree a.clicked").attr("id"));
		swfu.addPostParam('folder_guid', 	$('#file_tools_file_parent_guid').val());
		swfu.startUpload();
		
		e.preventDefault();
	});
	
	var checked;
	$('#file_tools_select_all').click(function()
	{
		if(!checked)
		{
			$('input[name="file_tools_file_action_check"]').attr('checked', true);
			checked = true;
			$(this).html('Deselect all');
		}
		else
		{
			$('input[name="file_tools_file_action_check"]').attr('checked', false);
			checked = false;
			$(this).html('Select all');
		}
	});
});

<?php
	$filetypes = file_tools_allowed_extensions(true);
?>

var swfu;
var filetypes = "<?php echo $filetypes;?>";

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
		button_text: "<span class=\"submit_button\"><?php echo elgg_echo('file_tools:forms:browse'); ?></span>",
		button_text_style: ".submit_button { color: #000000; fontSize: 12; textAlign: left; fontFamily: Arial; fontWeight: bold; }",
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
});



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
		folder_guid = $("#file_tools_list_tree a.clicked").attr("id");
		window.location.reload(true);
	}
}

</script>