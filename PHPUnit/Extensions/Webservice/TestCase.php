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
 * @author     Bastian Feder <lapis@php.net>
 * @copyright  2002-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 1.0.0
 */

/**
 * A TestCase extension that provides functionality for testing and asserting
 * against a real database.
 *
 * @package    WsUnit
 * @author     Bastian Feder <lapis@php.net>
 * @copyright  2011 Bastian Feder <lapis@php.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.0.0
 */
abstract class PHPUnit_Extensions_Webservice_TestCase extends PHPUnit_Framework_TestCase
{

    /**
     * Asserts that two given JSON encoded objects or arrays are equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringEqualsJsonString($expectedJson, $actualJson, $message = '')
    {
    $expected = json_decode($expectedJson);
        if ($jsonError = json_last_error()) {
            $message .= self::determineJsonError($jsonError, 'expected');
        }

        $actual   = json_decode($actualJson);
        if ($jsonError = json_last_error()) {
            $message .= self::determineJsonError($jsonError, 'actual');
        }
        parent::assertEquals($expected, $actual, $message);
    }

    /**
     * Asserts that two given JSON encoded objects or arrays are not equal.
     *
     * @param string $expectedJson
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringNotEqualsJsonString($expectedJson, $actualJson, $message = '')
    {
        $expected = json_decode($expectedJson);
        if ($jsonError = json_last_error()) {
            $message .= self::determineJsonError($jsonError, 'expected');
        }

        $actual   = json_decode($actualJson);
        if ($jsonError = json_last_error()) {
            $message .= self::determineJsonError($jsonError, 'actual');
        }

        parent::assertNotEquals($expected, $actual, $message);
    }

    /**
     * Asserts that the generated JSON encoded object and the content of the given file are equal.
     *
     * @param string $expectedFile
     * @param string $actualJson
     * @param string $message
     */
    public static function assertJsonStringEqualsJsonFile($expectedFile, $actualJson, $message = '')
    {
        /*
        self::assertFileExists($expectedFile, $message);

        if (!is_string($string)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(2, 'string');
        }

        // call constraint
        $constraint = new PHPUnit_Framework_Constraint_StringMatches(
          file_get_contents($expectedFile)
        );

        self::assertThat($string, $constraint, $message);
        */
    }


    /*************************************************************************/
    /* helpers                                                               */
    /*************************************************************************/

    private static function determineJsonError($error, $type = '')
    {
        switch (strtolower($type)) {
        case 'expected':
            $prefix = 'Expected value JSON decode error - ';
            break;
        case 'actual':
            $prefix = 'Actual value JSON decode error - ';
            break;
        default:
            $prefix = '';
            break;
        }

        switch (strtoupper($error)) {
        case JSON_ERROR_NONE:
            return;
        case JSON_ERROR_DEPTH:
            return $prefix . 'Maximum stack depth exceeded';
        case JSON_ERROR_STATE_MISMATCH:
            return $prefix . 'Underflow or the modes mismatch';
        case JSON_ERROR_CTRL_CHAR:
            return $prefix . 'Unexpected control character found';
        case JSON_ERROR_SYNTAX:
            return $prefix . 'Syntax error, malformed JSON';
        case JSON_ERROR_UTF8:
            return $prefix . 'Malformed UTF-8 characters, possibly incorrectly encoded';
        default:
            return $prefix . 'Unknown error';
        }
    }
}