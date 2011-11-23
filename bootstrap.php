<?php

// pathes
define("PROJECT_DIR", __DIR__ . '/PHPUnit/Extensions/Webservice');
define("TEST_DIR", __DIR__ . '/Tests');

// load necessary libraries
require_once 'PHPUnit/Framework/TestCase.php';
require_once TEST_DIR . '/libs/lapistano/proxy-object/bootstrap.php';

// load source to be tested
require_once PROJECT_DIR . '/Constraint/JsonMatches.php';
require_once PROJECT_DIR . '/TestCase.php';

// listener files
require_once PROJECT_DIR . '/Listener/WebServiceListener.php';
require_once PROJECT_DIR . '/Listener/Factory.php';

// interface files
require_once PROJECT_DIR . '/Listener/Loader/Interface.php';
require_once PROJECT_DIR . '/Listener/Logger/Interface.php';
require_once PROJECT_DIR . '/Listener/HttpClient/Interface.php';