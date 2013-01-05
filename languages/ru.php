<?php 

	$russian = array(
		'file_tools' => "File Tools",
	
		'file_tools:file:actions' => 'Действия',
	
		'file_tools:list:sort:type' => 'Тип',
		'file_tools:list:sort:time_created' => 'Время создания',
		'file_tools:list:sort:asc' => 'По возрастанию',
		'file_tools:list:sort:desc' => 'По убыванию',
	
		// object name
		'item:object:folder' => "Папка",
	
		// menu items
		'file_tools:menu:mine' => "Ваши папки",
		'file_tools:menu:user' => "%s: папки",
		'file_tools:menu:group' => "Папки группы",
		
		// group tool option
		'file_tools:group_tool_option:structure_management' => "Разрешить участникам управлять папками",
		
		// views
	
		// object
		'file_tools:object:files' => "%s: файлы",
		'file_tools:object:no_files' => "Нет файлов",
	
		// input - folder select
		'file_tools:input:folder_select:main' => "Корневая папка",
	
		// list
		'file_tools:list:title' => "Список папок",
		
		'file_tools:list:folder:main' => "Корневая папка",
		'file_tools:list:files:none' => "---",
		'file_tools:list:select_all' => 'Выбрать все',
		'file_tools:list:deselect_all' => 'Отменить выбор',
		'file_tools:list:download_selected' => 'Загрузить выбранное',
		'file_tools:list:delete_selected' => 'Удалить выбранное',
		'file_tools:list:alert:not_all_deleted' => 'Не все файлы могут быть удалены',
		'file_tools:list:alert:none_selected' => 'Ничего не выбрано',
		
	
		'file_tools:list:tree:info' => "Известно ли вам?",
		'file_tools:list:tree:info:1' => "Вы можете перетаскивать файлы в папки!",
		'file_tools:list:tree:info:2' => "Двойной клик на папке раскрывает все её подпапки!",
		'file_tools:list:tree:info:3' => "Вы можете менять порядок папок, перетаскивая их на новое место в дереве папок!",
		'file_tools:list:tree:info:4' => "Вы можете перемещать целые ветви папок!",
		'file_tools:list:tree:info:5' => "Если вы удаляете папку, вы можете опционально удалить и все файлы в ней!",
		'file_tools:list:tree:info:6' => "Если вы удаляете папку, все её подпапки будут удалены!",
		'file_tools:list:tree:info:7' => "Это случайное сообщение!",
		'file_tools:list:tree:info:8' => "Если вы удалите папку, но без опции удаления файлов в ней, файлы появятся в папке верхнего уровня!",
		'file_tools:list:tree:info:9' => "Новую папку можно создать в любой существующей папке!",
		'file_tools:list:tree:info:10' => "При создании или изменении файла вы можете указать папку назначения!",
		'file_tools:list:tree:info:11' => "Перетаскивание файлов возможно только при отображении в виде списка!",
		'file_tools:list:tree:info:12' => "Вы можете обновлять права доступа всех подпапок и/или файлов при изменении прав доступа папки!",
	
		'file_tools:list:files:options:sort_title' => 'Сортировка',
		'file_tools:list:files:options:view_title' => 'Вид',
	
		'file_tools:usersettings:time' => 'Отображение времени',
		'file_tools:usersettings:time:description' => 'Изменить способ отображения времени создания файла/папки',
		'file_tools:usersettings:time:default' => 'Отображение времени по умолчанию',
		'file_tools:usersettings:time:date' => 'Дата',
		'file_tools:usersettings:time:days' => 'Прошедший период',
		
		// new/edit
		'file_tools:new:title' => "Новая папка",
		'file_tools:edit:title' => "Изменить папку",
		'file_tools:forms:edit:title' => "Название",
		'file_tools:forms:edit:description' => "Описание",
		'file_tools:forms:edit:parent' => "Папка верхнего уровня",
		'file_tools:forms:edit:change_children_access' => "Обновить пава доступа всех подпапок",
		'file_tools:forms:edit:change_files_access' => "Обновить права доступа всех файлов в этой папке (и всех подпапок, если выбраны)",
		'file_tools:forms:browse' => 'Просмотр',
		'file_tools:forms:empty_queue' => 'Очистить очередь',
	
		'file_tools:folder:delete:confirm_files' => "Удалить также все файлы в удаляемых (под)папках",
	
		// actions
		// edit
		'file_tools:action:edit:error:input' => "Неверный ввод при создании/изменении папки",
		'file_tools:action:edit:error:owner' => "Невозможно найти владельца папки",
		'file_tools:action:edit:error:folder' => "Нет паки для создания/изменения",
		'file_tools:action:edit:error:parent_guid' => "Неверная папка верхнего уровня",
		'file_tools:action:edit:error:save' => "Неизвестная ошибка во время сохранения папки",
		'file_tools:action:edit:success' => "Папка успешно создана/изменена",
	
		'file_tools:action:move:parent_error' => "Невозможно помесить папку в саму себя",
		
		// delete
		'file_tools:actions:delete:error:input' => "Неверный ввод при удалении папки",
		'file_tools:actions:delete:error:entity' => "Указанный GUID не может быть найден",
		'file_tools:actions:delete:error:subtype' => "Указанный GUID не является папкой",
		'file_tools:actions:delete:error:delete' => "Неизвестная ошибка при удалении папки",
		'file_tools:actions:delete:success' => "Папка успешно удалена",
	
		'file_tools:upload:new' => 'Загрузить и распаковать архив zip',
		'file_tools:upload:form:choose' => 'Выберите файл',
		'file_tools:upload:form:info' => 'Нажмите просмотр для загрузки (нескольких) файлов',
		'file_tools:upload:form:zip:info' => "Вы можете загрузить архив zip. Он будет распакован и каждый файл (папка) будет доступен отдельно. Не разрешенные типы файлов будут пропущены.",
	
		//errors
		'file_tools:error:pageowner' => 'Ошибка при определении владельца папки.',
		'file_tools:error:nofilesextracted' => 'При распаковке не найдено разрешенных файлов.',
		'file_tools:error:cantopenfile' => 'Архив не может быть распакован (проверьте, является ли загружаемый файл архивом .zip).',
		'file_tools:error:nozipfilefound' => 'Загруженный файл не является архивом .zip.',
		'file_tools:error:nofilefound' => 'Выберите файл для загрузки.',
	
		//messages
		'file_tools:error:fileuploadsuccess' => 'Архив загружен и распакован.',
		
		// move
		'file_tools:action:move:success:file' => "Файл успешно перемещён",
		'file_tools:action:move:success:folder' => "Папка успешно перемещена",
		
		// buld delete
		'file_tools:action:bulk_delete:success:files' => "Успешно удалено %s файлов",
		'file_tools:action:bulk_delete:error:files' => "Ошибка удаления некоторых файлов",
		'file_tools:action:bulk_delete:success:folders' => "Успешно удалено %s папок",
		'file_tools:action:bulk_delete:error:folders' => "Ошибка удаления некоторых папок",
		
		// reorder
		'file_tools:action:folder:reorder:success' => "Порядок папок успешно изменён",
		
		//settings
		'file_tools:settings:allowed_extensions' => 'Разрешенные типы файлов (через запятую)',
		'file_tools:settings:user_folder_structure' => 'Использовать структуру папок',
		'file_tools:settings:sort:default' => 'Сортировка по умолчанию',
	
		'file:type:application' => 'Приложение',
		'file:type:text' => 'Текст',

		// widgets
		// file tree
		'widgets:file_tree:title' => "Папки",
		'widgets:file_tree:description' => "Showcase your File folders",
		
		'widgets:file_tree:edit:select' => "Select which folder(s) to display",
		'widgets:file_tree:edit:show_content' => "Show the content of the folder(s)",
		'widgets:file_tree:no_folders' => "No folders configured",
		'widgets:file_tree:no_files' => "No files configured",
		'widgets:file_tree:more' => "More file folders",
	
		'widget:file:edit:show_only_featured' => 'Show only featured files',
		
		'widget:file_tools:show_file' => 'Feature file (widget)',
		'widget:file_tools:hide_file' => 'Unfeature file',
	
		'widgets:file_tools:more_files' => 'More files',
		
		// Group files
		'widgets:group_files:description' => "Показать последние файлы группы",
		
		// index_file
		'widgets:index_file:description' => "Показать последние файлы вашего сообщества",
	
	);
	
	add_translation("ru", $russian);
	