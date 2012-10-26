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

namespace lapistano\wsunit\Loader;

use lapistano\wsunit\Wsunit_TestCase;

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
class LoaderConfigurationTest extends Wsunit_TestCase
{
    /**
     * Provides an example loader configuration.
     *
     * @return string
     */
    protected function getLoaderConfigurationXmlFixture()
    {
        return '
            <listener>
                <test name=\'testTranslateTypeToPrefix with data set "expected"\'>
                    <serializer>SerializerHttpResponse</serializer>
                    <location href="http://example.org/data.xml"/>
                    <location href="http://blog.bastian-feder.de/blog.rss">
                        <query>
                          <param name="mascott[]">tux</param>
                          <param name="mascott[RedHat]">beastie</param>
                          <param name="os">Linux</param>
                        </query>
                    </location>
                </test>
            </listener>
        ';
    }

    /**
     * @expectedException \InvalidArgumentException
     * @covers \lapistano\wsunit\Loader\LoaderConfiguration::load
     */
    public function testLoadExpectingInvalidArgumentException()
    {
        $loader = new LoaderConfiguration();
        $loader->load('Tux');
    }

    /**
     * @covers \lapistano\wsunit\Loader\LoaderConfiguration::load
     * @covers \lapistano\wsunit\Loader\LoaderConfiguration::getDomFromFile
     * @covers \lapistano\wsunit\Loader\LoaderConfiguration::transcode
     */
    public function testLoad()
    {
        $expected = array(
            'serializer' => '\lapistano\wsunit\Serializer\Http\SerializerHttpResponse',
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
            'lapistano\wsunit\Logger\LoggerFilesystemTest' => array(
                'testSanitizeString with data set "string without unallowed char"' => array(
                    'serializer' => '\lapistano\wsunit\Serializer\Http\SerializerHttpResponse',
                    'locations' => array(
                        'expected' => array(
                            'url' => 'http://blog.bastian-feder.de/blog.rss',
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
                'testLog' => array(
                    'locations' => array(
                        array(
                            'url' => 'http://blog.bastian-feder.de/blog.rss',
                            'params' => array(),
                        ),
                    ),
                ),
            ),
        );

        $loader = new LoaderConfiguration();
        $this->assertEquals($expected, $loader->load('../../../../Tests/_files/configuration.xml'));
    }

    /**
     * @dataProvider extractSerializerClassnameDataprovider
     * @covers\lapistano\wsunit\Loader\LoaderConfiguration::extractSerializerClassname
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
                    <location href="http://blog.bastian-feder.de/blog.rss">
                        <query>
                          <param name="mascott[]">tux</param>
                          <param name="mascott[RedHat]">beastie</param>
                          <param name="os">Linux</param>
                        </query>
                    </location>
                </test>
            </listener>
        ';
        $loader = $this->getProxyBuilder('\lapistano\wsunit\Loader\LoaderConfiguration')
            ->setMethods(array('extractSerializerClassname'))
            ->getProxy();

        $dom = new \DOMDocument();
        $dom->loadXml($configuration);
        $xpath = new \DOMXpath($dom);
        $node = $xpath->query($path)->item(0);
        $this->assertEmpty($loader->extractSerializerClassname($node));
    }

    /**
     * @dataProvider extractSerializerClassnameDataprovider
     * @covers\lapistano\wsunit\Loader\LoaderConfiguration::extractSerializerClassname
     */
    public function testExtractSerializerClassname($path)
    {
        $configuration = '
            <listener>
                <serializer>SerializerHttpResponse</serializer>
                <test name=\'testTranslateTypeToPrefix with data set "expected"\'>
                    <serializer>SerializerHttpResponse</serializer>
                    <location href="http://blog.bastian-feder.de/blog.rss">
                        <query>
                          <param name="mascott[]">tux</param>
                          <param name="mascott[RedHat]">beastie</param>
                          <param name="os">Linux</param>
                        </query>
                    </location>
                </test>
            </listener>
        ';
        $loader = $this->getProxyBuilder('\lapistano\wsunit\Loader\LoaderConfiguration')
            ->setMethods(array('extractSerializerClassname'))
            ->getProxy();

        $dom = new \DOMDocument();
        $dom->loadXml($configuration);
        $xpath = new \DOMXpath($dom);
        $node = $xpath->query($path)->item(0);
        $this->assertEquals(
            'SerializerHttpResponse',
            $loader->extractSerializerClassname($node)
        );
    }

    /**
     * @covers\lapistano\wsunit\Loader\LoaderConfiguration::extractLocations
     */
    public function testExtractLocations()
    {
        $expected = array(
            array(
                'url'   => 'http://example.org/data.xml',
                'params' => array(),
            ),
            array(
                'url'   => 'http://blog.bastian-feder.de/blog.rss',
                'params' => array(
                    'mascott' => array(
                        'tux',
                        'RedHat' => 'beastie',
                    ),
                    'os'      => 'Linux',
                ),
            ),
        );

        $loader = $this->getProxyBuilder('\lapistano\wsunit\Loader\LoaderConfiguration')
            ->setMethods(array('extractLocations'))
            ->getProxy();

        $dom = new \DOMDocument();
        $dom->loadXml($this->getLoaderConfigurationXmlFixture());

        $xpath = new \DOMXpath($dom);
        $node = $xpath->query("//listener/test")->item(0);

        $this->assertEquals(
            $expected,
            $loader->extractLocations($node, $xpath)
        );
    }

    /**
     * @covers\lapistano\wsunit\Loader\LoaderConfiguration::extractMetaDataFromLocation
     */
    public function testExtractMetaDataFromLocation()
    {
        $expected = array(
            'url' => 'http://blog.bastian-feder.de/blog.rss',
            'params' => array(
                'mascott' => array(
                    0 => 'tux',
                    'RedHat' => 'beastie'
                ),
                'os' => 'Linux'
            ),
        );

        $loader = $this->getProxyBuilder('\lapistano\wsunit\Loader\LoaderConfiguration')
            ->setMethods(array('extractMetaDataFromLocation'))
            ->getProxy();

        $dom = new \DOMDocument();
        $dom->loadXml($this->getLoaderConfigurationXmlFixture());

        $xpath = new \DOMXpath($dom);
        $node = $xpath->query("//listener/test")->item(0);

        $this->assertEquals(
            $expected,
            $loader->extractMetaDataFromLocation(
                $xpath->query('location', $node)->item(1),
                $xpath
            )
        );
    }

    /**
     * @covers\lapistano\wsunit\Loader\LoaderConfiguration::extractParametersFromLocation
     */
    public function testExtractParametersFromLocation()
    {
        $expected = array(
            'mascott' => array(
                0 => 'tux',
                'RedHat' => 'beastie'
            ),
            'os' => 'Linux'
        );

        $loader = $this->getProxyBuilder('\lapistano\wsunit\Loader\LoaderConfiguration')
            ->setMethods(array('extractParametersFromLocation'))
            ->getProxy();

        $dom = new \DOMDocument();
        $dom->loadXml($this->getLoaderConfigurationXmlFixture());

        $xpath = new \DOMXpath($dom);
        $location = $xpath->query("//listener/test/location")->item(1);

        $this->assertEquals(
            $expected,
            $loader->extractParametersFromLocation(
                $xpath->query('query/param', $location)
            )
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
