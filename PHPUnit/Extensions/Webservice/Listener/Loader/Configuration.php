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

/**
 * Basic http client to request information from an url via GET method.
 *
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2011 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 3.6.0
 */

class Extensions_Webservice_Listener_Loader_Configuration implements Extensions_Webservice_Listener_Loader_Interface
{
    /**
     * Loads the configuration from the given file.
     *
     * @param string $configFile
     *
     * @see Extensions_Webservice_Listener_Loader_Interface::load()
     */
    public function load($configFile)
    {
        $configFile = __DIR__ . '/' . $configFile;

        if (!is_readable($configFile)) {
            throw new InvalidArgumentException('File not found ( '. $configFile.' ).');
        }
        return $this->transcode($this->getDomFromFile($configFile));
    }

    /**
     * Converts the content of the given file to a DOM object
     *
     * @param string $file
     * @return DOMDocument
     */
    protected function getDomFromFile($file)
    {
        $dom = new DomDocument();
        $dom->load($file);
        return $dom;
    }

    /**
     * Transcodes the given DOMDocument to an array
     *
     * @param DOMDocument $data
     * @return array
     */
    protected function transcode(DOMDocument $data)
    {
        $transcoded = array();
        $xpath = new DOMXpath($data);
        $elements = $xpath->query("//listener/test");
        $transcoded['serializer'] = $this->extractSerializerClassname($xpath->query("//listener/serializer")->item(0));

        if (!is_null($elements)) {
            foreach ($elements as $test) {
                $testClassName = $test->getAttribute('case');
                $testName      = $test->getAttribute('name');
                $case          = array();

                // determine if there is a testcase specific serializer defined
                $serializer = $this->extractSerializerClassname($xpath->query("serializer", $test)->item(0));
                if (!empty($serializer)) {
                    $case['serializer'] = $serializer;
                }

                // extract location urls to be requested for each run of the test
                $case['locations'] = $this->extractLocations($test, $xpath);

                // assemble into general array
                if (!isset($transcoded[$testClassName])) {
                    $transcoded[$testClassName] = array();
                }
                $transcoded[$testClassName][$testName] = $case;
            }
        }
        return $transcoded;
    }

    /**
     * Extracts the name of the serializer from the given node.
     *
     * @param DOMNode $node
     * @return string
     */
    protected function extractSerializerClassname($node)
    {
        if (!is_null($node)) {
            return $node->nodeValue;
        }
        return '';
    }

    /**
     * Extracts urls to be visited on each test run
     *
     * @param DOMXPath   $xpath
     * @param DOMElement $node
     *
     * @return array
     */
    protected function extractLocations($node, $xpath)
    {
        $case      = array();
        $locations = $xpath->query('location', $node);
        foreach ($locations as $location) {
            $dataName = $location->getAttribute('dataName');
            $testData = array();
            $testData['url'] = $location->getAttribute('href');
            $testData['params'] = array();

            $params = $xpath->query('query/param', $location);
            foreach ($params as $param) {
                $paramName = $param->getAttribute('name');

                $pos = strpos($paramName, '[');
                if ($pos > 0) {
                    preg_match("(^([^\[]+)\[([^\[]*)\]$)", $paramName, $matches);

                    if (!empty($matches[2])) {
                        $testData['params'][$matches[1]][$matches[2]] = $param->nodeValue;
                    } else {
                        $testData['params'][$matches[1]][] = $param->nodeValue;
                    }
                } else {
                    $testData['params'][$paramName] = $param->nodeValue;
                }
            }

            if (!empty($dataName)) {
                $case[$dataName] = $testData;
            } else {
                $case[] = $testData;
            }
        }
        return $case;
    }
}
