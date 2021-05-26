<?php


class phpUnitTest extends PHPUnit_Framework_TestCase
{
    private $pu;

    public function setUp()
    {
        include_once "../phpunit.class.php";
        $_SESSION["phpunit_setup"] = "Ok";
        $_SESSION["phpunit_tearDown"] = "Ok";
        $this->pu = new maierlabs\phpunit\phpunit();

    }

    public function tearDown()
    {
        $_SESSION["phpunit_tearDown"] = "TearDown";
    }

    /**
     * @ignore
     */
    public function testIgnore()
    {
        $this->assertTrue(true);
    }

    /**
     * @skip
     */
    public function testSkip()
    {
        notExistingFunction();
    }

    public function testSetUp()
    {
        $this->assertSame("Ok", $_SESSION["phpunit_setup"]);
        $this->assertSame("Ok", $_SESSION["phpunit_tearDown"]);
    }

    /**
     * @test
     */public function getTestFiles()
    {
        $files = $this->pu->getTestFilesForAllProjects();
        $this->assertGreaterThan(1,sizeof($files));
        $this->assertStringEndsWith(".php",$files[0]["file"]);
        $this->assertGreaterThan(0,$files[0]["tests"]);
        $this->assertGreaterThan(0,$files[0]["asserts"]);
    }

    /**
     * @test
     * @author Levi
     * @author Maierlabs
     */public function assets()
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

    /**
     * @ignore
     * @test
     */
    public function assetsIgnoreResult()
    {
        $this->assertTrue(false);
        $this->assertFalse(true);
        $this->assertSame(2, 1);
        $this->assertSame("2", "1");
        $this->assertSame(true, false);
        $this->assertNotSame("1", "1");
        $this->assertNotSame(false, false);
        $this->assertStringStartsWith("xhp","phpunit");
        $this->assertStringStartsNotWith("php","phpunit");
        $this->assertStringEndsWith("php","phpunit");
        $this->assertStringEndsNotWith("unit","phpunit");
        $notNullObject = new DateTime();
        $this->assertNull($notNullObject);
        $this->assertNotNull(null);
        $this->assertGreaterThan(4,3);
        $this->assertGreaterThanOrEqual(15,13);
        $this->assertGreaterThanOrEqual(25,22);
        $this->assertLessThan(30,32);
        $this->assertLessThanOrEqual(30,42);
        $this->assertLessThanOrEqual(30,53);
        $a = array();
        $this->assertCount(1,$a);
        array_push($a, "2");array_push($a, "3");
        $this->assertCount(12,$a);
    }

}