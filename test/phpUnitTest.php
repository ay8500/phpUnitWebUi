<?php


class phpUnitTest extends PHPUnit_Framework_TestCase
{


    public function setUp()
    {
        $_SESSION["phpunit_setup"] = "Ok";
        $_SESSION["phpunit_tearDown"] = "Ok";
    }

    public function tearDown()
    {
        $_SESSION["phpunit_tearDown"] = "TearDown";
    }

    public function testSetUp()
    {
        $this->assertSame("Ok", $_SESSION["phpunit_setup"]);
        $this->assertSame("Ok", $_SESSION["phpunit_tearDown"]);
    }

    public function testGetTestFiles()
    {
        include_once "../phpunit.class.php";
        $pu = new maierlabs\phpunit\phpunit();
        $files = $pu->getTestFiles();
        $this->assertGreaterThan(3,sizeof($files));
    }

    public function testAssets()
    {
        $this->assertTrue(true);
        $this->assertFalse(false);
        $this->assertSame(1, 1);
        $this->assertSame("1", "1");
        $this->assertSame(false, false);
        $this->assertNotSame(1, "1");
        $this->assertNotSame(true, false);
        $this->assertStringStartsWith("php","phpunit");
        $this->assertStringStartsNotWith("php",null);
        $this->assertStringStartsNotWith("unit","phpunit");
        $this->assertStringEndsWith("unit","phpunit");
        $this->assertStringEndsNotWith("php",null);
        $this->assertStringEndsNotWith("php","phpunit");
        $this->assertNull(null);
        $this->assertNotNull("");
        $this->assertGreaterThan(2,3);
        $this->assertGreaterThanOrEqual(12,13);
        $this->assertGreaterThanOrEqual(22,22);
        $this->assertLessThan(33,32);
        $this->assertLessThanOrEqual(43,42);
        $this->assertLessThanOrEqual(53,53);
        $a = array();
        $this->assertCount(0,$a);
        array_push($a, "2");array_push($a, "3");
        $this->assertCount(2,$a);
    }


}