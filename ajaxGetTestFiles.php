<?php
/**
 * Created by PhpStorm.
 * User: Levi
 * Date: 07.12.2018
 * Time: 10:22
 */
include 'config.class.php';
include 'phpunit.class.php';


header('Content-Type: application/json');

$pu = new \maierlabs\phpunit\phpunit();
$ret = array();
foreach (\maierlabs\phpunit\config::$projects as $project) {
    $testFiles = $pu->getDirContents($project["dir"], \maierlabs\phpunit\config::$excludeFiles);
    foreach ($testFiles as $idx => $testFile) {
        $tests = $pu->getTestClassMethodsFromFile($testFile["dir"] . $testFile["file"]);
        $asserts=0;
        foreach ($tests as $test=>$count) {
            $asserts +=$count;
        }
        $testFiles[$idx]["tests"] = sizeof($tests);
        $testFiles[$idx]["asserts"] = $asserts;
        $testFiles[$idx]["name"] = $project["name"];
    }
    $ret=array_merge($ret, $testFiles);
}

echo json_encode($ret);