<?php

class exceptionTest extends PHPUnit_Framework_TestCase
{


    public function testFunctionNotFoundException()
    {
        $this->setExpectedException("Call to undefined function");
        $test = functionNotExists();
        $this->assertSame(null,$test );
    }

    public function testException()
    {
        $this->setExpectedException("This is a test exception");
        throw new Exception("This is a test exception",2);
    }

}
?>