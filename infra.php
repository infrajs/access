<?php
namespace infrajs\access;

use infrajs\event\Event;
use infrajs\infra\Infra;
use infrajs\path\Path;
use infrajs\view\View;

$conf=&Infra::config('access');
Access::$conf=array_merge(Access::$conf, $conf);
$conf=Access::$conf;

Event::handler('oninstall', function () {
	Access::adminSetTime();
},'access:mem');


Event::handler('onjs', function () {	
	View::js('-access/access.js');
});