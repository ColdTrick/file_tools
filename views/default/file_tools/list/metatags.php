<?php ?>
<script src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/jstree/jquery.tree.min.js"></script>
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
		$.post(reorder_url, {"folder_guid": folder_guid, "parent_guid": parent_guid, "order": order});		
	}
	
	function file_tools_load_folder(folder_guid)
	{
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

$(function()
{
	$('a').click(function(e)
	{
		link = $(this).attr('href');

		var i = (link + '').indexOf('pg/file/new');
	    if(i === -1){}else
	    {
			var parent_guid = $("#file_tools_list_tree a.clicked").attr("id");
	
			document.location.href = link + '?folder_guid=' + parent_guid;
			
			e.preventDefault();
	    }

		var i = (link + '').indexOf('pg/file_tools/import/zip');
	    if(i === -1){}else
	    {
			var parent_guid = $("#file_tools_list_tree a.clicked").attr("id");
	
			document.location.href = link + '?folder_guid=' + parent_guid;
			
			e.preventDefault();
	    }
	});


	$('.file_tools_load_folder').live('click', function()
	{
		folder_guid = $(this).attr('rel');
		
		$("#file_tools_list_tree a").removeClass('clicked');

		$('#'+folder_guid).addClass('clicked');

		if($('#'+folder_guid).parent().hasClass('closed'))
		{
			$('#'+folder_guid).parent().removeClass('closed');
			$('#'+folder_guid).parent().addClass('open');
		}		
		
		file_tools_load_folder(folder_guid);
	});

	$('select[name="file_view_time"]').change(function()
	{
		time_option = $(this).val();

		var folder_url = "<?php echo $vars["url"];?>pg/file_tools/list/<?php echo page_owner();?>?folder_guid=" + $("#file_tools_list_tree a.clicked").attr("id") + "&search_viewtype=<?php echo get_input("search_viewtype", "list"); ?>&sort_by=" + $('select[name="file_sort"]').val() + "&direction=" + $('select[name="file_sort_direction"]').val() + "&time_option=" + time_option;
		$("#file_tools_list_files_container").load(folder_url);
	});

	$('select[name="file_sort"], select[name="file_sort_direction"]').change(function()
	{
		var folder_url = "<?php echo $vars["url"];?>pg/file_tools/list/<?php echo page_owner();?>?folder_guid=" + $("#file_tools_list_tree a.clicked").attr("id") + "&search_viewtype=<?php echo get_input("search_viewtype", "list"); ?>&sort_by=" + $('select[name="file_sort"]').val() + "&direction=" + $('select[name="file_sort_direction"]').val();
		$("#file_tools_list_files_container").load(folder_url);
	});
});
</script>