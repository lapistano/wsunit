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
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2002-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.6.0
 */

/**
 * @package    WsUnit
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2011 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.6.0
 */
class Extensions_Webservice_TestCaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers PHPUnit_Extensions_Webservice_TestCase::assertJsonStringEqualsJsonString
     */
    public function testAssertJsonStringEqualsJsonString()
    {
        $expected = '{"Mascott" : "Tux"}';
        $actual   = '{"Mascott" : "Tux"}';
        $message  = 'Given Json strings do not match';

        PHPUnit_Extensions_Webservice_TestCase::assertJsonStringEqualsJsonString($expected, $actual, $message);
    }

    /**
     * @dataProvider validInvalidJsonDataprovider
     * @covers PHPUnit_Extensions_Webservice_TestCase::assertJsonStringEqualsJsonString
     */
    public function testAssertJsonStringEqualsJsonStringErrorRaised($expected, $actual)
    {
        try {
            PHPUnit_Extensions_Webservice_TestCase::assertJsonStringEqualsJsonString($expected, $actual);
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            return;
        }
        $this->fail('Expected exception not found');
    }

    /**
     * @covers PHPUnit_Extensions_Webservice_TestCase::assertJsonStringNotEqualsJsonString
     */
    public function testAssertJsonStringNotEqualsJsonString()
    {
        $expected = '{"Mascott" : "Beastie"}';
        $actual   = '{"Mascott" : "Tux"}';
        $message  = 'Given Json strings do match';

        PHPUnit_Extensions_Webservice_TestCase::assertJsonStringNotEqualsJsonString($expected, $actual, $message);
    }

    /**
     * @dataProvider validInvalidJsonDataprovider
     * @covers PHPUnit_Extensions_Webservice_TestCase::assertJsonStringNotEqualsJsonString
     */
    public function testAssertJsonStringNotEqualsJsonStringErrorRaised($expected, $actual)
    {
        PHPUnit_Extensions_Webservice_TestCase::assertJsonStringNotEqualsJsonString($expected, $actual);
    }

    /**
     * @covers PHPUnit_Extensions_Webservice_TestCase::assertJsonStringEqualsJsonFile
     */
    public function testAssertJsonStringEqualsJsonFile()
    {
        $file = __DIR__ . '/../../../_files/JsonData/simpleObject.js';
        $actual = json_encode(array("Mascott" => "Tux"));
        $message = '';
        PHPUnit_Extensions_Webservice_TestCase::assertJsonStringEqualsJsonFile($file, $actual, $message);
    }

    /**
     * @covers PHPUnit_Extensions_Webservice_TestCase::assertJsonStringEqualsJsonFile
     */
    public function testAssertJsonStringEqualsJsonFileExpectingExpectationFailedException()
    {
        $file = __DIR__ . '/../../../_files/JsonData/simpleObject.js';
        $actual = json_encode(array("Mascott" => "Beastie"));
        $message = '';
        try {
            PHPUnit_Extensions_Webservice_TestCase::assertJsonStringEqualsJsonFile($file, $actual, $message);
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertEquals(
                'Failed asserting that \'{"Mascott":"Beastie"}\' matches JSON string "{"Mascott":"Tux"}".',
                $e->getMessage()
            );
            return;
        }

        $this->fail('Expected Exception not thrown.');
    }

    /**
     * @covers PHPUnit_Extensions_Webservice_TestCase::assertJsonStringEqualsJsonFile
     */
    public function testAssertJsonStringEqualsJsonFileExpectingInvalidArgumentException()
    {
        $file = __DIR__ . '/../../../_files/JsonData/simpleObject.js';
        try {
            PHPUnit_Extensions_Webservice_TestCase::assertJsonStringEqualsJsonFile($file, null);
        } catch (InvalidArgumentException $e) {
            return;
        }
        $this->fail('Expected Exception not thrown.');
    }

    /**
     * @covers PHPUnit_Extensions_Webservice_TestCase::assertJsonStringNotEqualsJsonFile
     */
    public function testAssertJsonStringNotEqualsJsonFile()
    {
        $file = __DIR__ . '/../../../_files/JsonData/simpleObject.js';
        $actual = json_encode(array("Mascott" => "Beastie"));
        $message = '';
        PHPUnit_Extensions_Webservice_TestCase::assertJsonStringNotEqualsJsonFile($file, $actual, $message);
    }

    /**
     * @covers PHPUnit_Extensions_Webservice_TestCase::assertJsonStringNotEqualsJsonFile
     */
    public function testAssertJsonStringNotEqualsJsonFileExpectingInvalidArgumentException()
    {
        $file = __DIR__ . '/../../../_files/JsonData/simpleObject.js';
        try {
            PHPUnit_Extensions_Webservice_TestCase::assertJsonStringNotEqualsJsonFile($file, null);
        } catch (InvalidArgumentException $e) {
            return;
        }
        $this->fail('Expected exception not found.');
    }

    /**
     * @covers PHPUnit_Extensions_Webservice_TestCase::assertJsonFileNotEqualsJsonFile
     */
    public function testAssertJsonFileNotEqualsJsonFile()
    {
        $fileExpected = __DIR__ . '/../../../_files/JsonData/simpleObject.js';
        $fileActual   = __DIR__ . '/../../../_files/JsonData/arrayObject.js';
        $message = '';
        PHPUnit_Extensions_Webservice_TestCase::assertJsonFileNotEqualsJsonFile($fileExpected, $fileActual, $message);
    }

    /**
     * @covers PHPUnit_Extensions_Webservice_TestCase::assertJsonFileEqualsJsonFile
     */
    public function testAssertJsonFileEqualsJsonFile()
    {
        $file = __DIR__ . '/../../../_files/JsonData/simpleObject.js';
        $message = '';
        PHPUnit_Extensions_Webservice_TestCase::assertJsonFileEqualsJsonFile($file, $file, $message);
    }


/*************************************************************************/
/* Dataprovider                                                          */
/*************************************************************************/

    public static function validInvalidJsonDataprovider()
    {
        return array(
            'error syntax in expected JSON'  => array('{"Mascott"::}', '{"Mascott" : "Tux"}'),
            'error UTF-8 in actual JSON'     => array('{"Mascott" : "Tux"}', '{"Mascott" : :}'),
        );
    }
}