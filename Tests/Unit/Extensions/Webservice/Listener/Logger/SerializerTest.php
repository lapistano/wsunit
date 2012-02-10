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

/**
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2011 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.6.0
 */
class Extensions_Webservice_Logger_SerializerTest extends Extensions_Webservice_TestCase
{
    /**
     * Provides a dummy of the Extensions_Webservice_Logger_Serializer_Type class.
     *
     * @param array $methods
     * @return Extensions_Webservice_Logger_Serializer_Type
     */
    protected function getSerializerTypeMock(array $methods = array())
    {
        return $this->getMockBuilder('Extensions_Webservice_Logger_Serializer_Type')
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Provides an instance of the abstract Extensions_Webservice_Logger_Serializer class.
     *
     * @return Extensions_Webservice_Logger_Serializer
     */
    protected function getSerializerFixture()
    {
        return $this->getMockBuilder('Extensions_Webservice_Logger_Serializer')
            ->setMethods(array('serialize'))
            ->getMock();
    }

    /**
     * @covers Extensions_Webservice_Logger_Serializer_Http_Response::register
     */
    public function testRegister()
    {
        $type = $this->getSerializerTypeMock(array('getName'));
        $type
            ->expects($this->exactly(4))
            ->method('getName')
            ->will($this->returnValue('testSerializerType'));

        $expected = array(
            'testSerializerType' => array(array())
        );

        $serializer = $this->getSerializerFixture();
        $serializer->register($type, array());
        $this->assertAttributeEquals($expected, 'dataContainer', $serializer);
    }

    /**
     * @covers Extensions_Webservice_Logger_Serializer_Http_Response::addType
     * @covers Extensions_Webservice_Logger_Serializer_Http_Response::__construct
     */
    public function testAddType()
    {
        $type = $this->getSerializerTypeMock(array('getName'));
        $type
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('testSerializerType'));

        $expected = array(
            'testSerializerType' => $type
        );

        $serializer = $this->getSerializerFixture();
        $serializer->addType($type, array());

        $this->assertAttributeEquals($expected, 'types', $serializer);
    }

    /**
     * @expectedException Extensions_Webservice_Logger_Serializer_Exception
     * @covers Extensions_Webservice_Logger_Serializer_Http_Response::addType
     */
    public function testAddTypeExpectingExtensionsWebserviceLoggerSerializerException()
    {
        $type = $this->getSerializerTypeMock(array('getName'));
        $type
            ->expects($this->exactly(3))
            ->method('getName')
            ->will($this->returnValue('testSerializerType'));

        $serializer = $this->getSerializerFixture();
        $serializer->addType($type, array());
        $serializer->addType($type, array());
    }


    /*************************************************************************/
    /* Dataprovider                                                          */
    /*************************************************************************/


}