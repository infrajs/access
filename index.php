<?php
use infrajs\ans\Ans;
use infrajs\access\Access;
use infrajs\nostore\Nostore;

Nostore::on();

$ans = array();
$ans['time'] = Access::adminTime();
$ans['test'] = Access::test();
$ans['debug'] = Access::debug();
$ans['admin'] = Access::admin();

if (is_file('.git/index')) {
	$ans['update'] = filemtime('.git/index');
} else if (is_file('composer.lock')) {
	$ans['update'] = filemtime('composer.lock');
} else {
	$ans['update'] = filemtime(__FILE__);
}

return Ans::ret($ans);