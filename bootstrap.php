<?php

// pathes
define("PROJECT_DIR", __DIR__ . '/src/lapistano/wsunit');
define("TEST_DIR", __DIR__ . '/Tests');

$loader = require_once __DIR__ . '/vendor/autoload.php';
$loader->add('lapistano\ProxyObject', __DIR__ . '/vendor/lapistano/proxy-object/src');

require_once TEST_DIR . '/ExtensionsWebservicesTestCase.php';
require_once TEST_DIR . '/_files/wsTestCaseMock.php';
require_once PROJECT_DIR . '/WebServiceListener.php';
require_once PROJECT_DIR . '/WebserviceListenerFactory.php';
