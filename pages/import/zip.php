<?php 

	set_context('file');

	gatekeeper();	

	$page_owner = elgg_get_page_owner_entity();

	if($page_owner)
	{
		$title_text = elgg_echo("file_tools:upload:new");

		$form = elgg_view("file_tools/forms/import/zip");

		$title = elgg_view_title($title_text . $back_text);

		$page_data = $title . $form;

		if($_SESSION['extracted_files'])
		{
			$files = '<h3 class="settings">Extracted files</h3>';
			$files .= '<p>'.count($_SESSION['extracted_files']).' file(s) extracted.</p>';
			$files .= '<ul style="list-style: disc;">';
			
			foreach($_SESSION['extracted_files'] as $file)
			{
				$files .= '<li>'.$file.'</li>';
			}
			
			$files .= '<ul>';
			$uploaded_files = $files;
		}

		$body = elgg_view_layout("one_sidebar", array('content' => $page_data.$uploaded_files));

		echo elgg_view_page($title_text, $body);
		$_SESSION['extracted_files'] = null;
	}
	else 
	{
		register_error(elgg_echo("file_tools:error:pageowner"));
		forward();
	}