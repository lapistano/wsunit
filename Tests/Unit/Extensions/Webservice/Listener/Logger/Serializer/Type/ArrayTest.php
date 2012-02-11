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
class Extensions_Webservice_Logger_Serializer_Type_ArrayTest extends Extensions_Webservice_TestCase
{
    /**
     * Normalizes a given xml string to be compareable.
     *
     * @param string $xml
     * @return string
     */
    protected function sanitizeXml($xml) {
        $dom = new DOMDocument();
        $dom->loadXml($xml);
        //$dom->normalizeDocument();
        return $dom->saveXml();
    }

    /**
     * @covers Extensions_Webservice_Logger_Serializer_Type_Array::serialize
     */
    public function testSerialize() {
        $xml = '
            <array>
                <item name="Mascott">Tux</item>
                <item>42</item>
                <item />
                <array name="Beastie">
                    <item>21</item>
                    <item>23</item>
                    <item>42</item>
                </array>
                <array>
                    <item>80</item>
                    <item />
                </array>
                <array />
            </array>';

        $data = array(
            'Mascott' => 'Tux',
            0 => '42',
            1 => '',
            'Beastie' => array('21', '23', '42'),
            2 => array('80', ''),
            3 => array(),
        );

        $serializer = new Extensions_Webservice_Logger_Serializer_Type_Array();

        $this->assertXmlStringEqualsXmlString(
            $this->sanitizeXml($xml),
            $this->sanitizeXML($serializer->serialize($data))
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @covers Extensions_Webservice_Logger_Serializer_Type_Array::serialize
     */
    public function testSerializeExpectingInvalidArgumentException() {
        $serializer = new Extensions_Webservice_Logger_Serializer_Type_Array();
        $serializer->serialize('Invalid data set');
    }

    /**
     * @covers Extensions_Webservice_Logger_Serializer_Type_Array::serialize
     */
    public function testSerializeArrayTooDeepError() {
        $xml = '
            <array>
                <item name="Mascott">Tux</item>
                <array>
                    <item>80</item>
                    <error>Maximum amount recursions exceeded</error>
                </array>
                <array />
            </array>';

        $data = array(
            'Mascott' => 'Tux',
            2 => array(
                '80',
                array(42),
            ),
            array()
        );

        $serializer = $this->ProxyBuilder('Extensions_Webservice_Logger_Serializer_Type_Array')
            ->setProperties(array('maxDepth'))
            ->getProxy();

        $serializer->maxDepth = 2;
        $this->assertXmlStringEqualsXmlString(
            $this->sanitizeXml($xml),
            $this->sanitizeXML($serializer->serialize($data))
        );
    }

}