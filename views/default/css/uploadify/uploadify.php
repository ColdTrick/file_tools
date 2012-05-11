<?php
?>
/*
	Uploadify v3.1.0
	Copyright (c) 2012 Reactive Apps, Ronnie Garcia
	Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

#uploadify-button-wrapper {
	display: inline;
}

.uploadify-queue-item {
	background-color: #EEEEEE;
	
	border: 1px solid #CCCCCC;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	
	font: 11px Verdana, Geneva, sans-serif;
	
	margin-top: 5px;
	max-width: 350px;
	padding: 10px;
}

.uploadify-error {
	background-color: #FDE5DD !important;
}

.uploadify-queue-item .cancel a {
	background: transparent url(<?php echo elgg_get_site_url(); ?>_graphics/elgg_sprites.png) no-repeat 0 -234px;
	width: 16px;
	height: 16px;
	margin: 0 2px;
	
	float: right;
	text-indent: -9999px;
}
.uploadify-queue-item .cancel a:hover {
	background-position: 0 -216px;
}

.uploadify-queue-item.completed {
	background-color: #E5E5E5;
}

.uploadify-progress {
	background-color: #E5E5E5;
	margin-top: 10px;
	width: 100%;
}

.uploadify-progress-bar {
	background-color: #0099FF;
	height: 3px;
	width: 1px;
}