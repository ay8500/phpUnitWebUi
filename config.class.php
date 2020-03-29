<?php
/**
 * Created by PhpStorm.
 * User: Levi
 * Date: 07.12.2018
 * Time: 00:06
 */

namespace maierlabs\phpunit;

class config {

    /**
     * @var string Title of the php unit test page
     */
    public static $SiteTitle = 'PhpUnit webinterface';

    /**
     * @var string sender and reply e-mail address
     */
    public static $siteMail ="code@blue-l.de";

    /**
     * @var string start directory for *Test.php test files
     */
    public static $projects = array(
        array("name"=>"Levis PHP Framework","dir"=>__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."lpfw"),
        array("name"=>"Brassai","dir"=>__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."brassai"),
        array("name"=>"Component Database","dir"=>__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."cdb"),
        array("name"=>"WebCam","dir"=>__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."webcam"),
        array("name"=>"AddressOk","dir"=>__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."addressok"),
    );


    /**
     * @var array exclude file list
     * could be inportant to use it, if the test subject has a lot of images or other non php files
     * example: ('images','..','.','.git')
     */
    public static $excludeFiles = array('images','..','.','.git');

    /**
     * @var string the version of php unit mainly used for parameter in css and js files
     */
    public static $webAppVersion = "2.01/25-11-2019";

}