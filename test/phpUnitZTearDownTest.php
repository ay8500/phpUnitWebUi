<?php

class phpUnitZTearDownTest extends PHPUnit_Framework_TestCase
{

    public function testTearDown()
    {
        $this->assertSame("Ok", $_SESSION["phpunit_setup"]);
        $this->assertSame("TearDown", $_SESSION["phpunit_tearDown"]);
    }

    public function testEmpty()
    {
        $_SESSION["phpunit_tearDown"]=null;
    }
}