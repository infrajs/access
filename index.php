<?php
use infrajs\ans\Ans;
use infrajs\access\Access;
use infrajs\nostore\Nostore;

if(!is_file('vendor/autoload.php')) {
	chdir('../../../');
	require_once('vendor/autoload.php');
}

Nostore::on();

$ans = array();
$ans['test'] = Access::test();
$ans['debug'] = Access::debug();
$ans['admin'] = Access::admin();


return Ans::ret($ans);