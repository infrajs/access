<?php
//use infrajs\ans\Ans;
use infrajs\access\Access;
//use infrajs\nostore\Nostore;

//Nostore::on();

$ans = array();
$ans['time'] = Access::adminTime();
$ans['test'] = Access::test();
$ans['debug'] = Access::debug();
$ans['admin'] = Access::admin();
$ans['update'] = Access::updateTime();

header('Cache-Control: no-store');
header('Content-type: application/javascript');

echo 'export default ';
echo json_encode($ans, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);