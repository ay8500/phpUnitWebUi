<?php
/**
 * User: Levi
 * Date: 07.12.2018
 */
include_once 'config.class.php';
include_once 'phpunit.class.php';

$pu = new \maierlabs\phpunit\phpunit();

if (isset($_GET["action"]) && $_GET["action"]="batch") {
    include_once 'PHPUnit_Framework_TestCase.php';
    header('Content-Type: text/plain');
    echo(\maierlabs\phpunit\config::$SiteTitle."\n");

    $allTests=0;
    $testFiles=$pu->getDirContents(\maierlabs\phpunit\config::$startDir,\maierlabs\phpunit\config::$excludeFiles );
    foreach ($testFiles as $idx => $testFile) {
        $tests = $pu->getTestClassMethodsFromFile($testFile["dir"].$testFile["file"]);
        $testFiles[$idx]["tests"]=sizeof($tests);
        $allTests +=sizeof($tests);
    }
    echo("test files:".sizeof($testFiles).' tests:'.$allTests);
    $allTime =0;
    $allTestsError =0;
    $allTestsOk =0;
    set_time_limit(120);

    foreach ($testFiles as $idx => $testFile) {
        $tests = $pu->getTestClassMethodsFromFile($testFile["dir"] . $testFile["file"]);

        include $testFile["dir"] . $testFile["file"];

        $testClassName=substr($testFile["file"],0,strpos(strtolower($testFile["file"]),".php"));
        $testMethodList = $pu->getTestClassMethods($testClassName);
        $testSetupMethod= $pu->getTestClassSetupMethod($testClassName);
        $testTearDownMethod= $pu->getTestClassTearDownMethod(($testClassName));

        foreach ($testMethodList as $idx => $aktTest) {
            echo("\n\n".$testClassName.'/'.$testMethodList[$idx]);
            $timer=microtime(true);
            $error=null;
            if ($idx == 0) {
                $theTestClass = new $testClassName();
            }

            error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
            ob_start();

            if ($testSetupMethod != null) {
                try {
                    $theTestClass->$testSetupMethod();
                } catch (\Exception $e) {
                    $error=$e;
                } catch (\Error $e) {
                    $error=$e;
                } catch (\Throwable $e) {
                    $error=$e;
                }
            }

            if (isset($testMethodList[$idx])) {
                try {
                    $functionName = $testMethodList[$idx];
                    $theTestClass->$functionName();
                } catch (\Exception $e) {
                    $error=$e;
                } catch (\Error $e) {
                    $error=$e;
                } catch (\Throwable $e) {
                    $error=$e;
                }
            }

            if ($testTearDownMethod != null) {
                try {
                    $theTestClass->$testTearDownMethod();
                } catch (\Exception $e) {
                    $error=$e;
                } catch (\Error $e) {
                    $error=$e;
                } catch (\Throwable $e) {
                    $error=$e;
                }
            }


            echo (ob_get_clean());
            $res = $theTestClass->assertGetUnitTestResult();
            echo( " ok:".$res->assertOk);
            echo( " error:".$res->assertError);
            echo( " time:".number_format((microtime(true) - $timer) * 1000, 2)).'ms';
            $allTime +=(microtime(true) - $timer)*1000;
            $allTestsError +=$res->assertError;
            $allTestsOk +=$res->assertOk;
        }
    }
    echo("\n\nResult ok:".$allTestsOk." error:".$allTestsError." time:".number_format($allTime,2)."ms");
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
    die();
}

function sendTestResultsMail($recipient,$subject,$ok,$error,$time) {
    $subject = \maierlabs\phpunit\config::$SiteTitle.' '.$subject;
    $text = 'Reasults ok:'.$ok." error:".$error. " time:".number_format($time,2)."ms";
    $header = 'From: ' . \maierlabs\phpunit\config::$siterMail. "\r\n" .
        'Reply-To: ' . \maierlabs\phpunit\config::$siteMail. "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    return mail($recipient, $subject, $text, $header);
}

?>
<html>
    <header>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="//www.gstatic.com/charts/loader.js"></script>
        <title>Web PHP Unit by MaierLabs</title>
    </header>
    <body>
        <div class="container-fluid well">
            <div class="row">
                <div class="col-4 col-md-4" >
                    <div style="height:110px;margin-bottom: 10px">
                        <div style="font-size: 30px"><?php echo(\maierlabs\phpunit\config::$SiteTitle)?></div>
                        <div style="">&copy; MaierLabs version:<?php echo (\maierlabs\phpunit\config::$webAppVersion)?></div>
                        <button class="btn btn-success" onclick="getTestFiles()">Check server for tests</button>
                        <button class="btn btn-success" onclick="runAlltests()">Run all unit tests</button>
                    </div>
                    <div id="projectsGauge" style="display: inline-block; width: 100%; height: 350px;padding:5px;background-color: white; border-radius: 10px"></div>
                </div>
                <div class="col-4 col-md-4" >
                    <div style="height:110px;background-color:white;border-radius: 10px;vertical-align: top;margin-bottom: 10px">
                        <div style="margin-left: 20px;display: inline-block;vertical-align: top">
                            <b>Test Status</b>
                        </div>
                        <div style="margin-left: 20px;display: inline-block">
                            <span style="width:65px;display: inline-block">Projects:</span>
                            <span class="badge" style="background-color: green" id="pok">0</span><span class="badge" style="background-color: red" id="perror">0</span><br/>
                            <span style="width:65px;display: inline-block">Files:</span>
                            <span class="badge" style="background-color: green" id="fok">0</span><span class="badge" style="background-color: red" id="ferror">0</span><br/>
                            <span style="width:65px;display: inline-block">Tests:</span>
                            <span class="badge" style="background-color: green" id="tok">0</span><span class="badge" style="background-color: red" id="terror">0</span><br/>
                            <span style="width:65px;display: inline-block">Asserts:</span>
                            <span class="badge" style="background-color: green" id="aok">0</span><span class="badge" style="background-color: red" id="aerror">0</span><br/>
                        </div>
                    </div>
                    <div id="filesGauge" style="display: inline-block;width: 100%; height: 350px;padding:5px;background-color: white; border-radius: 10px;"></div>
                </div>
                <div class="col-4 col-md-4" >
                    <div style="height:110px;background-color: white;border-radius: 10px;margin-bottom: 10px">
                        <div style="display: inline-block; vertical-align: top;margin-left: 20px;"><b>Speed</b><br/>test files per second</div>
                        <div style="height:100px;display: inline-block; margin-left: 20px;" id="speedGauge"></div>
                    </div>
                    <div id="fileGauge" style="display: inline-block; width: 100%; height: 350px;padding:5px;background-color: white; border-radius: 10px;"></div>
                </div>
            </div>
            <div class="panel-body">
            </div>
            <div class="panel-body" id="console">
                <b>Console</b>
            <div>
        </div>
    </body>
</html>

<script type="text/javascript">
    <?php include "js/phpunit.js"?>
</script>