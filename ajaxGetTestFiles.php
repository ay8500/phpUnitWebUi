<?php
include 'phpunit.class.php';

header('Content-Type: application/json');
$pu = new \maierlabs\phpunit\phpunit();
echo json_encode($pu->getTestFilesForAllProjects());

