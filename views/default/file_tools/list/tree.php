<?php 

	$folders = $vars["folders"]; 
	$folder = $vars["folder"];
	
	$selected_id = "file_tools_list_tree_main";
	if($folder instanceof ElggObject)
	{
		$selected_id = $folder->getGUID();
	}
	
	$page_owner = page_owner_entity();
?>
<script type="text/javascript">

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
					},
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
			"accept": ".file_tools_file, .file_tools_folder",
			"hoverClass": "ui-state-hover",
			"tolerance": "pointer",
			"drop": function(event, ui) {
	
				var file_move_url = "<?php echo $vars["url"];?>pg/file_tools/proc/file/move";
				var file_guid = $(ui.draggable).prev("input").val();
				if(file_guid == undefined)
				{
					file_guid = $(ui.draggable).attr('id').replace('file_','');
				}
				var folder_guid = $(this).attr("id");
				var selected_folder_guid = $('input[name="file_tools_parent_guid"]').val();
				var overlay_width = $(ui.draggable).outerWidth();
				var margin_left = $(ui.draggable).css("margin-left");

				// dragged
				console.log('dragged guid: ' + file_guid);

				//dropped on
				console.log('dropped guid: ' + folder_guid);
				
				if(file_guid == folder_guid)
				{
					$(ui.draggable).hide();
					
					alert('<?php echo elgg_echo('file_tools:action:move:parent_error'); ?>');
					
					$("#file_tools_list_files_overlay").css("width", overlay_width).css("left", margin_left).show();
					
					file_tools_load_folder(selected_folder_guid);
				}
				else
				{
					$(ui.draggable).hide();
					
					$("#file_tools_list_files_overlay").css("width", overlay_width).css("left", margin_left).show();
					
					$.post(file_move_url, {"file_guid": file_guid, "folder_guid": folder_guid}, function(data)
					{
						file_tools_load_folder(selected_folder_guid);
					});
				}
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
				echo elgg_view("input/button", array('value' => elgg_echo("file_tools:new:title"), 'internalid' => 'file_tools_list_new_folder_toggle')); 
			?>
			</div>
			<?php 
		}
		 
	?>
</div>

<div class="contentWrapper" id="file_tools_list_files_sort_options">

	<span><?php echo elgg_echo('file_tools:list:files:options:sort_title');?></span><br />
	
	<?php echo elgg_view('input/pulldown', array('internalname' => 'file_sort',
												'options_values' => array(
																	'oe.title' 			=> elgg_echo('title'), 
																	'oe.description'	=> elgg_echo('description'), 
																	'e.time_created' 	=> elgg_echo('file_tools:list:sort:time_created'), 
																	'simpletype' 		=> elgg_echo('file_tools:list:sort:type')))); ?>

	<?php echo elgg_view('input/pulldown', array('internalname' => 'file_sort_direction',
												'options_values' => array(
																	'asc' 	=> elgg_echo('file_tools:list:sort:asc'), 
																	'desc'	=> elgg_echo('file_tools:list:sort:desc')))); ?><br />
</div>

<?php
if(isloggedin())
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
