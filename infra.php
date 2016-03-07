<?php
use infrajs\access\Access;
use infrajs\ans\Ans;

$action = Ans::GET('-access');
if ($action == 'false') {
	Access::$conf['test'] = false;
	Access::$conf['debug'] = false;
	Access::$conf['admin'] = false;
}