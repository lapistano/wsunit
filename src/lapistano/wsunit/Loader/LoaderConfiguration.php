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

namespace lapistano\wsunit\Loader;

/**
 * Basic http client to request information from an url via GET method.
 *
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2011 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://github.com/lapistano/wsunit
 * @since      File available since Release 3.6.0
 */

class LoaderConfiguration implements LoaderInterface
{
    /**
     * Loads the configuration from the given file.
     *
     * @param string $configFile
     *
     * @see \lapistano\wsunit\Loader\LoaderInterface::load()
     */
    public function load($configFile)
    {
        $configFile = __DIR__ . '/' . $configFile;

        if (!is_readable($configFile)) {
            throw new \InvalidArgumentException('File not found ( ' . $configFile . ' ).');
        }

        return $this->transcode($this->getDomFromFile($configFile));
    }

    /**
     * Converts the content of the given file to a DOM object
     *
     * @param string $file
     *
     * @return \DOMDocument
     */
    protected function getDomFromFile($file)
    {
        $dom = new \DomDocument();
        $dom->load($file);

        return $dom;
    }

    /**
     * Transcodes the given DOMDocument to an array
     *
     * @param \DOMDocument $data
     *
     * @return array
     */
    protected function transcode(\DOMDocument $data)
    {
        $transcoded = array();
        $xpath = new \DOMXpath($data);
        $elements = $xpath->query("//listener/test");
        $transcoded['serializer'] = $this->extractSerializerClassname($xpath->query("//listener/serializer")->item(0));

        if (!is_null($elements)) {
            foreach ($elements as $test) {
                $testClassName = $test->getAttribute('case');
                $testName = $test->getAttribute('name');
                $case = array();

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
     * @param \DOMNode $node
     *
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
     * @param \DOMElement $node
     * @param \DOMXPath   $xpath
     *
     * @return array
     */
    protected function extractLocations(\DOMElement $node, \DOMXPath $xpath)
    {
        $case = array();
        $locations = $xpath->query('location', $node);

        foreach ($locations as $location) {
            $dataName = $location->getAttribute('dataName');
            $testData = $this->extractMetaDataFromLocation($location, $xpath);

            if (!empty($dataName)) {
                $case[$dataName] = $testData;
            } else {
                $case[] = $testData;
            }
        }

        return $case;
    }

    /**
     * Extracts information from a location.
     *
     * @param \DOMElement    $location
     * @param \DOMXPath      $xpath
     *
     * @return array
     */
    protected function extractMetaDataFromLocation(\DOMElement $location, \DOMXPath $xpath)
    {
        $testData = array();
        $testData['url'] = $location->getAttribute('href');
        $testData['params'] = $this->extractParametersFromLocation($xpath->query('query/param', $location));

        return $testData;
    }

    /**
     * Extracts each location parameter to an array.
     *
     * @param \DOMNodeList $params
     *
     * @return array
     */
    protected function extractParametersFromLocation(\DOMNodeList $params)
    {
        $locationParameters = array();

        foreach ($params as $param) {

            $paramName = $param->getAttribute('name');
            $pos = strpos($paramName, '[');

            if ($pos > 0) {
                $matches = array();

                preg_match("(^([^\[]+)\[([^\[]*)\]$)", $paramName, $matches);

                if (!empty($matches[2])) {

                    $locationParameters[$matches[1]][$matches[2]] = $param->nodeValue;

                } else {

                    $locationParameters[$matches[1]][] = $param->nodeValue;
                }

            } else {

                $locationParameters[$paramName] = $param->nodeValue;
            }
        }

        return $locationParameters;
    }
}
