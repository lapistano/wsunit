<?php
/**
 * PHPUnit - Test listener extension
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
 * @package    PHPUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2011-2013 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://github.com/lapistano/wsunit
 * @since      File available since Release 3.6.0
 */

namespace lapistano\wsunit\Serializer\Type;

use lapistano\wsunit\Serializer\SerializerException;

/**
 * Type defintion of a serializer
 *
 * @package    WsUnit
 * @subpackage Extensions_WebServiceListener
 * @author     Bastian Feder <php@bastian-feder.de>
 * @copyright  2011-2013 Bastian Feder <php@bastian-feder.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://github.com/lapistano/wsunit
 * @since      Class available since Release 3.6.0
 */
class SerializerTypeJson extends SerializerTypeAbstract
{
    /**
     * Name of the current serialization type
     * @var string
     */
    protected $name = 'Json';


    /**
     * Does the actual serialization.
     *
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        $this->isValid($data);
        return $data;
    }

    /**
     * Checks it the given string is a valid JSON string.
     *
     * @param string $data
     * @throws \InvalidArgumentException in case an invalid data string was given.
     */
    protected function isValid($data)
    {
        $message = 'Failed to decode JSON string';
        json_decode($data);

        switch (json_last_error()) {
        case JSON_ERROR_NONE:
            return true;
        case JSON_ERROR_DEPTH:
            $message =' - Maximum stack depth exceeded';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            $message = ' - Underflow or the modes mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            $message = ' - Unexpected control character found';
            break;
        case JSON_ERROR_SYNTAX:
            $message = ' - Syntax error, malformed JSON';
            break;
        case JSON_ERROR_UTF8:
            $message = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
        default:
            $message = ' - Unknown error';
            break;
        }

        throw new \InvalidArgumentException($message, SerializerException::FAILED_DECODING_ATTEMPT);
    }
}
