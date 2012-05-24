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

namespace lapistano\wsunit\Serializer\Type;

use lapistano\wsunit\Extensions_Webservice_TestCase;

/**
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2011 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.6.0
 */
class Extensions_Webservice_Serializer_Type_XmlTest extends Extensions_Webservice_TestCase
{

    /**
     * @covers \lapistano\wsunit\Serializer\Type\Extensions_Webservice_Serializer_Type_Xml::serialize
     * @covers \lapistano\wsunit\Serializer\Type\Extensions_Webservice_Serializer_Type_Xml::isValid
     */
    public function testSerialize()
    {
        $xmlDef = '<?xml version="1.0" encoding="utf-8"?>';
        $data = '<rss version="2.0">
              <channel>
                <title>Blog - bastian-feder.de</title>
                <link>http://blog.bastian-feder.de/blog.html</link>
                <description>Blog - bastian-feder.de</description>
                <language>en</language>
                <copyright>CC by-nc-sa</copyright>
                <managingEditor>lapis</managingEditor>
                <pubDate>Sat, 05 May 2012 18:47:42 +0100</pubDate>
                <lastBuildDate>Sat, 05 May 2012 18:47:04 +0100</lastBuildDate>
                <generator>eZ Components Feed dev (http://ezcomponents.org/docs/tutorials/Feed)</generator>
                <docs>http://www.rssboard.org/rss-specification</docs>
                <item>
                  <title>Introduction to wsunit</title>
                  <link>http://blog.bastian-feder.de/blog/031_wsunit.html</link>
                  <description>Testing interactions with data providers via Http</description>
                  <author>lapis</author>
                  <pubDate>Fri, 04 May 2012 08:31:16 +0100</pubDate>
                </item>
              </channel>
            </rss>';

        $serializer = new Extensions_Webservice_Serializer_Type_Xml();
        $this->assertEquals($data, $serializer->serialize($xmlDef . $data));
    }

    /**
     * @covers \lapistano\wsunit\Serializer\Type\Extensions_Webservice_Serializer_Type_Xml::isValid
     */
    public function testSerializeExpectingInvalidArgumentException()
    {
        $data = '<?xml version="1.0" encoding="utf-8"?>
            <rss version="2.0">
              <channel>
            </rss>';

        $serializer = new Extensions_Webservice_Serializer_Type_Xml();
        try {
            $serializer->serialize($data);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(
                \lapistano\wsunit\Serializer\Extensions_Webservice_Serializer_Exception::InvalidType,
                $e->getCode()
            );
            return;
        }
        $this->fail('Expected exception (\InvalidArgumentException) not thrown!');
    }

    /**
     * @covers \lapistano\wsunit\Serializer\Type\Extensions_Webservice_Serializer_Type_Xml::getName
     */
    public function testGetName()
    {
        $serializer = new Extensions_Webservice_Serializer_Type_Xml();
        $this->assertEquals('Xml', $serializer->getName());
    }
}