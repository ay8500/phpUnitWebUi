<?php
session_start();
/**
 * Run a single test as ajax
 */
include 'config.class.php';
include 'phpunit.class.php';


header('Content-Type: application/json');

$pu = new \maierlabs\phpunit\phpunit();

$file=$pu->getGetParam("file");
$dir=$pu->getGetParam("dir");
$testNr=$pu->getGetParam("testNr");

if (null==$testNr || null==$file || null==$dir ) {
    die('Invalid parameter list!');
}

$result = $pu->runTestsForTestfile($dir,$file,$testNr);
echo json_encode($result);