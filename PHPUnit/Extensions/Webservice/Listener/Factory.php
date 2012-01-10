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
 * Factory to load necessary dependencies
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

class Extensions_Webservice_Listener_Factory
{
    /**
     * Register of types and corresponding classes.
     * @var array
     */
    protected $register = array(
        'httpClient' => 1,
        'logger' => 1,
        'loader' => 1,
    );

    /**
     * Register of instantiated classes.
     * @var array
     */
    protected $instances = array();

    /**
     * Set of allowed interface implementations.
     * @var array
     */
    protected $interfaces = array(
        'httpClient' => 'Extensions_Webservice_Listener_HttpClient_Interface',
        'logger'     => 'Extensions_Webservice_Listener_Logger_Interface',
        'loader'     => 'Extensions_Webservice_Listener_Loader_Interface',
    );

    /**
     * Provides an instance of the class registered to the given type.
     *
     * @param  string $type Name of the class instance to be returned
     * @throws ReflectionException in case something went wrong when trying to instantiate the registered class.
     * @throws FactoryException in case the type to be registered is not known.
     * @return object
     */
    public function getInstanceOf($type)
    {
        if (!isset($this->register[$type]) || 1 === $this->register[$type]) {
            throw new FactoryException('Unknown type (' . $type .')!', FactoryException::UnknownType);
        }

        if (empty($this->instances[$type])) {
            $params = func_get_args();
            // remove $type
            array_shift($params);

            $class = new ReflectionClass($this->register[$type]);

            if (empty($params)) {
                $this->instances[$type] = $class->newInstance();
            } else {
                $this->instances[$type] = $class->newInstanceArgs($params);
            }
        }

        return $this->instances[$type];
    }

    /**
     * Adds the given type and classname to a register
     *
     * @param string $type
     * @param string $class
     * @throws FactoryException in case the type to be registered is not known.
     */
    public function register($type, $class)
    {
        if (!isset($this->register[$type])) {
            throw new FactoryException('Unknown type (' . $type .')!', FactoryException::UnknownType);
        }

        if (! $this->implementsMandatoryInterfaces($class)) {
            throw new FactoryException(
                'The given class (' . $class . ') does neither implement "Extensions_Webservice_Listener_Logger_Interface" '.
                'nor "Extensions_Webservice_Listener_HttpClient_Interface"!',
                FactoryException::NotAllowedtoRegister
            );
        }
        $this->register[$type] = $class;
    }

    /**
     * Determines if the class to be registered implements a supported interface.
     *
     * @param string $class
     * @return boolean true, if at least on of the mandatory interfaces is implemented.
     */
    protected function implementsMandatoryInterfaces($class)
    {
        try {
            $reflection = new ReflectionClass($class);
            foreach ($this->interfaces as $interface) {
                if ($reflection->implementsInterface($interface)) {
                    return true;
                }
            }
        } catch (ReflectionException $re) {
            return false;
        }
        return false;
    }
}

class FactoryException extends \Exception
{
    const UnknownType = 1;
    const NotAllowedtoRegister = 2;
}