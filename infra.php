<?php
use infrajs\access\Access;
use infrajs\ans\Ans;

$action = Ans::GET('-access');

if ($action == 'false') {
	if (Access::isTest()) Access::$conf['test'] = true;
	else Access::$conf['test'] = false; //Повышать права нельзя, если я не тестер, то нетестером и останусь!!!
	Access::$conf['debug'] = false;
	Access::$conf['admin'] = false;
}