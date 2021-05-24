<?php
namespace maierlabs\phpunit;

include_once 'config.class.php';
include_once 'PHPUnit_Framework_TestCase.php';

class phpunit {

    /**
     * @param string $dir
     * @param array $excludeFiles
     * @return array
     */
    function getDirContents(string $dir, array $excludeFiles=array()): array
    {
        $results = array();
        $files = scandir($dir);

        foreach($files as $value){

            if(!is_dir($dir. DIRECTORY_SEPARATOR .$value)){
                if (strstr($value, 'Test.php') !== false) {
                    array_push($results,array("file"=>$value,"dir"=>$dir. DIRECTORY_SEPARATOR));
                }
            } else if(is_dir($dir. DIRECTORY_SEPARATOR .$value) && !in_array($value,$excludeFiles)) {
                $rr=$this->getDirContents($dir. DIRECTORY_SEPARATOR .$value,$excludeFiles);
                $results=array_merge($results, $rr);
            }
        }
        return $results;
    }

    function getTestFiles(): array
    {
        $ret = array();
        foreach (config::$projects as $project) {
            $testFiles = $this->getDirContents($project["dir"], config::$excludeFiles);
            foreach ($testFiles as $idx => $testFile) {
                $tests = $this->getTestClassMethodsFromFile($testFile["dir"] . $testFile["file"]);
                $asserts=0;
                foreach ($tests as $count) {
                    $asserts +=$count;
                }
                $testFiles[$idx]["tests"] = sizeof($tests);
                $testFiles[$idx]["asserts"] = $asserts;
                $testFiles[$idx]["name"] = $project["name"];
            }
            $ret=array_merge($ret, $testFiles);
        }
        return $ret;
    }

    function getTestClassMethods($className): array
    {
        $ignoreList = array("setup", "teardown");
        $ret = array();
        $methods=get_class_methods($className);
        if ($methods!=null) {
            foreach ($methods as $methodName) {
                $reflector  = new \ReflectionMethod($className,$methodName);
                $docComment = $reflector->getDocComment();
                if (!in_array(strtolower($methodName), $ignoreList) && strpos($docComment, "PHPUnit_Framework") === false) {
                    if (substr($methodName,0,4)==="test"  || strpos($docComment,"@test") ){
                        $method = new \stdClass();
                        $method->name = $methodName;
                        $method->ignore = strpos($docComment,"@ignore")!==false;
                        $method->opositeResult = strpos($docComment,"@oposite")!==false;
                        $ret[] = $method;
                    }
                }
            }

        }
        return $ret;
    }

    /**
     * @param $fileName
     * @return array
     */
    function getTestClassMethodsFromFile($fileName): array
    {
        $annotationTest=false;
        $ret = array();
        $methods = array();
        $aktMethod="";
        $lines = explode("\n",file_get_contents($fileName));
        foreach ($lines as $line) {
            $line = str_replace("\t"," ",$line);
            $line = str_replace("  "," ",$line);
            if (strpos($line,"@test")!==false)
                $annotationTest=true;
            $p1=0;$p2=0;
            if (($p1 = strpos($line,"public function test"))!==false &&
                ($p2 = strpos($line,"()"))!==false) {
                    $p1 +=strlen("public function ");
                    $aktMethod=substr($line,$p1,$p2-$p1);
                    $methods[$aktMethod]=0;
                    $annotationTest=false;
            }
            if ($annotationTest && ($p1 = strpos($line,"public function "))!==false &&
                ($p2 = strpos($line,"()"))!==false) {
                $p1 +=strlen("public function ");
                $aktMethod=substr($line,$p1,$p2-$p1);
                $methods[$aktMethod]=0;
                $annotationTest=false;
            }
            $line = str_replace(" ","",$line);
            if(strpos($line,'$this->assert')!==false && $aktMethod!="") {
                $methods[$aktMethod]++;
            }
        }
        //remove setup and teardown
        if ($methods!=null) {
            foreach ($methods as $method=>$count) {
                if (!in_array(strtolower($method), array("setup", "teardown")) && strpos($method, "assert") !== 0) {
                    $ret[$method] = $count;
                }
            }
        }
        return $ret;
    }

    function getTestClassSetupMethod($className): ?string
    {
        $methods=get_class_methods($className);
        foreach ($methods as $method) {
            if (strtolower($method)=="setup") {
                return $method;
            }
        }
        return null;
    }

    function getTestClassTearDownMethod($className) {
        $methods=get_class_methods($className);
        foreach ($methods as $method) {
            if (strtolower($method)=="teardown") {
                return $method;
            }
        }
        return null;
    }

    public function  runTestsForTestfile($dir,$file,$testNr=0)
    {
        $timer=microtime(true);

        $testClassName = substr($file, 0, strpos(strtolower($file), ".php"));
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

        //include the php file that contains the function
        try {
            include_once  $dir . $file;
        } catch (\Exception $e) {
            return $this->exceptionOccured($e);

        } catch (\Error $e) {
            return $this->exceptionOccured($e);
        } catch (\Throwable $e) {
            return $this->exceptionOccured($e);
        }

        $testMethodList = $this->getTestClassMethods($testClassName);
        $testSetupMethod = $this->getTestClassSetupMethod($testClassName);
        $testTearDownMethod = $this->getTestClassTearDownMethod(($testClassName));


        $result = array();
        if ($testNr < sizeof($testMethodList) - 1)
            $result["filestatus"] = "running";
        else
            $result["filestatus"] = "done";
        $result["tests"] = $testMethodList;
        $result["testNr"] = intval($testNr);
        $result["testName"] = $testMethodList[$testNr]->name;

        if ($testNr == 0) {
            $theTestClass = new $testClassName();
            $_SESSION["class"] = serialize($theTestClass);
        } else {
            $theTestClass = unserialize($_SESSION["class"]);
        }

        ob_start();

        if ($testSetupMethod != null) {
            try {
                $theTestClass->$testSetupMethod();
            } catch (\Exception $e) {
                $result = $this->exceptionOccured($e, $theTestClass, $result, $timer);
            } catch (\Error $e) {
                $result =  $this->exceptionOccured($e, $theTestClass, $result, $timer);
            } catch (\Throwable $e) {
                $result = $this->exceptionOccured($e, $theTestClass, $result, $timer);
            }
        }

        if (isset($testMethodList[$testNr]) && isset($testMethodList[$testNr])) {
            try {
                if ($testMethodList[$testNr]->ignore) {
                    echo($testMethodList[$testNr]->name. " ignored");
                } else {
                    $functionName = $testMethodList[$testNr]->name;
                    $theTestClass->startNewTestFunction();
                    $theTestClass->$functionName();
                    if ($theTestClass->getExpectedException() !== null) {
                        $result = $this->exceptionOccured(new Exception("Expected exception not occurd"), $theTestClass, $result, $timer);
                    }
                }
            } catch (\Exception $e) {
                $result = $this->exceptionOccured($e, $theTestClass, $result, $timer);
            } catch (\Error $e) {
                $result = $this->exceptionOccured($e, $theTestClass, $result, $timer);
            } catch (\Throwable $e) {
                $result = $this->exceptionOccured($e, $theTestClass, $result, $timer);
            }
        }

        if ($testTearDownMethod != null) {
            try {
                $theTestClass->$testTearDownMethod();
            } catch (\Exception $e) {
               $result = $this->exceptionOccured($e, $theTestClass, $result, $timer);
            } catch (\Error $e) {
                $result = $this->exceptionOccured($e, $theTestClass, $result, $timer);
            } catch (\Throwable $e) {
                $result = $this->exceptionOccured($e, $theTestClass, $result, $timer);
            }
        }


        $result["echo"] = ob_get_clean();
        $res = $theTestClass->assertGetUnitTestResult();
        if ($res->errorText != "")
            $result["errorMessage"] = $res->errorText;
        if ($testMethodList[$testNr]->opositeResult) {
            $result["test"] = !$res->testResult;
            $result["assertOk"] = $res->assertError;
            $result["assertError"] = $res->assertOk;
        } else {
            $result["test"] = $res->testResult;
            $result["assertOk"] = $res->assertOk;
            $result["assertError"] = $res->assertError;
        }
        $result["time"] = number_format((microtime(true) - $timer) * 1000, 2);
        return $result;
    }

    private function exceptionOccured($e,$theTestClass=null,$result=null,$timer=null) {
        if ($theTestClass!=null) {
            $res = $theTestClass->assertGetUnitTestResult();
            $result["assertOk"] = $res->assertOk;
            $result["assertError"] = $res->assertError;
            if ($theTestClass->getExpectedException()!==null &&
                stripos($e->getMessage(),$theTestClass->getExpectedException()->name)!==false) {
                $result["assertOk"] = 0;
                $result["assertError"] = 0;
                $result["test"] = "true";
                return $result;
            }
        } else {
            $result["assertOk"] = 0;
            $result["assertError"] = 1;
            $result["test"] = "false";
        }
        $result["errorMessage"]=$e->getMessage()." in file:".$e->getFile()." line:".$e->getLine(). $this->getCallStackHtml($e->getTrace());
        $result["test"]=false;
        if (!isset($result["tests"])) {
            $result["filestatus"] = "error";
        }
        $result["echo"]=ob_get_clean();
        if ($timer!=null)
            $result["time"]=number_format((microtime(true)-$timer) * 1000,2);
        else
            $result["time"]=0;
        return $result;
    }

    function getCallStackHtml($callStack) {
        $html='';
        foreach ($callStack as $e) {
            $html .="<br/>";
            $html .="Called by: ".$e["function"];
            $html .=" in file: ".$e["file"];
            $html .=" line: ".$e["line"];
        }
        $html = str_replace('\\','/',$html);
        return $html;
    }
        /**
     * Safety get paramateter read
     * @param string $name
     * @param string $def
     * @return string
     */
    function getGetParam($name,$def=null) {
        if (isset($_GET[$name]))
            return html_entity_decode(htmlentities($_GET[$name],ENT_QUOTES),ENT_NOQUOTES);
        return  $def;

    }

}