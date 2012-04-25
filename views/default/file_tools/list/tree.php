<?php 

	$folders = $vars["folders"]; 
	$folder = $vars["folder"];
	
	$selected_id = "file_tools_list_tree_main";
	if($folder instanceof ElggObject)
	{
		$selected_id = $folder->getGUID();
	}
	
	$page_owner = elgg_get_page_owner_entity();
?>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/jstree/jquery.tree.js"></script>
<script type="text/javascript" src="<?php echo $vars["url"]; ?>mod/file_tools/vendors/hashchange/jquery.hashchange.js"></script>

<script type="text/javascript">
	function file_tools_get_selected_tree_folder_id(){
		var result = 0;

		tree = jQuery.tree.reference($("#file_tools_list_tree"));
		result = file_tools_tree_folder_id(tree.selected);
		return result;
	}

	function file_tools_add_folder()
	{
		var parent_guid = $("#file_tools_list_tree a.clicked").attr("id");
		var forward_url = "<?php echo $vars["url"]; ?>file_tools/folder/new/<?php echo elgg_get_page_owner_guid();?>"
		
		if(parent_guid)
		{
			forward_url = forward_url + "?parent_guid=" + parent_guid;
		}
		
		document.location.href = forward_url;
	}
	
	function file_tools_reorder(folder_guid, parent_guid, order)
	{
		var reorder_url = "<?php echo $vars["url"];?>file_tools/reorder";
		$.post(reorder_url, {"folder_guid": folder_guid, "parent_guid": parent_guid, "order": order}, function()
		{
			file_tools_load_folder(file_tools_get_selected_tree_folder_id());
		});
	}
	
	function file_tools_load_folder(folder_guid)
	{
		var folder_url = "<?php echo $vars["url"];?>file_tools/list/<?php echo elgg_get_page_owner_guid();?>?folder_guid=" + folder_guid + "&search_viewtype=<?php echo get_input("search_viewtype", "list"); ?>";
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
		<?php if(elgg_get_plugin_setting("user_folder_structure", "file_tools") == "yes"){?>
		if(window.location.hash.substring(1) == '')
		{
			file_tools_load_folder(0);
		}

		$('#file_tools_list_new_folder_toggle').live('click', function()
		{
			var link = "<?php echo $vars["url"]; ?>file_tools/folder/new/<?php echo $page_owner->username; ?>";
			if(file_tools_get_selected_tree_folder_id() != undefined) {
				link = link + '?folder_guid=' + file_tools_get_selected_tree_folder_id();				
	    	}
			window.location = link;
			e.preventDefault();
		});
		
		$(window).hashchange(function()
		{
			file_tools_show_loader($("#file_tools_list_folder"));
			file_tools_load_folder(window.location.hash.substring(1));
		});
		
		$("a[href*='file_tools/file/new'], a[href*='file_tools/import/zip']").live("click",function(e)
		{
			var link = $(this).attr('href');
		
			window.location = link + '?folder_guid=' + file_tools_get_selected_tree_folder_id();
			e.preventDefault();
	        
		});
		<?php }?>
	
		$('.file_tools_load_folder').live('click', function()
		{
			folder_guid = $(this).attr('rel');
			file_tools_select_node(folder_guid);
		});
	
		$('select[name="file_sort"], select[name="file_sort_direction"]').change(function()
		{
			file_tools_show_loader($("#file_tools_list_folder"));
			var folder_url = "<?php echo $vars["url"];?>file_tools/list/<?php echo elgg_get_page_owner_guid();?>?folder_guid=" + file_tools_get_selected_tree_folder_id() + "&search_viewtype=<?php echo get_input("search_viewtype", "list"); ?>&sort_by=" + $('select[name="file_sort"]').val() + "&direction=" + $('select[name="file_sort_direction"]').val();
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
				
				$.getJSON("<?php echo $vars["url"]; ?>file_tools/proc/file/delete", {check: JSON.stringify(data)}, function(response)
				{
					$.each(response.deleted, function(i, guid)
					{
						$('div#file_' + guid).remove();
						$('#file_tools_list_tree li a#' + guid).parent().remove();
					});
		
					if(!response.valid)
					{
						alert('<?php echo elgg_echo("file_tools:list:alert:not_all_deleted"); ?>');
					}
				});
			}
			else
			{
				alert('<?php echo elgg_echo("file_tools:list:alert:none_selected"); ?>');
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
	
				window.location = '<?php echo $vars['url']; ?>file_tools/file/download?guids=' + data.join('-');
			}
			else
			{
				alert('<?php echo elgg_echo("file_tools:list:alert:none_selected"); ?>');
			}
		});
	
		var checked;
		$('#file_tools_select_all').click(function()
		{
			if(!checked)
			{
				$('input[name="file_tools_file_action_check"]').attr('checked', true);
				checked = true;
				$(this).html("<?php echo elgg_echo("file_tools:list:deselect_all"); ?>");
			}
			else
			{
				$('input[name="file_tools_file_action_check"]').attr('checked', false);
				checked = false;
				$(this).html("<?php echo elgg_echo("file_tools:list:select_all"); ?>");
			}
		});
	});

	function file_tools_show_loader(elem){
		var overlay_width = elem.outerWidth();
		var margin_left = elem.css("margin-left");
			
		$("#file_tools_list_files_overlay").css("width", overlay_width).css("left", margin_left).show();
	}
	
		
	
	$(function () 
	{		
		var requested_id = window.location.hash.substring(1);
		
		$("#file_tools_list_tree")
			.tree({
				"ui": {
					"theme_name": "classic"
				},
				"rules": {
					"multiple": false,
					"drag_copy": false,
					"valid_children" : [ "root" ]
				},
				"callback": {
					"onload" : function (tree) {
						if(requested_id == '')
						{
							tree.select_branch($("#file_tools_list_tree_main"));
							tree.open_branch($("#file_tools_list_tree_main"));
						}
						else
						{
							tree.select_branch($("#file_tools_tree_element_" + requested_id));
							tree.open_branch($("#file_tools_tree_element_" + requested_id));
							file_tools_load_folder(requested_id);
						}
					},
					"ondblclk": function(node, tree) {
						tree.open_branch(node);
						tree.open_all(node);
					},	
					"onselect": function(node, tree) {
						var folder_guid = file_tools_tree_folder_id(tree.get_node(node));
						
						if(folder_guid) {							
							window.location.hash = folder_guid;
						} else {
							folder_guid = 0;
							window.location.hash = "#";
						}
					},
					"onmove": function (node, ref_node, type, tree_obj, rb) {
						var parent_node = tree_obj.parent(node);
						
						var folder_guid = file_tools_tree_folder_id(tree_obj.get_node(node));
						var parent_guid = file_tools_tree_folder_id(parent_node, true);
						
						var order = parent_node.children("ul").children("li").children("a").makeDelimitedList("id");

						file_tools_reorder(folder_guid, parent_guid, order);
					}
				},
				types : {
					"default" : {
						deletable : false,
						renameable : false
						<?php if(!($page_owner->canEdit() || ($page_owner instanceof ElggGroup && $page_owner->isMember() && $page_owner->file_tools_structure_management_enable != "no"))){ ?>
						,draggable : false
						<?php } ?>
					},
					"root" : {
						draggable : false
					}
				}
		});
		
		<?php if($page_owner->canEdit() || ($page_owner instanceof ElggGroup && $page_owner->isMember())){ ?>
		$("#file_tools_list_tree a").droppable({
			"accept": ".file_tools_file",
			"hoverClass": "ui-state-hover",
			"tolerance": "pointer",
			"drop": function(event, ui) {
	
				var file_move_url = "<?php echo $vars["url"];?>file_tools/proc/file/move";
				var file_guid = $(ui.draggable).prev("input").val();
				if(file_guid == undefined)
				{
					file_guid = $(ui.draggable).attr('id').replace('file_','');
				}
				var folder_guid = $(this).attr("id");
				var selected_folder_guid = file_tools_get_selected_tree_folder_id();

				file_tools_show_loader($(ui.draggable));
				
				$(ui.draggable).hide();
				
				$.post(file_move_url, {"file_guid": file_guid, "folder_guid": folder_guid}, function(data)
				{
					file_tools_load_folder(selected_folder_guid);
				});
			},
			"greedy": true
		});
		<?php } ?>
	});

</script>

<div class="contentWrapper" id="file_tools_list_tree_container">
	<div id="file_tools_list_tree">
		<ul>
			<li id="file_tools_list_tree_main" rel="root">
				<a id="0" href="#"><?php echo elgg_echo("file_tools:list:folder:main"); ?></a>
				<?php 
					echo file_tools_display_folders($folders);
				?>
			</li>
		</ul>
	</div>
	
	<div class="clearfloat"></div>
	
	<?php 
		
		if($page_owner->canEdit() || ($page_owner instanceof ElggGroup && $page_owner->isMember() && $page_owner->file_tools_structure_management_enable != "no"))
		{ 
		?>
			<div>
			<?php
				echo elgg_view("input/button", array('value' => elgg_echo("file_tools:new:title"), 'id' => 'file_tools_list_new_folder_toggle')); 
			?>
			</div>
			<?php 
		}
		 
	?>
</div>

<div class="contentWrapper" id="file_tools_list_files_sort_options">

	<span><?php echo elgg_echo('file_tools:list:files:options:sort_title');?></span><br />
	
	<?php 
	$sort_value = 'e.time_created';
	if(is_array($_SESSION["file_tools"]) && !empty($_SESSION["file_tools"]["sort"])){
		$sort_value = $_SESSION["file_tools"]["sort"];
	} else {
		if($page_owner instanceof ElggGroup && !empty($page_owner->file_tools_sort)){
			$sort_value = $page_owner->file_tools_sort;
		} elseif($site_sort_default = elgg_get_plugin_setting("sort", "file_tools")){
			$sort_value = $site_sort_default;
		}
	}
	
	echo elgg_view('input/dropdown', array('name' => 'file_sort',
												'value' => $sort_value,
												'options_values' => array(
																	'oe.title' 			=> elgg_echo('title'), 
																	'oe.description'	=> elgg_echo('description'), 
																	'e.time_created' 	=> elgg_echo('file_tools:list:sort:time_created'), 
																	'simpletype' 		=> elgg_echo('file_tools:list:sort:type')))); ?>

	<?php 
	$sort_direction_value = 'asc';
	if(is_array($_SESSION["file_tools"]) && !empty($_SESSION["file_tools"]["sort_direction"])){
		$sort_direction_value = $_SESSION["file_tools"]["sort_direction"];
	} else {
		if($page_owner instanceof ElggGroup && !empty($page_owner->file_tools_sort_direction)){
			$sort_direction_value = $page_owner->file_tools_sort_direction;
		} elseif($site_sort_direction_default = elgg_get_plugin_setting("sort_direction", "file_tools")){
			$sort_direction_value = $site_sort_direction_default;
		}
	}
	
	echo elgg_view('input/dropdown', array('name' => 'file_sort_direction',
											'value' => $sort_direction_value,
												'options_values' => array(
																	'asc' 	=> elgg_echo('file_tools:list:sort:asc'), 
																	'desc'	=> elgg_echo('file_tools:list:sort:desc')))); ?><br />
</div>

<?php
if(elgg_is_logged_in())
{
	?>
	<div class="contentWrapper">
		<div id="file_tools_list_tree_info">
			<div><?php echo elgg_echo("file_tools:list:tree:info"); ?></div>
			<?php 
				echo elgg_echo("file_tools:list:tree:info:" . rand(1,12));
			?>
		</div>
	</div>
	<?php 
}
?>
