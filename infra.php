<?php
namespace infrajs\access;

use infrajs\infra\Config;

if(!Access::adminTime()){
	Config::$install = true;
	Access::adminSetTime();
}