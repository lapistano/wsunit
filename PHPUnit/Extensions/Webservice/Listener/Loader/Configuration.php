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
        if (!file_exists($configFile)) {
            throw new InvalidArgumentException('Unable to open file ( '. $configFile.' )');
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

        if (!is_null($elements)) {
            foreach ($elements as $test) {
                $name = $test->getAttribute('name');

                if (!isset($transcoded[$name])) {
                    $transcoded[$name] = array();
                }

                $locations = $xpath->query('location/text()', $test);
                foreach ($locations as $location) {
                    $transcoded[$name][] = $location->nodeValue;
                }
            }
        }
        return $transcoded;
    }
}
