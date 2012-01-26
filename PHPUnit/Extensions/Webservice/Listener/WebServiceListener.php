<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2011, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2002-2011 Sebastian Bergmann <sebastian@phpunit.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.6.0
 */

/**
 * A test listener to interacting with webservices.
 *
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2011 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 3.6.0
 */
class WebServiceListener implements PHPUnit_Framework_TestListener
{
    /**
     * Instance of the Extensions_Webservice_Listener_Factory
     * @var Extensions_Webservice_Listener_Factory
     */
    private $factory;

    /**
     * Instance of the Extensions_Webservice_Listener_Loader
     * @var Extensions_Webservice_Listener_Loader
     */
    private $loader;

    /**
     * Instance of the logger observing the http client to capture the response and its header.
     * @var Extensions_Webservice_Listener_Logger_Interface
     */
    private $logger;

    /**
     * Instance of the http client used to send a request to the defined webservice.
     * @var Extensions_Webservice_Listener_Http_Client_Interface
     */
    private $httpClient;

    /**
     * Provides information about the configuration details of the listener.
     * @var array
     */
    private $configuration = array();

    /**
     * Contains the mapping information of which urls a test has to call
     * @var array
     */
    private $mapping = array();

    /**
     * Constructor of the class.
     *
     * Structure of the configuration array:
     * <code>
     * Array(
     *     'httpClient' => 'MyHttpClient',
     *     'logger' => 'MyLogger',
     * )
     * </code>
     *
     * @param Extensions_Webservice_Listener_Factory  $factory
     * @param Extensions_Webservice_Listener_Loader   $loader
     * @param array                                   $configuration
     */
    public function __construct(
        Extensions_Webservice_Listener_Factory $factory,
        Extensions_Webservice_Listener_Loader_Interface $loader,
        array $configuration)
    {
        $this->factory       = $factory;
        $this->loader        = $loader;
        $this->configuration = $configuration;
    }

    /**
     * An error occurred.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    /**
     * A failure occurred.
     *
     * @param  PHPUnit_Framework_Test                 $test
     * @param  PHPUnit_Framework_AssertionFailedError $e
     * @param  float                                  $time
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }

    /**
     * Incomplete test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    /**
     * Skipped test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     * @since  Method available since Release 3.0.0
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
    }

    /**
     * A test started.
     *
     * @param  PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        //print_r(get_class_methods(get_class($test)));die;

        if ($test instanceof PHPUnit_Framework_Warning) {
            return;
        }

        $name = $test->getName();
        if (!isset($this->mapping[$name])) {
            // log that there is no url set for this test
            return;
        }

        $testMap = $this->mapping[$name];
        foreach ($testMap as $data) {
            $response = $this->httpClient->get($data['url'], $data['params']);
            $this->logger->setFilename($test->getName());
            $this->logger->log($response);
        }
    }

    /**
     * A test ended.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        if ($test instanceof PHPUnit_Framework_Warning) {
            return;
        }

    }

    /**
     * A test suite started.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->factory->register('logger', $this->configuration['logger']);
        $this->factory->register('httpClient', $this->configuration['httpClient']);

        $this->httpClient = $this->factory->getInstanceOf('httpClient');
        $this->logger     = $this->factory->getInstanceOf('logger');

        $this->loadMapping();

    }

    /**
     * A test suite ended.
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
    }



    protected function loadMapping()
    {
        if (empty($this->mapping)) {
            $this->mapping = $this->loader->load($this->configuration['mappingFile']);
        }
        return $this->mapping;
    }
}
