<?php

class PHPUnit_Framework_TestCase {

    private $assertOk=0;
    private $assertError=0;
    private $errorText="";
    private $expectedException        = null;

    /**
     * PHPUnit_Framework
     * @param  mixed   $exceptionName
     * @param  string  $exceptionMessage
     * @param  integer $exceptionCode
     * @since  Method available since Release 3.2.0
     */
    public function setExpectedException($exceptionName='', $exceptionMessage = '', $exceptionCode = NULL)
    {
        $this->expectedException        = new stdClass();
        $this->expectedException->name = $exceptionName;
        $this->expectedException->message = $exceptionMessage;
        $this->expectedException->code    = $exceptionCode;
    }


    /**
     * PHPUnit_Framework
     */
    public function assertStringStartsWith($prefix,$actual,$message='') {
        if ($actual!=null && $prefix===substr($actual,0,strlen($prefix))) {
            $this->assertOk++;
        } else {
            $this->assertError++;
            $this->errorText .= $message==''?'Failed asserting that ' . $actual. '  starts with '.$prefix."<br />":$message;
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertStringStartsNotWith($prefix,$actual,$message='') {
        if ($actual==null || $prefix!==substr($actual,0,strlen($prefix))) {
            $this->assertOk++;
        } else {
            $this->assertError++;
            $this->errorText .= $message==''?'Failed asserting that ' . $actual. '  not with '.$prefix."<br />":$message;
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertStringEndsWith($suffix,$actual,$message='') {
        if ($actual!=null && $suffix===substr($actual,strlen($actual)-strlen($suffix))) {
            $this->assertOk++;
        } else {
            $this->assertError++;
            if ($message=='') {
                $this->errorText .= 'Failed asserting that ' . $actual. '  ends with '.$suffix."<br />";
            } else {
                $this->errorText .= $message."<br />";
            }
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertStringEndsNotWith($suffix,$actual,$message='') {
        if ($actual==null || $suffix!==substr($actual,strlen($actual)-strlen($suffix))) {
            $this->assertOk++;
        } else {
            $this->assertError++;
            if ($message=='') {
                $this->errorText .= 'Failed asserting that ' . $actual. '  ends with '.$suffix."<br />";
            } else {
                $this->errorText .= $message."<br />";
            }
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertSame($expected,$actual,$message='') {
        if ($expected===$actual) {
            $this->assertOk++;
        } else {
            $this->assertError++;
            if ($message=='') {
                $this->errorText .= 'Failed asserting that ' . $actual. ' is identical to '.$expected."<br />";
            } else {
                $this->errorText .= $message."<br />";
            }
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertNotSame($expected,$actual,$message='') {
        if ($expected!==$actual) {
            $this->assertOk++;
        } else {
            $this->assertError++;
            if ($message=='') {
                $this->errorText .= 'Failed asserting that ' . $actual. ' is different to '.$expected."<br />";
            } else {
                $this->errorText .= $message."<br />";
            }
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertTrue($condition,$message='') {
        if ($condition===true) {
            $this->assertOk++;
        } else {
            if ($message=='') {
                $this->errorText .= 'Failed asserting that false is true'."<br />";
            } else {
                $this->errorText .= $message."<br />";
            }
            $this->assertError++;
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertFalse($condition,$message='') {
        if ($condition===false) {
            $this->assertOk++;
        } else {
            if ($message=='') {
                $this->errorText .= 'Failed asserting that true is false'."<br />";
            } else {
                $this->errorText .= $message."<br />";
            }
            $this->assertError++;
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertNotNull($object,$message='') {
        if (null!==$object) {
            $this->assertOk++;
        } else {
            if ($message=='') {
                $this->errorText .= "Failed asserting that '".$object."' is not null"."<br />";
            } else {
                $this->errorText .= $message."<br />";
            }
            $this->assertError++;
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertNull($object,$message='') {
        if (null===$object) {
            $this->assertOk++;
        } else {
            if ($message=='') {
                $this->errorText .= "Failed asserting that '".$object."' is null"."<br />";
            } else {
                $this->errorText .= $message."<br />";
            }
            $this->assertError++;
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertGreaterThan($expected, $actual, string $message = '') {
        if ($actual > $expected) {
            $this->assertOk++;
        } else {
            if ($message=='') {
                $this->errorText .= 'Failed asserting that '.$actual.'  is greater then '.$expected."<br />";
            } else {
                $this->errorText .= $message."<br />";
            }
            $this->assertError++;
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertGreaterThanOrEqual($expected, $actual, string $message = '') {
        if ($actual >= $expected) {
            $this->assertOk++;
        } else {
            if ($message=='') {
                $this->errorText .= 'Failed asserting that '.$actual.'  is greater then or equal '.$expected."<br />";
            } else {
                $this->errorText .= $message."<br />";
            }
            $this->assertError++;
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertLessThan($expected, $actual, string $message = '') {
        if ($actual < $expected) {
            $this->assertOk++;
        } else {
            if ($message=='') {
                $this->errorText .= 'Failed asserting that '.$actual.'  is less then '.$expected."<br />";
            } else {
                $this->errorText .= $message."<br />";
            }
            $this->assertError++;
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertLessThanOrEqual($expected, $actual, string $message = '') {
        if ($actual <= $expected) {
            $this->assertOk++;
        } else {
            if ($message=='') {
                $this->errorText .= 'Failed asserting that '.$actual.'  is less then or equal '.$expected."<br />";
            } else {
                $this->errorText .= $message."<br />";
            }
            $this->assertError++;
        }
    }

    /**
     * PHPUnit_Framework
     */
    public function assertCount($expectedCount, $haystack, string $message = '') {
        if (is_countable($haystack)) {
            if ($expectedCount == sizeof($haystack)) {
                $this->assertOk++;
            } else {
                if ($message == '') {
                    $this->errorText .= 'Failed asserting that actual size ' . sizeof($haystack) . '  matches expected size ' . $expectedCount . "<br />";
                } else {
                    $this->errorText .= $message . "<br />";
                }
                $this->assertError++;
            }
        } else {
            if ($message == '') {
                $this->errorText .= "Failed asserting that object is countable"."<br />";
            } else {
                $this->errorText .= $message . "<br />";
            }
            $this->assertError++;
        }
    }

    /******************************[ Only in Levis Php Framework ]**************************/

    /**
     * collected test results
     * PHPUnit_Framework
     * @return stdClass
     */
    public function assertGetUnitTestResult() {
        $ret = new stdClass();
        $ret->testResult = $this->assertError==0;
        $ret->assertError = $this->assertError;
        $ret->assertOk = $this->assertOk;
        $ret->errorText = $this->errorText;
        return $ret;
    }

    /**
     * Call url
     * PHPUnit_Framework
     * @param $url the url
     * @param bool $isResultJson default=true
     * @param null $post  associative array of post parameter
     * @return stdClass|null
     */
    public function callTestUrl($url,$isResultJson=true, $post=null){
        if ($url==null || strlen($url)==0)
            return null;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_COOKIE,"LPFW=kf9p3b0pk1hnain1gmh8pqso36");
        if($post!=null) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($post));
        }
        $resp = curl_exec($ch);
        if ($resp===false)
            return null;
        $ret = new stdClass();
        if ($isResultJson)
            $ret->content = json_decode($resp,true);
        else
            $ret->content = $resp;
        $ret->http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $ret->content_type = curl_getinfo($ch,CURLINFO_CONTENT_TYPE);
        curl_close($ch);
        return $ret;
    }

    /**
     * PHPUnit_Framework
     */
    public function startNewTestFunction() {
        $this->expectedException = null;
    }

    /**
     * PHPUnit_Framework
     */
    public function getExpectedException() {
        return $this->expectedException;
    }

}

