<?php
namespace infrajs\access;

use infrajs\config\Config;

if(!Access::adminTime()){
	header('Infrajs-Access-Update:true');
	Config::update();
	Access::adminSetTime();
}