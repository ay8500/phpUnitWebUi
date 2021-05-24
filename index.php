<?php
/**
 * User: Levi
 * Date: 24.05.2021
 */
include_once 'config.class.php';

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
                        <div>
                            <a style="font-size: 30px;text-decoration: none;color: black;" href="phpunitInfo.php"><?php echo(\maierlabs\phpunit\config::$SiteTitle)?></a>
                            <a style="font-size: 13px;color: black;" href="phpunitInfo.php">read more | documentation</a>

                        </div>
                        <div style="">&copy; MaierLabs version:<?php echo (\maierlabs\phpunit\config::$webAppVersion)?></div>
                        <button class="btn btn-success" onclick="getTestFiles()" id="btn_chkservertests">Check server for tests</button>
                        <button class="btn btn-success" onclick="runAlltests()" id="btn_runservertests">Run all unit tests</button>
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