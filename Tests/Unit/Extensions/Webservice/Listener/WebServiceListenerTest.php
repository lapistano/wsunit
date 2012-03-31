<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2002-2011, Sebastian Bergmann <sb@sebastian-bergmann.de>.
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
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2002-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.6.0
 */

/**
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2011 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.6.0
 */

class Extensions_Webservice_ListenerTest extends Extensions_Webservice_TestCase
{
    /**
     * Provides an instance of the WebServiceListener.
     *
     * @return WebServiceListener
     */
    protected function getListener()
    {
        return new WebServiceListener(
            $this->getFactoryStub(),
            $this->getLoaderFake(),
            $this->getConfiguration()
        );
    }

    /**
     * @covers WebServiceListener::__construct
     */
    public function testConstruct()
    {
        $this->assertAttributeInternalType(
            'array',
            'configuration',
            $this->getListener()
        );
    }

    /**
     * @dataProvider hasDataproviderDataprovider
     * @covers WebServiceListener::hasDataprovider
     */
    public function testHasDataprovider($expected, $annotations)
    {
        $testCase = $this->getMockBuilder('wsTestCaseMock')
            ->disableOriginalConstructor()
            ->setMethods(array('getAnnotations'))
            ->getMock();
        $testCase
            ->expects($this->once())
            ->method('getAnnotations')
            ->will($this->returnValue($annotations));

        $wst = $this->ProxyBuilder('WebServiceListener')
            ->disableOriginalConstructor()
            ->setMethods(array('hasDataprovider'))
            ->getProxy();
        $this->assertEquals($expected, $wst->hasDataprovider($testCase));
    }

    /**
     * @dataProvider loadMappingDataprovider
     * @covers WebServiceListener::loadMapping
     */
    public function testLoadMapping($mapping)
    {
        $loader = $this->getLoaderFake(array('load'));
        $loader
            ->expects($this->any())
            ->method('load')
            ->will($this->returnValue($mapping));

        $wst = $this->ProxyBuilder('WebServiceListener')
            ->disableOriginalConstructor()
            ->setMethods(array('loadMapping'))
            ->setProperties(array('mapping', 'loader', 'configuration'))
            ->getProxy();
        $wst->mapping = $mapping;
        $wst->loader = $loader;
        $wst->configuration = $this->getConfiguration();

        $this->assertEquals($mapping, $wst->loadMapping());
    }

    /**
     * @dataProvider getRunlevelDataprovider
     * @covers WebServiceListener::getRunlevel
     * @covers WebServiceListener::extractRunlevelFromTestName
     */
    public function testGetRunlevel($expected, $name)
    {
        $testCase = $this->getMockBuilder('wsTestCaseMock')
            ->disableOriginalConstructor()
            ->setMethods(array('getName'))
            ->getMock();
        $testCase
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name));

        $wst = $this->ProxyBuilder('WebServiceListener')
            ->disableOriginalConstructor()
            ->setMethods(array('getRunlevel'))
            ->getProxy();
        $this->assertEquals($expected, $wst->getRunlevel($testCase));
    }

    /**
     * @dataProvider sendRequestDataprovider
     * @covers WebServiceListener::sendRequest
     */
    public function testSendRequest($runlevel, $url)
    {
        $config = array(
            'locations' => array(
                'expected' => array(
                    'url' => 'http://example.org/data.json',
                    'params' => array(
                        'mascott' => array(
                            'tux',
                            'RedHat' => 'beastie',
                         ),
                        'os' => 'Linux',
                    ),
                ),
                array(
                    'url' => 'http://example.org/data.xml',
                    'params' => array(),
                ),
                array(
                    'url' => 'http://example.org/data.txt',
                    'params' => array(),
                ),
            )
        );

        $client = $this->getHttpClientFake(array('get'));
        $client
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($url),
                $this->isType('array')
            )
            ->will($this->returnValue('Tux'));

        $wst = $this->ProxyBuilder('WebServiceListener')
            ->disableOriginalConstructor()
            ->setProperties(array('logger', 'httpClient'))
            ->setMethods(array('sendRequest'))
            ->getProxy();
        $wst->httpClient = $client;

        $this->assertEquals('Tux', $wst->sendRequest($config, $runlevel));
    }

    /*************************************************************************/
    /* Dataprovider                                                          */
    /*************************************************************************/

    public static function sendRequestDataprovider()
    {
        return array(
            'string identification' => array('expected', 'http://example.org/data.json'),
            'invalid string identification' => array('tux', 'http://example.org/data.txt'),
            'numeric identification' => array('0', 'http://example.org/data.xml'),
            'invalid numeric identification' => array('42', 'http://example.org/data.txt'),
        );
    }

    public static function getRunlevelDataprovider()
    {
        return array(
            'unnamed dataprovider in 1st iteration' => array(0, 'testCase::test with data set #0'),
            'unnamed dataprovider in 2nd iteration' => array(1, 'testCase::test with data set #1'),
            'named iteration of dataprovider'       => array('dataName', 'testCase::test with data set "dataName"'),
        );
    }

    public static function loadMappingDataprovider()
    {
        return array(
            'empty mapping' => array(array()),
            'set mapping'   => array(array('tux')),
        );
    }

    public static function hasDataproviderDataprovider()
    {
        return array(
            'with data provider' => array(
                true,
                array(
                   'method' => array(
                       'dataProvider' => array(
                           'translateTypeToPrefixDataprovider',
                        )
                    ),
                )
            ),
            'without data provider' => array(false, array('method' => array())),
            'without data provider ("method" not even set)' => array(false, array()),
        );
    }
}