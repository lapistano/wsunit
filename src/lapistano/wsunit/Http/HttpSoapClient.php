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

namespace lapistano\wsunit\Http;


/**
 * Interceptor for to be able to record the Http Response (body and header) from a SOAP Call.
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
class HttpSoapClient extends \SoapClient implements HttpClientInterface
{
    /**
     * @var \SoapClient
     */
    protected $soapClient;

    /**
     * @var string
     */
    protected $soapClientWsdl;

    /**
     * @var array
     */
    protected $soapClientOptions = array();


    /**
     * @param string|null $wsdl
     * @param array $options
     *
     * @link http://www.php.net/manual/en/soap.configuration.php#110408
     * @throw \SoapFault
     */
    public function __construct($wsdl, array $options = array())
    {
        if (isset($options['disableWSDLCache']) && true === (bool)$options['disableWSDLCache']) {

            // disable soap.wsdl_cache_enabled to ensure changes on the WSDL does take effect immediately
            ini_set('soap.wsdl_cache_enabled', 0);
            ini_set('soap.wsdl_cache_ttl', 0);
        }

        parent::_construct($wsdl, $options = array());
    }

    /**
     * Alias for the SOAPClient::__soapCall() method
     *
     * @param string $wsdl
     * @param array  $options
     */
    public function get($wsdl, array $options = array())
    {
        $functionName = $options['functionName'];
        $arguments = $options['arguments'];
        $inputHeaders = $options['inputHeaders'];

        return $this->__soapCall(
            $functionName,
            $arguments,
            $options,
            $inputHeaders
        );
    }
}
