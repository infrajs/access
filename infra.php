<?php
namespace infrajs\access;
use infrajs\event\Event;
use infrajs\infra\Infra;
use infrajs\path\Path;

$conf=&Infra::config('access');
Access::$conf=array_merge(Access::$conf, $conf);
$conf=Access::$conf;

Event::handler('oninstall', function () {
	Access::adminSetTime();
});