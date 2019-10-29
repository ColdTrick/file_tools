<?php
/**
 * File renderer
 *
 * @uses $vars['entity'] ElggFile to show
 */

$full = elgg_extract('full_view', $vars, false);
$entity = elgg_extract('entity', $vars, false);

if (!$entity instanceof \ElggFile) {
	return;
}


// which time format to show
$time_preference = 'date';

if ($user_time_preference = elgg_get_plugin_user_setting('file_tools_time_display', 0, 'file_tools')) {
	$time_preference = $user_time_preference;
} elseif ($site_time_preference = elgg_get_plugin_setting('file_tools_default_time_display', 'file_tools')) {
	$time_preference = $site_time_preference;
}

if ($time_preference == 'date') {
	$date = date(elgg_echo('friendlytime:date_format'), $entity->time_created);
} else {
	$date = elgg_view_friendly_time($entity->time_created);
}

if ($full && !elgg_in_context('gallery')) {
	$mime = $entity->getMimeType();
	$base_type = substr($mime, 0, strpos($mime, '/'));

	$extra = '';
	if (elgg_view_exists("file/specialcontent/$mime")) {
		$extra = elgg_view("file/specialcontent/$mime", $vars);
	} elseif (elgg_view_exists("file/specialcontent/$base_type/default")) {
		$extra = elgg_view("file/specialcontent/$base_type/default", $vars);
	}

	$body = elgg_view('output/longtext', ['value' => $entity->description]);

	$params = [
		'show_summary' => true,
		'icon_entity' => $entity->getOwnerEntity(),
		'body' => $body,
		'attachments' => $extra,
		'show_responses' => elgg_extract('show_responses', $vars, false),
		'show_navigation' => true,
	];
	$params = $params + $vars;
	
	echo elgg_view('object/elements/full', $params);
} elseif (elgg_in_context('gallery')) {
	echo '<div class="file-gallery-item">';
	echo "<h3>" . $entity->getDisplayName() . "</h3>";
	echo elgg_view_entity_icon($entity, 'medium');
	echo '</div>';
} else {
	// brief view
	$params = [
		'content' => elgg_get_excerpt($entity->description),
		'icon_entity' => $entity,
		'icon_size' => 'tiny',
		'class' => 'file-tools-icon-tiny',
		'subtitle' => false,
	];
	$params = $params + $vars;
	
	if (elgg_extract('file_tools_selector', $vars)) {
		$params['image_block_vars'] = [
			'image_alt' => elgg_view('input/checkbox', [
				'name' => 'file_guids[]',
				'value' => $entity->guid,
				'default' => false,
			]),
		];
	}
	
	echo elgg_view('object/elements/summary', $params);
}


// add folder structure to the breadcrumbs
// 	if (elgg_get_plugin_setting('use_folder_structure', 'file_tools') == 'yes') {
// 		// @todo this should probably be moved to the file view page, but that is currently not under control of file_tools
// 		$endpoint = elgg_pop_breadcrumb();
		
// 		$parent_folder = elgg_get_entities([
// 			'relationship' => 'folder_of',
// 			'relationship_guid' => $entity->guid,
// 			'inverse_relationship' => true,
// 		]);
		
// 		$folders = [];
// 		if ($parent_folder) {
			
// 			$parent_guid = (int) $parent_folder[0]->guid;
			
// 			while (!empty($parent_guid) && ($parent = get_entity($parent_guid))) {
// 				$folders[] = $parent;
// 				$parent_guid = (int) $parent->parent_guid;
// 			}
// 		}
		
// 		while ($p = array_pop($folders)) {
// 			elgg_push_breadcrumb($p->title, $p->getURL());
// 		}
		
// 		elgg_push_breadcrumb($entity->title);
// 	}





	// listing view of the file
// 	$file_icon_alt = '';
// 	if (elgg_get_plugin_setting('use_folder_structure', 'file_tools') == 'yes') {
// 		$file_icon = elgg_view_entity_icon($entity, 'tiny', [
// 			'img_class' => 'file-tools-icon-tiny',
// 		]);
// 		if (elgg_in_context('file_tools_selector')) {
// 			$file_icon_alt = elgg_view('input/checkbox', [
// 				'name' => 'file_guids[]',
// 				'value' => $entity->guid,
// 				'default' => false,
// 			]);
// 		}
		
// 		$excerpt = '';
// 		$subtitle = '';
// 		$tags = '';
// 	} else {
// 		$file_icon = elgg_view_entity_icon($entity, 'small');
// 		$excerpt = elgg_get_excerpt($entity->description);
// 	}
	
// 	$params = [
// 		'entity' => $entity,
// 		'metadata' => $entity_menu,
// 		'subtitle' => $subtitle,
// 		'content' => $excerpt
// 	];
// 	$params = $params + $vars;
// 	$list_body = elgg_view('object/elements/summary', $params);
	
// 	echo elgg_view_image_block($file_icon, $list_body, [
// 		'class' => 'file-tools-file',
// 		'image_alt' => $file_icon_alt,
// 	]);

