<?php
namespace maierlabs\phpunit;

include_once 'config.class.php';

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
        $ret = array();
        $methods=get_class_methods($className);
        if ($methods!=null) {
            foreach ($methods as $method) {
                if (!in_array(strtolower($method), array("setup", "teardown","calltesturl")) && strpos($method, "assert") === false) {
                    $ret[] = $method;
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
        $ret = array();
        $methods = array();
        $aktMethod="";
        $lines = explode("\n",file_get_contents($fileName));
        foreach ($lines as $line) {
            $line = str_replace("  "," ",$line);
            $p1 = strpos($line,"public function test");
            if ($p1!==false) {
                $p2 = strpos($line,"()",$p1);
                if ($p2!==false) {
                    $p1 +=strlen("public function ");
                    $aktMethod=substr($line,$p1,$p2-$p1);
                    $methods[$aktMethod]=0;
                }
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