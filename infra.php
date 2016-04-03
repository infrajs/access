<?php
use infrajs\access\Access;
use infrajs\ans\Ans;

$action = Ans::GET('-access');

if ($action == 'false') {
	if (Access::isTest()) Access::$conf['test'] = true;
	else Access::$conf['test'] = false; //Повышать права нельзя, если я не тестер, то нетестером и останусь!!!
	Access::$conf['debug'] = false;
	Access::$conf['admin'] = false;
} else if ($action == 'true') {
	Access::test(true);
	Access::adminSetTime(); 
	//Устанавливает будто админ только что заходил... это мягко обновит кэши шаблонов и проверит изменения файлов
}