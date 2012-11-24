<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2011-2013, Bastian Feder <php@bastian-feder.de>.
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
 * @copyright  2011-2013 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       https://github.com/lapistano/wsunit
 * @since      File available since Release 3.6.0
 */

namespace lapistano\wsunit\Serializer\Type;

use lapistano\wsunit\Wsunit_TestCase;

/**
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2011-2013 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       https://github.com/lapistano/wsunit
 * @since      File available since Release 3.6.0
 */
class SerializerTypeJsonTest extends Wsunit_TestCase
{

    /**
     * @covers \lapistano\wsunit\Serializer\Type\SerializerTypeJson::serialize
     * @covers \lapistano\wsunit\Serializer\Type\SerializerTypeJson::isValid
     */
    public function dtestSerialize()
    {

    }

    /**
     * @covers \lapistano\wsunit\Serializer\Type\SerializerTypeJson::isValid
     */
    public function testSerializeExpectingInvalidArgumentException()
    {
        $data = '{not a valid JSON string}';
        $serializer = new SerializerTypeJson();

        try {
            $serializer->serialize($data);

        } catch (\InvalidArgumentException $e) {

            $this->assertEquals(
                \lapistano\wsunit\Serializer\SerializerException::FAILED_DECODING_ATTEMPT,
                $e->getCode()
            );

            return;
        }

        $this->fail('Expected exception (\InvalidArgumentException) not thrown!');
    }

    /**
     * @covers \lapistano\wsunit\Serializer\Type\SerializerTypeJson::getName
     */
    public function testGetName()
    {
        $serializer = new SerializerTypeJson();
        $this->assertEquals('Json', $serializer->getName());
    }
}
