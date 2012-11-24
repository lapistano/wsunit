<?php
/**
 * PHPUnit - Test listener extension
 *
 * Copyright (c) 2012, Bastian Feder <php@bastian-feder.de>.
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

namespace lapistano\wsunit\Logger;

use lapistano\wsunit\Serializer\SerializerInterface;

/**
 * Logger to persist data onto the filesystem.
 *
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2012 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://github.com/lapistano/wsunit
 * @since      Class available since Release 3.6.0
 */
class LoggerFilesystem implements LoggerInterface
{
    /**
     * Contains an instance of an PHPUnit_Framework_Test.
     * @var \PHPUnit_Framework_Test
     */
    protected $test = null;

    /**
     * Instance of an implementation of the SerializerInterface.
     * @var lapistano\wsunit\Serializer\SerializerInterface
     */
    protected $serializer;

    /**
     * Contains mapping between the data type (e.g. xml) and the region.
     * @var array
     */
    protected $configuration = array(
        'header' => 'Array',
        'body' => 'Xml',
    );


    /**
     * @param \lapistano\wsunit\Serializer\SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer, array $configuration = array())
    {
        $this->serializer = $serializer;

        if (!empty($configuration)) {
            $this->configuration = $configuration;
        }
    }

    /**
     * Persists the given message.
     *
     * @param string $message
     * @param string $level
     */
    public function log($message, $level = '')
    {
        $this->serializer->register($this->configuration['header'], $message->getHeader());
        $this->serializer->register($this->configuration['body'], $message->getBody());
        $this->serializer->setDocumentRoot('response');

        // due to time issues just a bad hack .. to be refactored asap
        $path = TEST_DIR . '/_files/responses';
        $file = $path . '/' . $this->generateFilename($this->test->getName());
        $filename = $file . '.xml';

        if (file_exists($filename)) {
            rename($filename, $file . microtime(true) . '.xml');
        }

        try {
            file_put_contents($filename, $this->serializer->serialize());

        } catch (\InvalidArgumentException $e) {

            $error = sprintf(
                "%s\n\nResponse header:\n%s\n\nResponse body:\n%s\n\n",
                $e->getMessage(),
                print_r($message->getHeader(), true),
                print_r($message->getBody(), true)
            );
            file_put_contents($filename, $error);
        }
    }

    /**
     * Registers a Test.
     *
     * @parameter \PHPUnit_Framework_Test $test
     * @see lapistano\wsunit\Logger\LoggerInterface::registerTest()
     */
    public function registerTest(\PHPUnit_Framework_Test $test)
    {
        $this->test = $test;
    }

    /**
     * Removes a number of not allowed chars from the passed string to be a valid filename.
     *
     * @param string $string
     * @return string
     */
    protected function sanitizeString($string)
    {
        $chars = array('"');
        return str_replace($chars, '', $string);
    }


    protected function generateFilename($string)
    {
        $sanitizedItems = array();
        $array = explode(' ', $this->sanitizeString($string));

        foreach ($array as $item) {
            $sanitizedItems[] = ucfirst(trim($item));
        }

        return join('', $sanitizedItems);
    }
}
