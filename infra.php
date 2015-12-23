<?php
namespace infrajs\access;

use infrajs\config\Config;

if(!Access::adminTime() || Config::$install){
	header('Infrajs-Access-Update:true');
	Config::$install = true;
	Access::adminSetTime();
}