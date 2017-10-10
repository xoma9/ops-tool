# ops-tool
Обновление от 6.10.2017

- Изменилась схема базы данных. Теперь у каждого модуля должна быть таблица "modules_$modulename_action_rights" с полями id(INT(11) PRIMARY INDEX AUTOINCREMENT), userid(INT(11)),action_id(INT(11)),user_execute_right(TINYINT(1))
Чтобы не запиливать эту таблицу руками для каждого модуля на существующей установке можно добавить в api/api.frontend.php строку "create_module_action_rights_table($module);" в конец функции "show_module" и пооткрывать по-очереди все модули в веб-интерфейсе
