<?php
/**
 * PHPUnit - Test listener extension
 *
 * Copyright (c) 2012 Bastian Feder <php@bastian-feder.de>.
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
 * @copyright  2012 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://github.com/lapistano/wsunit
 * @since      File available since Release 3.6.0
 */

namespace lapistano\wsunit;

/**
 * A test listener to interacting with webservices.
 *
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2012 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://github.com/lapistano/wsunit
 * @since      Class available since Release 3.6.0
 */
class WebServiceListener implements \PHPUnit_Framework_TestListener
{
    /**
     * Instance of the Extensions_Webservice_Listener_Factory
     * @var \lapistano\wsunit\Extensions_Webservice_Listener_Factory
     */
    protected $factory;

    /**
     * Instance of the Extensions_Webservice_Listener_Loader
     * @var \lapistano\wsunit\Extensions_Webservice_Listener_Loader
     */
    protected $loader;

    /**
     * Instance of the logger observing the http client to capture the response and its header.
     * @var \lapistano\wsunit\Extensions_Webservice_Listener_Logger_Interface
     */
    protected $logger;

    /**
     * Instance of the serializer transcoding the given data into a loggable format.
     * @var \lapistano\wsunit\Extensions_Webservice_Serializer
     */
    protected $serializer;

    /**
     * Instance of the http client used to send a request to the defined webservice.
     * @var \lapistano\wsunit\Http\Client\Extensions_Webservice_Listener_Http_Client_Interface
     */
    protected $httpClient;

    /**
     * Provides information about the configuration details of the listener.
     * @var array
     */
    protected $configuration = array();

    /**
     * Contains the mapping information of which urls a test has to call
     * @var array
     */
    protected $mapping = array();

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
     * @param \lapistano\wsunit\Extensions_Webservice_Listener_Factory                   $factory
     * @param \lapistano\wsunit\Loader\Extensions_Webservice_Listener_Loader_Interface   $loader
     * @param array                                                                      $configuration
     */
    public function __construct(
        \lapistano\wsunit\Extensions_Webservice_Listener_Factory $factory,
        \lapistano\wsunit\Loader\Extensions_Webservice_Listener_Loader_Interface $loader,
        array $configuration)
    {
        $this->factory       = $factory;
        $this->loader        = $loader;
        $this->configuration = $configuration;
    }

    /**
     * An error occurred.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  \Exception              $e
     * @param  float                  $time
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->errors[] = new \PHPUnit_Framework_TestFailure($test, $e);
        $this->lastTestFailed = true;
        $this->time          += $time;
    }

    /**
     * A failure occurred.
     *
     * @param  \PHPUnit_Framework_Test                 $test
     * @param  \PHPUnit_Framework_AssertionFailedError $e
     * @param  float                                  $time
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
    }

    /**
     * Incomplete test.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  \Exception              $e
     * @param  float                  $time
     */
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    /**
     * Skipped test.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  \Exception              $e
     * @param  float                  $time
     * @since  Method available since Release 3.0.0
     */
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    /**
     * A test started.
     *
     * @param  \PHPUnit_Framework_Test $test
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
        if ($test instanceof PHPUnit_Framework_Warning) {
            return;
        }

        $name = $test->getName();
        $class = get_class($test);

        if (!isset($this->mapping[$class][$name])) {
            //Proposal: log that there is no url set for this test
            return;
        }

        $configuration = $this->mapping[$class][$name];

        // load dedicated serializer for a specific test
        if (!empty($configuration['serializer'])) {
            $this->factory->register('serializer', $configuration['serializer']);
            $logger = $this->factory->getInstanceOf(
                'logger',
                $this->factory->getInstanceOf('serializer', true)
            );
        } else {
            $this->addError(
                $test,
                new \PHPUnit_Framework_OutputError(
                    sprintf(
                        'No serializer found for test: %s. '.
                        'Either define a global serializer or add one to the specific section '.
                        'in your configuration file.',
                        $test->getName()
                    )
                ),
                \PHP_Timer::$requestTime
            );
        }

        if ($this->hasDataprovider($test)) {
            $response = $this->sendRequest($configuration, $this->getRunlevel($test));
        } else {
            $response = $this->sendRequest($configuration);
        }

        // persist response
        $this->logger->registerTest($test);
        $this->logger->log($response);
    }

    /**
     * A test ended.
     *
     * @param  \PHPUnit_Framework_Test $test
     * @param  float                  $time
     */
    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        if ($test instanceof \PHPUnit_Framework_Warning) {
            return;
        }

    }

    /**
     * A test suite started.
     *
     * @param  \PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        $this->factory->register('logger', $this->configuration['logger']);
        $this->factory->register('httpClient', $this->configuration['httpClient']);

        $this->httpClient = $this->factory->getInstanceOf('httpClient');

        $this->loadMapping();

        // get serializer from configuration
        if (!empty($this->mapping['serializer'])) {
            $this->factory->register('serializer', $this->mapping['serializer']);
            $serializer = $this->factory->getInstanceOf('serializer');
        }
        // initialize a loader only if there is a serializer defined.
        if (!empty($serializer)) {
            $this->logger = $this->factory->getInstanceOf('logger', $serializer);
        }
    }

    /**
     * A test suite ended.
     *
     * @param  \PHPUnit_Framework_TestSuite $suite
     * @since  Method available since Release 2.2.0
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
    }


    /**
     * Send a request to the service provider.
     *
     * @param array  $configuration
     * @param string $runlevel
     *
     * @return Extensions_Webservice_Listener_Http_Response
     */
    protected function sendRequest($configuration, $runlevel = 0)
    {
        if (!empty($configuration['locations'][$runlevel])) {
            $location = $configuration['locations'][$runlevel];
        } else {
            $location = end($configuration['locations']);
        }
        return $this->httpClient->get($location['url'], $location['params']);
    }

    /**
     * Determines which location shall be fetched from the list of configured locations.
     *
     * @param \PHPUnit_Framework_TestCase $test
     * @return string
     */
    protected function getRunlevel(\PHPUnit_Framework_TestCase $test)
    {
        return $this->extractRunlevelFromTestName($test->getName());
    }

    /**
     * Extracts the runlevel from the test name.
     *
     * @param string $name
     * @return string
     */
    protected function extractRunlevelFromTestName($name)
    {
        // reg exp matches onto the following strings:
        // [...] with data set "error syntax in expected JSON" [...]
        // [...] with data set #0 [...]
        preg_match('(with data set (?:(?:#(\d+))|"([^"]+)"))', $name, $matches);

        // normalize the array:
        // array_filter with no callback removes empty entries
        // array_values resets the keys
        $matches = array_values(array_filter($matches));

        return (!empty($matches[1])) ? $matches[1] : 0;
    }

    /**
     * Fetches the information about which test has to request which url.
     *
     * @return array
     */
    protected function loadMapping()
    {
        if (empty($this->mapping)) {
            $this->mapping = $this->loader->load($this->configuration['mappingFile']);
        }
        return $this->mapping;
    }

    /**
     * Determines, if there is a data provider defined for the current test case.
     *
     * @param \PHPUnit_Framework_Test $test
     * @return boolean
     */
    protected function hasDataprovider(\PHPUnit_Framework_TestCase $test)
    {
        $annotations = $test->getAnnotations();
        return !empty($annotations['method']['dataProvider']);
    }
}
