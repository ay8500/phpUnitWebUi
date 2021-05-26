<?php
/**
* User: Levi
* Date: 24.05.2021
*/
session_start();
include_once 'config.class.php';
include_once 'phpunit.class.php';

$pu = new \maierlabs\phpunit\phpunit();

header('Content-Type: text/plain');
echo(\maierlabs\phpunit\config::$SiteTitle."\n"."Version:".\maierlabs\phpunit\config::$webAppVersion."\n");
if (!isset($_GET["project"]))
    die("Error: paramter project is missing!");
$project=$_GET["project"];
if (($key = array_search($project,array_column(\maierlabs\phpunit\config::$projects,"name")))===false)
    die("Error: Projekt not found");
include_once 'PHPUnit_Framework_TestCase.php';

$allTests=0;
$allAsserts=0;
$testFiles=$pu->getDirContents(\maierlabs\phpunit\config::$projects[$key]["dir"],\maierlabs\phpunit\config::$excludeFiles );
foreach ($testFiles as $idx => $testFile) {
    $tests = $pu->getTestClassMethodsFromFile($testFile["dir"].$testFile["file"]);
    $testFiles[$idx]["tests"]=sizeof($tests);
    $allTests +=sizeof($tests);
    foreach ($tests as $test) {
        $allAsserts +=$test;
    }
}
echo("Project:".$project." test files:".sizeof($testFiles).' tests:'.$allTests. ' asserts:'.$allAsserts."\r\n");
$allTime =0;
$allFilesError =0;$allTestsError =0;$allAssertsError =0;
$allFilesOk =0;$allTestsOk =0;$allAssertsOk =0;
set_time_limit(120);

foreach ($testFiles as $idx => $testFile) {
    $aktTest=0;
    $fileIsOk=true;
    $result= array("filestatus"=>"running");
    do {
        $result = $pu->runTestsForTestfile($testFile["dir"] , $testFile["file"],$aktTest);
        //echo($result["echo"]."\r\n");
        while (strlen($testFile["file"]."-".$result["testName"])<50) $result["testName"].=" ";
        echo( "\tTest:".$testFile["file"]."-".$result["testName"]);
        echo( "\t ok:".$result["assertOk"]);
        echo( "\t error:".$result["assertError"]);
        echo( "\t time:".$result["time"].'ms'."\r\n");
        $allTime +=$result["time"];
        $allAssertsError +=$result["assertError"];
        $allAssertsOk +=$result["assertOk"];
        if ($result["test"])
            $allTestsOk++;
        else
            $allTestsError++;
        if ($result["assertError"]!==0)
            $fileIsOk=false;
        $aktTest++;
        if ($result["filestatus"]!=="running") {
            if ($fileIsOk)
                $allFilesOk++;
            else
                $allFilesError++;
        }
    } while ($result["filestatus"]==="running");
}

echo("\r\n\r\nOk \tfiles:".$allFilesOk." \ttest:".$allTestsOk." \tasserts:".$allAssertsOk." time:".number_format($allTime,2)."ms");
echo("\r\nError \tfiles:".$allFilesError." \ttest:".$allTestsError." \tasserts:".$allAssertsError);

echo ($allTestsError==0?"\r\nTestresult:OK":"\r\nTestresult:ERROR");

if (isset($_GET["succesmail"]) && $allTestsError==0) {
    if (sendTestResultsMail($_GET["succesmail"],"Result OK",$allTestsOk,$allTestsError,$allTime))
        echo("\nSuccesmail sent to ".$_GET["succesmail"]);
    else
        echo("\nError sending succesmail to ".$_GET["succesmail"]);
}
if (isset($_GET["errormail"]) && $allTestsError>0) {
    if (sendTestResultsMail($_GET["errorsmail"],"Result ERROR",$allTestsOk,$allTestsError,$allTime))
        echo("\nErrormail sent to ".$_GET["errormail"]);
    else
        echo("\nError sending errormail to ".$_GET["errormail"]);
}

function sendTestResultsMail($recipient,$subject,$ok,$error,$time) {
    $subject = \maierlabs\phpunit\config::$SiteTitle.' '.$subject;
    $text = 'Reasults ok:'.$ok." error:".$error. " time:".number_format($time,2)."ms";
    $header = 'From: ' . \maierlabs\phpunit\config::$siterMail. "\r\n" .
        'Reply-To: ' . \maierlabs\phpunit\config::$siteMail. "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    return mail($recipient, $subject, $text, $header);
}
