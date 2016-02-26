<?php
namespace infrajs\access;
use infrajs\update\Update;

if (Update::$is) {
	Access::adminSetTime();
}

?>