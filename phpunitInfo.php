<?php
    include "config.class.php";
    include "PHPUnit_Framework_TestCase.php";
?>
<html>
<header>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <title>Web PHP Unit by MaierLabs</title>
</header>
<body>
<div class="container-fluid well">
    <div class="row">
        <div class="col-4 col-md-4" >
            <div style="height:110px;margin-bottom: 10px">
                <div>
                    <a style="font-size: 30px;text-decoration: none;color: black;" href="javascript:window.history.back();"><?php echo(\maierlabs\phpunit\config::$SiteTitle)?></a><br/>
                    <b>easy and lightweight way to quickly test your php code</b>
                </div>
                <div style="">&copy; MaierLabs version:<?php echo (\maierlabs\phpunit\config::$webAppVersion)?></div>
            </div>
        </div>
    </div>
    <div>
        <h2>Basics</h2>
        <ul>
            <li>Each test is a class that usually extens the PHPUnit_Framework_TestCase</li>
            <li>Test filename should end with Test for filetype. E.g. myFirstTest.php</li>
            <li>Test classname should fit to the file name. E.g. for myFirstTest.php use class myFirstTest extends PHPUnit_Framework_TestCase</li>
            <li>A test is recognized when a public function begins with test or has the annotation @test</li>
            <li>Public functions setUp and tearDown are used before and after the tests are processed</li>
        </ul>
    </div>
    <div>
        <h2>Special features of this solution</h2>
        <ul>
            <li>Simple and light weight</li>
            <li>No third party code or other frameworks are required</li>
            <li>The testing takes place via the browser in the runtime environment of the test item</li>
            <li>Individual tests, test classes or projects can be run separately</li>
            <li>Batch calls per project are possible, success or error emails are sent
                <ul>
                    <li>Script: batch.php  E.g. batch.php?project=myTest&errormail=name@mydomain.info</li>
                    <li>Parameter: <b>project</b> the project name specified in the config.class.php</li>
                    <li>Parameter: <b>succesmail</b> A notification will be sent to this address if all tests are ok</li>
                    <li>Parameter: <b>errormail</b> A notification will be sent to this address if at least one test is wrong</li>
                </ul>
            </li>
        </ul>
    </div>
    <div>
        <h2>Supported asserts</h2>
        <ul>
            <?php foreach(getAssets() as $asset) {
                echo('<li>'.$asset->name.'</li>');
            }?>
            <li>setExpectedException</li>
        </ul>
    </div>
    <div>
        <h2>Supported @annotations</h2>
        Info: A doc comment in PHP must start with /** and end with */. Annotations in any other style of comment will be ignored.
        <ul>
            <li>@test</li>
            <li>@ignore test will be executed but the result is always ok</li>
            <li>@skip skip the test</li>
        </ul>
        <h3>Annotations comming soon</h3>
        <ul>
            <li>@after Can be used to specify methods that should be called after all test methods in a test class have been run to clean up</li>
            <li>@before Can be used to specify methods that should be called before each test method in a test case clas</li>
            <li>@expectedException Annotation to test whether an exception is thrown inside the tested code</li>
            <li>@expectedExceptionMessage</li>
            <li>@group A test can be tagged as belonging to one or more group, in order to execute them separately</li>
            <li>@author A test can be tagged as belonging to one or more author, in order to execute them separately</li>
            <li>@backupGlobals</li>
        </ul>
    </div>
    <div>
        <h2>The config file</h2>
        <div class="code" style="background-color: white">
            /**
            * @var string start directory for *Test.php test files
            */
            public static $projects = array(
            array("name"=>"PhpUnit","dir"=>__DIR__),
            array("name"=>"myTestCase","dir"=>__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."myProject")
            );
        </div>
    </div>
    <div>
        <h2>Example</h2>
        <div class="code" style="background-color: white">
            include "PHPUnit_Framework_TestCase.php";

            class phpUnitTest extends PHPUnit_Framework_TestCase {

                private $db;

                public function setUp() {
                    $db = new Database();
                }
                public function tearDown() {
                    $this->db->diconnect();
                }

                /**
                 * @test
                 * @ignore
                 */
                public function ignore() {
                    $this->assertTrue(false);
                }

                public function testSomething() {
                    $this->assertSame("Ok", $db->getOk());
                    $this->assertTrue(1==1);
                }
            }
        </div>
    </div>
</div>

<style>
    .kw{color:blue;font-weight: bold}

</style>
<script>
    var keyWords = Array("public","function","include","private","new","static","array","class");
    $(".code").each(function() {
        var code = $(this).text();
        var html="";
        var tab = 0;
        code.split("\n").forEach((item)=> {
            if (item.search("}")!==-1  || item.trim().charAt(0)==")")
                tab--;
            for(var i=0; i<tab; i++) {
                html += '<span style="margin-left:20px"></span>';
            }
            var line = item;
            keyWords.forEach((e)=>{
                line = line.replaceAll(new RegExp(e,'g'),'<span kw>'+e+'</span>');
            });
            line = line.replaceAll( /"(.*?)"/g ,'<span style="color: green">"$1"</span>');
            line = line.replaceAll(new RegExp('<span kw>','g'),'<span class="kw">');
            html +=line;
            /*
            item.split(new RegExp("[\\s]")).forEach((e,k)=>{
                html += keyWords.includes(e)?('<span class="kw">'+e+'</span>'):e;
                html += ' ';
            });*/
            html +="<br />";
            if (item.search("{")!==-1 || item.trim().slice(-1)=="(")
                tab++;
        });
        $(this).html(html);
    });
</script>

<?php
function getAssets() {
    $ret = array();
    $methods=get_class_methods("PHPUnit_Framework_TestCase");
    if ($methods!=null) {
        foreach ($methods as $methodName) {
            if (stripos($methodName, "assert") !== false) {
                    $method = new \stdClass();
                    $method->name = $methodName;
                    $ret[] = $method;
                }
            }
        }
    sort($ret);
    return $ret;
}
