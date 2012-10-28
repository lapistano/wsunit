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

namespace lapistano\wsunit;

use lapistano\ProxyObject\ProxyBuilder;

abstract class Wsunit_TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Provides an instance of the \lapistano\ProxyObject\ProxyBuilder.
     *
     * @param string $classname
     * @return \lapistano\ProxyObject\ProxyBuilder
     */
    protected function getProxyBuilder($classname)
    {
         return new ProxyBuilder($classname);
    }

    /**
     * Provides an instance of PHPUnit_Framework_TestSuite.
     *
     * @return \PHPUnit_Framework_TestSuite
     */
    protected function getTestSuiteStub()
    {
        return $this->getMockBuilder('\\PHPUnit_Framework_TestSuite')
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
            'httpClient' => '\lapistano\wsunit\Http\HttpClient',
            'logger'     => '\lapistano\wsunit\Logger\LoggerFilesystem',
            'serializer' => '\lapistano\wsunit\Serializer\Http\SerializerHttpResponse',
            'mappingFile' => '../../../Tests/_files/configuration.xml',
        );
    }

    /**
     * Provides a stubbed instance of the WebserviceListenerFactory.
     *
     * @param array $methods
     * @return \lapistano\wsunit\WebserviceListenerFactory
     */
    protected function getFactoryStub(array $methods = array())
    {
        return $this->getMockBuilder('\lapistano\wsunit\WebserviceListenerFactory')
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Provides a faked instance of the LoaderInterface.
     *
     * @param array $methods
     * @return \lapistano\wsunit\Loader\LoaderInterface
     */
    protected function getLoaderFake(array $methods = array())
    {
        return $this->getFakeForAbstractClass(
            '\lapistano\wsunit\Loader\LoaderInterface',
            $methods
        );
    }

    /**
     * Provides a fake of the LoggerInterface
     *
     * @return \lapistano\wsunit\LoggerInterface
     */
    public function getLoggerFake(array $methods = array())
    {
        return $this->getFakeForAbstractClass(
            '\lapistano\wsunit\LoggerInterface',
            $methods
        );
    }

    /**
     * Provides a fake of the HttpClientInterface
     *
     * @return \lapistano\wsunit\Http\HttpClientInterface
     */
    public function getHttpClientFake(array $methods = array())
    {
        return $this->getFakeForAbstractClass(
            '\lapistano\wsunit\Http\HttpClientInterface',
            $methods
        );
    }

    /**
     * Provides a faked instance of the LoaderInterface.
     *
     * @param array $methods
     * @return \lapistano\wsunit\Loader\LoaderInterface
     */
    protected function getFakeForAbstractClass($className, array $methods = array())
    {
        return $this->getMockBuilder($className)
            ->setMethods(array($methods))
            ->getMockForAbstractClass();
    }
}
