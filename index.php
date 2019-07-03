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


return Ans::ret($ans);