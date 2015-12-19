<?php
namespace infrajs\access;

use infrajs\event\Event;
use infrajs\infra\Infra;
use infrajs\infra\Config;
use infrajs\path\Path;
use infrajs\view\View;

Event::handler('oninstall', function () {
	Access::adminSetTime();
},'access:mem');