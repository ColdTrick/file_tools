<?php 

	gatekeeper();	

	$page_owner = page_owner_entity();

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
			$uploaded_files = elgg_view('page_elements/contentwrapper', array('body' => $files));
		}

		$body = elgg_view_layout("two_column_left_sidebar", "", $page_data.$uploaded_files);

		page_draw($title_text, $body);
		$_SESSION['extracted_files'] = null;
	}
	else 
	{
		register_error(elgg_echo("file_tools:error:pageowner"));
		forward();
	}