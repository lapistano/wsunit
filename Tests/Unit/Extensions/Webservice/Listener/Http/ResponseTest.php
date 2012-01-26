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

use lapistano\ProxyObject\ProxyBuilder;

/**
 *
 *
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2011 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.6.0
 */

class Extensions_Webservice_Listener_Http_ResponseTest extends Extensions_Webservice_TestCase
{
    /**
     * @covers Extensions_Webservice_Listener_Http_Response::setHeader
     */
    public function testSetHeader()
    {
        $pb = new ProxyBuilder('Extensions_Webservice_Listener_Http_Response');
        $response = $pb->setProperties(array('header'))->getProxy();
        $response->setHeader(array());
        $this->assertInternalType('array', $response->header);
    }

    /**
     * @covers Extensions_Webservice_Listener_Http_Response::setBody
     */
    public function testSetBody()
    {
        $pb = new ProxyBuilder('Extensions_Webservice_Listener_Http_Response');
        $response = $pb->setProperties(array('body'))->getProxy();
        $response->setBody('');
        $this->assertInternalType('string', $response->body);
    }

    /**
     * @covers Extensions_Webservice_Listener_Http_Response::__toString
     */
    public function testMagicToSting()
    {
        $expected = "Gummibärchen in freier Wildbahn sind ausgestorben.";

        $response = new Extensions_Webservice_Listener_Http_Response();
        $response->setBody("Gummibärchen in freier Wildbahn sind ausgestorben.");
        $this->assertEquals($expected, strval($response));
    }
}