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

namespace lapistano\wsuniti\Serializer\Type;

use lapistano\wsunit\Serializer\Extensions_Webservice_Serializer_Exception;

use lapistano\wsunit\Serializer\Type\Extensions_Webservice_Serializer_Type;

/**
 * Serizlizer definition for a XML response.
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

class Extensions_Webservice_Serializer_Type_Xml extends Extensions_Webservice_Serializer_Type
{
    /**
     * Name of the current serialization type
     * @var string
     */
    protected $name = 'Xml';


    /**
     * Does the actual serialization.
     *
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        // verify if $data is valid xml
        $this->isValid($data);

        /* remove <?xml ... ?> line before returning the body */
        return preg_replace('#<\?xml [^>]+>#', '', $data);
    }

    /**
     * Provides the name of the current serializer type.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Checks it the given string is a valid XMLM string.
     *
     * @param string $data
     * @throws \lapistano\wsunit\Serializer\Extensions_Webservice_Serializer_Exception in case an invalid data string was passed.
     */
    protected function isValid($data)
    {
        if (!$dom = \DOMDocument::loadXml($data)) {
            throw new \InvalidArgumentException(
                'Given data set is not a valid XML string!',
                Extensions_Webservice_Serializer_Exception::InvalidType
            );
        }
    }
}
