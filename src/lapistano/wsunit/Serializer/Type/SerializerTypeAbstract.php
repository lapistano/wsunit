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

namespace lapistano\wsunit\Serializer\Type;

/**
 * Type defintion of a serializer
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
abstract class SerializerTypeAbstract
{
    /**
     * Name of the current serialization type
     * @var string
     */
    protected $name = '';


    /**
     * Does the actual serialization.
     *
     * @param mixed $data
     * @return string
     */
    abstract public function serialize($data);

    /**
     * Provides the name of the current serializer type.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
