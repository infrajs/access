<?php
namespace infrajs\access;
use infrajs\ans\Ans;

if(!is_file('vendor/autoload.php')) {
	chdir('../../../');
	require_once('vendor/autoload.php');
}

$ans=array();


$ans['test'] = Access::test();
$ans['debug'] = Access::debug();
$ans['admin'] = Access::admin();
return Ans::ret($ans);