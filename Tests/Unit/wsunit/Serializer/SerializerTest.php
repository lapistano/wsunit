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

namespace lapistano\wsunit\Serializer;

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
class SerializerTest extends Wsunit_TestCase
{
    /**
     * Provides a dummy of the SerializerTypeAbstract class.
     *
     * @param array $methods
     * @return \lapistano\wsunit\Serializer\Type\SerializerTypeAbstract
     */
    protected function getSerializerTypeMock(array $methods = array())
    {
        $methods = array_merge(array('serialize'), $methods);
        $type = $this->getMockBuilder('\lapistano\wsunit\Serializer\Type\SerializerTypeAbstract')
            ->setMethods($methods)
            ->getMock();
        return $type;
    }

    /**
     * Provides an instance of the abstract SerializerAbstract class.
     *
     * @return \lapistano\wsunit\Serializer\SerializerAbstract
     */
    protected function getSerializerFixture()
    {
        return $this->getMockBuilder('\lapistano\wsunit\Serializer\SerializerAbstract')
            ->getMockForAbstractClass();
    }

    /**
     * @expectedException \UnexpectedValueException
     * @covers \lapistano\wsunit\Serializer\SerializerAbstract::register
     */
    public function testRegisterExpectingUnexpectedValueException()
    {
        $serializer = $this->getSerializerFixture();
        $serializer->register('testSerializerType', array());
    }

    /**
     * @covers \lapistano\wsunit\Serializer\SerializerAbstract::register
     */
    public function testRegister()
    {
        $type = $this->getSerializerTypeMock(array('getName'));
        $type
            ->expects($this->exactly(2))
            ->method('getName')
            ->will($this->returnValue('testSerializerType'));

        $expected = array(
            'testSerializerType' => array(array())
        );

        $serializer = $this->getSerializerFixture();
        $serializer->addType($type);
        $serializer->register('testSerializerType', array());
        $this->assertAttributeEquals($expected, 'dataContainer', $serializer);
    }

    /**
     * @covers \lapistano\wsunit\Serializer\SerializerAbstract::addType
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
     * @expectedException \lapistano\wsunit\Serializer\SerializerException
     * @covers \lapistano\wsunit\Serializer\SerializerAbstract::addType
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

    /**
     * @covers \lapistano\wsunit\Serializer\SerializerAbstract::serialize
     */
    public function testSerialize()
    {
        $xmlFromArray = '<array><item>Tux</item><item>Beastie</item></array>';
        $expected = "<document>\n<array><item>Tux</item><item>Beastie</item></array>\n".
                    "<string>testSerializerType</string>\n</document>";

        $typeArray = $this->getSerializerTypeMock(array('getName'));
        $typeArray
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('testSerializerTypeArray'));
        $typeArray
            ->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue($xmlFromArray));

        $type = $this->getSerializerTypeMock(array('getName'));
        $type
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('testSerializerType'));
        $type
            ->expects($this->once())
            ->method('serialize')
            ->will($this->returnValue('<string>testSerializerType</string>'));

        $serializer = $this->getSerializerFixture();
        $serializer->addType($typeArray);
        $serializer->register('testSerializerTypeArray', array('Tux', 'Beastie'));
        $serializer->addType($type);
        $serializer->register('testSerializerType', array());
        $this->assertEquals($expected, $serializer->serialize());
    }

    /**
     * @covers \lapistano\wsunit\Serializer\SerializerAbstract::isValidTagName
     */
    public function testIsValidTagName()
    {
        $serializer = $this->getSerializerFixture();
        $serializer->setDocumentRoot('Tux');
        $this->assertAttributeEquals('Tux', 'documentRoot', $serializer);
    }

    /**
     * @expectedException \lapistano\wsunit\Serializer\SerializerException
     * @dataProvider isValidTagNameExpectingExtensionsWebserviceLoggerSerializerExceptionDataprovider
     * @covers \lapistano\wsunit\Serializer\SerializerAbstract::isValidTagName
     */
    public function testIsValidTagNameExpectingExtensionsWebserviceLoggerSerializer($tagName)
    {
        $serializer = $this->getSerializerFixture();
        $serializer->setDocumentRoot($tagName);
        $this->assertAttributeEquals($tagName, 'documentRoot', $serializer);
    }
    public static function isValidTagNameExpectingExtensionsWebserviceLoggerSerializerExceptionDataprovider()
    {
        return array(
            'with numbers' => array('Beastie23'),
            'with special chars' => array('$&%^<>'),
        );
    }

    /**
     * @covers \lapistano\wsunit\Serializer\SerializerAbstract::setDocumentRoot
     */
    public function testSetDocumentRoot()
    {
        $serializer = $this->getSerializerFixture();
        $serializer->setDocumentRoot('Tux');
        $this->assertAttributeEquals('Tux', 'documentRoot', $serializer);
    }

    /**
     * @expectedException \lapistano\wsunit\Serializer\SerializerException
     * @covers \lapistano\wsunit\Serializer\SerializerAbstract::setDocumentRoot
     */
    public function testSetDocumentRootExpectingExtensionsWebserviceLoggerSerializerException()
    {
        $serializer = $this->getSerializerFixture();
        $serializer->setDocumentRoot('232');
    }
}
