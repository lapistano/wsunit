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

namespace lapistano\wsunit\Logger;

use lapistano\ProxyObject\ProxyBuilder;
use lapistano\wsunit\Wsunit_TestCase;

/**
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2011 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.6.0
 */
class LoggerFilesystemTest extends Wsunit_TestCase
{
    /**
     * Provides an instance of the abstract SerializerAbstract class.
     *
     * @return \lapistano\wsunit\SerializerAbstract
     */
    protected function getSerializerFixture()
    {
        return $this->getMockBuilder('\lapistano\wsunit\Serializer\SerializerAbstract')
            ->getMockForAbstractClass();
    }

    /**
     * @covers \lapistano\wsunit\Logger\LoggerFilesystem::registerTest
     * @covers \lapistano\wsunit\Logger\LoggerFilesystem::__construct
     */
    public function testRegisterTest()
    {
        // 'testLogger with data set "expected"'
        $test = $this->getMockBuilder('\\PHPUnit_Framework_Test')
            ->setMethods(array('run'))
            ->getMockForAbstractClass();

        $logger = new LoggerFilesystem($this->getSerializerFixture());
        $logger->registerTest($test);
        $this->assertAttributeInstanceOf('\\PHPUnit_Framework_Test', 'test', $logger);
    }

    /**
     * @dataProvider sanitizeStringDataprovider
     * @covers \lapistano\wsunit\Logger\LoggerFilesystem::sanitizeString
     */
    public function testSanitizeString($expected, $string)
    {
        $logger = $this->getProxyBuilder('\\lapistano\\wsunit\\Logger\\LoggerFilesystem')
            ->disableOriginalConstructor()
            ->setMethods(array('sanitizeString'))
            ->getProxy();
        $this->assertEquals($expected, $logger->sanitizeString($string));
    }
    public static function sanitizeStringDataprovider()
    {
        return array(
            'string with unallowed char' => array(
                'testTranslatTypeToPrefix with data set expected',
                'testTranslatTypeToPrefix with data set "expected"',
            ),
            'string without unallowed char' => array(
                'Tux',
                'Tux',
            ),
        );
    }

    /**
     * @dataProvider generateFilenameDataprovider
     * @covers \lapistano\wsunit\Logger\LoggerFilesystem::generateFilename
     */
    public function testGenerateFilename($expected, $string)
    {
        $logger = $this->getProxyBuilder('\\lapistano\\wsunit\\Logger\\LoggerFilesystem')
            ->disableOriginalConstructor()
            ->setMethods(array('generateFilename'))
            ->getProxy();
        $this->assertEquals($expected, $logger->generateFilename($string));
    }
    public static function generateFilenameDataprovider()
    {
        return array(
            'string with whitespace' => array(
                'TestTranslatTypeToPrefixWithDataSetExpected',
                'testTranslatTypeToPrefix with data set "expected"',
            ),
            'string without whitespace' => array(
                'Tux',
                'Tux',
            ),
        );
    }

    /**
     * @covers \lapistano\wsunit\Logger\LoggerFilesystem::Log
     */
    public function testLog()
    {
        $this->markTestSkipped('Due to called user land function!');
    }
}
