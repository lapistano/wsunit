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
 * Basic http client to request information from an url via GET method.
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

class HttpSoapClient extends HttpClientAbstract
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
     * Sends a request to the given url.
     *
     * @param string $url   Location to be called
     * @param array  $query
     *
     * @return string The http response with the response header included.
     */
    public function get($url, array $query = array())
    {

        $soapClient = $this->getSoapClient($url, $query);

    }


    /**
     * Provides an instance of the SOAPClient build into PHP.
     *
     * @param string $wsdl    URI of the WSDL file or NULL if working in non-WSDL mode.
     * @param array  $options Optional if in WSDL mode, else keys 'location' & 'uri' are mandatory.
     *
     * @return \SoapClient
     *
     * @link http://ch2.php.net/manual/en/soapclient.soapclient.php
     */
    protected function getSoapClient($wsdl, array $options = array(), $force = false)
    {
        if ($force || $wsdl !== $this->soapClientWsdl || $options !== $this->soapClientOptions) {
            $force = true;
        }

        if ($force || empty($this->soapClient)) {
            $this->soapClient = new \SoapClient($wsdl, $options);
            $this->soapClientWsdl = $wsdl;
            $this->soapClientOptions = $options;
        }

        return $this->soapClient;
    }
}
