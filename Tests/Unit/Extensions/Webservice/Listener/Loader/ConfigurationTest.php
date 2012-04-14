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
 * Basic http client to request information from an url via GET method.
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
class Extensions_Webservice_Listener_Loader_ConfigurationTest extends Extensions_Webservice_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @covers Extensions_Webservice_Listener_Loader_Configuration::load
     */
    public function testLoadExpectingInvalidArgumentException()
    {
        $loader = new Extensions_Webservice_Listener_Loader_Configuration();
        $loader->load('Tux');
    }

    /**
     * @covers Extensions_Webservice_Listener_Loader_Configuration::load
     * @covers Extensions_Webservice_Listener_Loader_Configuration::getDomFromFile
     * @covers Extensions_Webservice_Listener_Loader_Configuration::transcode
     */
    public function testLoad()
    {
        $expected = array(
            'serializer' => 'Extensions_Webservice_Serializer_Http_Response',
            'Example_TestCase' => array(
                'testGetData' => array(
                    'locations' => array(
                        'expected' => array(
                            'url' => 'http://example.org/data.json',
                            'params' => array(),
                        ),
                        array(
                            'url' => 'http://example.org/data.xml',
                            'params' => array(),
                        ),
                        array(
                            'url' => 'http://example.org/data.txt',
                            'params' => array(),
                        ),
                    ),
                ),
            ),
            'Extensions_Webservice_Constraint_JsonErrorMessageProviderTest' => array(
                'testTranslateTypeToPrefix with data set "expected"' => array(
                    'serializer' => 'Extensions_Webservice_Serializer_Http_Response',
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
                    ),
                ),
                'testDetermineJsonError' => array(
                    'locations' => array(
                        array(
                            'url' => 'http://example.org/data.json',
                            'params' => array(),
                        ),
                    ),
                ),
            ),
        );

        $loader = new Extensions_Webservice_Listener_Loader_Configuration();
        $this->assertEquals($expected, $loader->load('../../../../../Tests/_files/configuration.xml'));
    }

    /**
     * @dataProvider extractSerializerClassnameDataprovider
     * @covers Extensions_Webservice_Listener_Loader_Configuration::extractSerializerClassname
     */
    public function testExtractSerializerClassnameNoElementFound($path)
    {
        $configuration = '
            <listener>
                <test name="testGetData">
                    <location href="http://example.org/data.json" />
                    <location href="http://example.org/data.xml" />
                    <location href="http://example.org/data.txt" />
                </test>
                <test name="testReadData">
                    <location href="http://example.org/data.json" />
                </test>
                <test name=\'testTranslateTypeToPrefix with data set "expected"\'>
                    <location href="http://example.org/data.json">
                        <query>
                          <param name="mascott[]">tux</param>
                          <param name="mascott[RedHat]">beastie</param>
                          <param name="os">Linux</param>
                        </query>
                    </location>
                </test>
            </listener>
        ';
        $loader = $this->getProxyBuilder('Extensions_Webservice_Listener_Loader_Configuration')
            ->setMethods(array('extractSerializerClassname'))
            ->getProxy();

        $dom = new DOMDocument();
        $dom->loadXml($configuration);
        $xpath = new DOMXpath($dom);
        $node = $xpath->query($path)->item(0);
        $this->assertEmpty($loader->extractSerializerClassname($node));
    }

    /**
     * @dataProvider extractSerializerClassnameDataprovider
     * @covers Extensions_Webservice_Listener_Loader_Configuration::extractSerializerClassname
     */
    public function testExtractSerializerClassname($path)
    {
        $configuration = '
            <listener>
                <serializer>Extensions_Webservice_Serializer_Http_Response</serializer>
                <test name=\'testTranslateTypeToPrefix with data set "expected"\'>
                    <serializer>Extensions_Webservice_Serializer_Http_Response</serializer>
                    <location href="http://example.org/data.json">
                        <query>
                          <param name="mascott[]">tux</param>
                          <param name="mascott[RedHat]">beastie</param>
                          <param name="os">Linux</param>
                        </query>
                    </location>
                </test>
            </listener>
        ';
        $loader = $this->getProxyBuilder('Extensions_Webservice_Listener_Loader_Configuration')
            ->setMethods(array('extractSerializerClassname'))
            ->getProxy();

        $dom = new DOMDocument();
        $dom->loadXml($configuration);
        $xpath = new DOMXpath($dom);
        $node = $xpath->query($path)->item(0);
        $this->assertEquals(
            'Extensions_Webservice_Serializer_Http_Response',
            $loader->extractSerializerClassname($node)
        );
    }

    /**
     * @covers Extensions_Webservice_Listener_Loader_Configuration::extractLocations
     */
    public function testExtractLocations()
    {
        $expected = array(
            array(
                'url'   => 'http://example.org/data.xml',
                'params' => array(),
            ),
            array(
                'url'   => 'http://example.org/data.json',
                'params' => array(
                    'mascott' => array(
                        'tux',
                        'RedHat' => 'beastie',
                    ),
                    'os'      => 'Linux',
                ),
            ),
        );

        $configuration = '
            <listener>
                <test name=\'testTranslateTypeToPrefix with data set "expected"\'>
                    <serializer>Extensions_Webservice_Serializer_Http_Response</serializer>
                    <location href="http://example.org/data.xml" />
                    <location href="http://example.org/data.json">
                        <query>
                          <param name="mascott[]">tux</param>
                          <param name="mascott[RedHat]">beastie</param>
                          <param name="os">Linux</param>
                        </query>
                    </location>
                </test>
            </listener>
        ';
        $loader = $this->getProxyBuilder('Extensions_Webservice_Listener_Loader_Configuration')
            ->setMethods(array('extractLocations'))
            ->getProxy();

        $dom = new DOMDocument();
        $dom->loadXml($configuration);
        $xpath = new DOMXpath($dom);
        $node = $xpath->query("//listener/test")->item(0);
        $this->assertEquals(
            $expected,
            $loader->extractLocations($node, $xpath)
        );
    }

    /*************************************************************************/
    /* Dataprovider                                                          */
    /*************************************************************************/


    public static function extractSerializerClassnameDataprovider()
    {
        return array(
            'global serializer' => array("//listener/serializer"),
            'local serializer' => array("//listener/test/serializer"),
        );
    }
}
