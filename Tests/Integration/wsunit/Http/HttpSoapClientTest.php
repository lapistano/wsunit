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

namespace lapistano\wsunit\Http;

use lapistano\wsunit\Wsunit_TestCase;

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

class HttpSoapClientIntegrationTest extends Wsunit_TestCase
{
    /**
     * @dataProvider getSoapClientDataprovider
     * @covers \lapistano\wsunit\Http\HttpSoapClient::getSoapClient
     */
    public function testGetSoapClient($wsdl, $options)
    {
        $client = $this->getProxyBuilder('\\lapistano\\wsunit\\Http\\HttpSoapClient')
            ->setMethods(array('getSoapClient'))
        ->getProxy();

        $this->assertInstanceOf('\\SOAPClient', $client->getSoapClient($wsdl, $options));
    }
    public static function getSoapClientDataprovider()
    {
        return array(
            'WSDL mode' => array(
                'http://webserviceserver.appspot.com/EntityAPIService.wsdl',
                array()
            ),
            'non wsdl mode' => array(
                null,
                array(
                    'location' => 'http://webserviceserver.appspot.com',
                    'uri' => 'http://example.org'
                )
            ),
        );
    }

    /**
     * @expectedException \SoapFault
     * @dataProvider getSoapClientExceptionDataprovider
     * @covers \lapistano\wsunit\Http\HttpSoapClient::getSoapClient
     */
    public function testGetSoapClientExpectingSoapFaultException($wsdl, $options)
    {
        $client = $this->getProxyBuilder('\\lapistano\\wsunit\\Http\\HttpSoapClient')
            ->setMethods(array('getSoapClient'))
        ->getProxy();

        $client->getSoapClient($wsdl, $options);
    }
    public function getSoapClientExceptionDataprovider()
    {
        return array(
            'invalid uri in wsdl mode' => array(
                'http://www.example.net/foo?WSDL',
                array()
            ),
            'missing mandatory "location" in non wsdl model' => array(
                null,
                array(
                    'uri' => 'http://example.org'
                )
            ),
            'missing mandatory "uri" in non wsdl model' => array(
                null,
                array(
                    'location' => 'http://example.org'
                )
            ),
        );
    }
}
