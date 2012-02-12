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
 *
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
class Extensions_Webservice_Listener_Logger implements Extensions_Webservice_Listener_Logger_Interface
{
    /**
     * Contains the name of the file to be logged to.
     * @var string
     */
    protected $filename = '';

    /**
     * Instance of an implementation of  the Extensions_Webservice_Serializer_Interface.
     * @var Extensions_Webservice_Serializer_Interface
     */
    protected $serializer;

    /**
     *
     * @param Extensions_Webservice_Serializer_Interface $serializer
     */
    public function __construct(Extensions_Webservice_Serializer_Interface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Persists the given message.
     *
     * @param string $message
     * @param string $level
     */
    public function log($message, $level = '')
    {
        $this->serializer->register('Array', $message->getHeader());
        $this->serializer->setDocumentRoot('response');

        // due to time issues just a bad hack .. to be refactored asap
        $path = TEST_DIR . '/_files/responses';
        $file = $path . '/' . $this->filename;
        file_put_contents($file, $this->serializer->serialize());
    }

    /**
     * Registers the given string as name for the log file.
     *
     * To prevent an invalid filename will be generated using the passed string.
     * It will be sanitized first.
     *
     * @param string $name
     */
    public function setFilename($name, $ext = 'txt')
    {
        // sanitize $name
        $name = $this->sanitizeString($name);
        $this->filename = $name . '.' . $ext;
    }

    /**
     * Removes a number of not allowed chars from the passed string to be a valid filename.
     *
     * @param string $string
     * @return string
     */
    protected function sanitizeString($string)
    {
        $sanitizedItems = array();
        $array = explode(' ', $string);

        foreach ($array as $item) {
            if ('"' == $item) continue;
            if (0 <= strpos('"', $item)) {
                $item = str_replace('"', '', $item);
            }
            $sanitizedItems[] = ucfirst(trim($item));
        }

        return join('', $sanitizedItems);
    }

}
