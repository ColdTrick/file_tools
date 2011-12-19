<?php

gatekeeper();

$folder_guid = get_input("folder_guid");

if(!empty($folder_guid))
{
	if($folder = get_entity($folder_guid))
	{
		if(($folder->getSubtype() == FILE_TOOLS_SUBTYPE) && $folder->canEdit())
		{
			if($folder->delete())
			{
				system_message(elgg_echo("file_tools:actions:delete:success"));
			}
			else
			{
				register_error(elgg_echo("file_tools:actions:delete:error:delete"));
			}
		}
		else
		{
			register_error(elgg_echo("file_tools:actions:delete:error:subtype"));
		}
	}
	else
	{
		register_error(elgg_echo("file_tools:actions:delete:error:entity"));
	}
}
else
{
	register_error(elgg_echo("file_tools:actions:delete:error:input"));
}

forward(REFERER);