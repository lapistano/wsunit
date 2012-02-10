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
 * Serializer to stringify a Http response to a transferable, computer-readable format.
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

abstract class Extensions_Webservice_Logger_Serializer
{
    /**
     * Register to store data to be serialized
     * @var array
     */
    protected $dataContainer = array();

    /**
     * List of registered serializer types
     * @var array
     */
    protected $types = array();

    /**
     * Stringifies the registered data
     *
     * @return string
     */
    public function serialize()
    {
        $serialized = array();

        foreach ($this->types as $serializerTypeName => $serializer) {
            if (!empty($this->dataContainer[$serializerTypeName])) {
                foreach ($this->dataContainer[$serializerTypeName] as $data) {
                    $serialized[] = $serializer->serialize($data);
                }
            }
        }

        return implode("\n", $serialized);
    }

    /**
     * Adds the given data to a registry.
     *
     * @param Extensions_Webservice_Logger_Serializer_Type $type
     * @param mixed $data
     */
    public function register(Extensions_Webservice_Logger_Serializer_Type $type, $data)
    {
        if (!isset($this->types[$type->getName()])) {
            $this->addType($type);
        }
        $this->dataContainer[$type->getName()][] = $data;
    }

    /**
     * Registers the given type in a local registry
     *
     * @param Extensions_Webservice_Logger_Serializer_Type $type
     */
    public function addType(Extensions_Webservice_Logger_Serializer_Type $type)
    {
        if (!isset($this->types[$type->getName()])) {
            $this->types[$type->getName()] = $type;
        } else {
            throw new Extensions_Webservice_Logger_Serializer_Exception(
                'Given type is already registered!',
                Extensions_Webservice_Logger_Serializer_Exception::DoubleTypeRegistrationAttempt
            );
        }
    }

}

class Extensions_Webservice_Logger_Serializer_Exception extends \Exception
{
    const DoubleTypeRegistrationAttempt = 1;
}
