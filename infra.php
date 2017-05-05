<?php
use infrajs\access\Access;
use infrajs\ans\Ans;
use infrajs\nostore\Nostore;
use infrajs\template\Template;


$action = Ans::GET('-access');

if ($action == 'false') {
	if (Access::isTest()) Access::$conf['test'] = true;
	else Access::$conf['test'] = false; //Повышать права нельзя, если я не тестер, то нетестером и останусь!!!
	Access::$conf['debug'] = false;
	Access::$conf['admin'] = false;
	Nostore::on();
} else if ($action == 'true') {
	Access::test(true);
	Access::adminSetTime();
	Nostore::on();//Страница с таким параметром не кэшируется в браузере и её можно всегда спокойно вызывать
	//Устанавливает будто админ только что заходил... это мягко обновит кэши шаблонов и проверит изменения файлов
}