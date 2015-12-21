<?php
namespace infrajs\access;

use infrajs\config\Config;

if(!Access::adminTime()){
	Config::$install = true;
	Access::adminSetTime();
}