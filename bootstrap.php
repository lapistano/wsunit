<?php

// pathes
define("PROJECT_DIR", __DIR__ . '/PHPUnit/Extensions/Webservice');

// load necessary libraries
require_once 'PHPUnit/Framework/TestCase.php';

// load source to be tested
require_once PROJECT_DIR . '/Constraint/JsonMatches.php';
require_once PROJECT_DIR . '/TestCase.php';
