<?php

if (!elgg_is_logged_in()) {
	return;
}

$body = elgg_echo('file_tools:list:tree:info:' . rand(1, 12));

echo elgg_view_module('aside', elgg_echo('file_tools:list:tree:info'), $body, ['menu' => elgg_view_icon('info')]);
