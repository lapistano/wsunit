<?php

// pathes
define("PROJECT_DIR", __DIR__ . '/src/lapistano/wsunit');
define("TEST_DIR", __DIR__ . '/Tests');

// load fixture file
require_once TEST_DIR . '/ExtensionsWebservicesTestCase.php';
require_once TEST_DIR . '/_files/wsTestCaseMock.php';

// load necessary libraries
require_once 'PHPUnit/Framework/TestCase.php';
require_once TEST_DIR . '/libs/lapistano/proxy-object/bootstrap.php';

// interface files
require_once PROJECT_DIR . '/Loader/Interface.php';
require_once PROJECT_DIR . '/Logger/Interface.php';
require_once PROJECT_DIR . '/Serializer/Interface.php';
require_once PROJECT_DIR . '/Http/Client/Interface.php';

// listener files
require_once PROJECT_DIR . '/WebServiceListener.php';
require_once PROJECT_DIR . '/Factory.php';
require_once PROJECT_DIR . '/Http/Client.php';
require_once PROJECT_DIR . '/Http/Response.php';
require_once PROJECT_DIR . '/Loader/Configuration.php';
require_once PROJECT_DIR . '/Logger.php';
require_once PROJECT_DIR . '/Serializer/Abstract.php';
require_once PROJECT_DIR . '/Serializer/Type/Abstract.php';
require_once PROJECT_DIR . '/Serializer/Type/Array.php';
require_once PROJECT_DIR . '/Serializer/Type/Xml.php';
require_once PROJECT_DIR . '/Serializer/Http/Response.php';
