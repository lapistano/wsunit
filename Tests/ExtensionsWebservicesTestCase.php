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
use lapistano\ProxyObject\ProxyBuilder;

class Extensions_Webservice_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Provides an instance of the
     * Enter description here ...
     * @param unknown_type $classname
     */
    protected function ProxyBuilder($classname) {
         return new ProxyBuilder($classname);
    }

    /**
     * Provides an instance of PHPUnit_Framework_TestSuite.
     *
     * @return PHPUnit_Framework_TestSuite
     */
    protected function getTestSuiteStub()
    {
        return $this->getMockBuilder('PHPUnit_Framework_TestSuite')
            ->getMock();
    }

    /**
     * Provides a configuration array accourding to the mandatory information provided by the phpunit xml configuration.
     *
     * @return array
     */
    protected function getConfiguration()
    {
        return array(
            'httpClient' => 'Extensions_Webservice_Listener_Http_Client',
            'logger'     => 'Extensions_Webservice_Listener_Logger',
            'serialiser' => 'Extensions_Webservice_Logger_Serializer_Http_Response',
            'mappingFile' => '../../../../../Tests/_files/configuration.xml',
        );
    }

    /**
     * Provides a stubbed instance of the Extensions_Webservice_Listener_Factory.
     *
     * @param array $methods
     * @return Extensions_Webservice_Listener_Factory
     */
    protected function getFactoryStub(array $methods = array())
    {
        return $this->getMockBuilder('Extensions_Webservice_Listener_Factory')
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Provides a faked instance of the Extensions_Webservice_Listener_Loader_Interface.
     *
     * @param array $methods
     * @return Extensions_Webservice_Listener_Loader_Interface
     */
    protected function getLoaderFake(array $methods = array())
    {
        return $this->getFakeForAbstractClass('Extensions_Webservice_Listener_Loader_Interface', $methods);
    }

    /**
     * Provides a fake of the Extensions_Webservice_Listener_Logger_Interface
     *
     * @return Extensions_Webservice_Listener_Logger_Interface
     */
    public function getLoggerFake(array $methods = array())
    {
        return $this->getFakeForAbstractClass('Extensions_Webservice_Listener_Logger_Interface', $methods);
    }

    /**
     * Provides a fake of the Extensions_Webservice_Listener_Http_Client_Interface
     *
     * @return Extensions_Webservice_Listener_Http_Client_Interface
     */
    public function getHttpClientFake(array $methods = array())
    {
        return $this->getFakeForAbstractClass('Extensions_Webservice_Listener_Http_Client_Interface', $methods);
    }

    /**
     * Provides a faked instance of the Extensions_Webservice_Listener_Loader_Interface.
     *
     * @param array $methods
     * @return Extensions_Webservice_Listener_Loader_Interface
     */
    protected function getFakeForAbstractClass($className, array $methods = array())
    {
        return $this->getMockBuilder($className)
            ->setMethods(array($methods))
            ->getMockForAbstractClass();
    }
}
