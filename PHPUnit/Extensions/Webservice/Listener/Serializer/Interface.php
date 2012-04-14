<?php

interface Extensions_Webservice_Serializer_Interface
{
    /**
     * Stringifies the registered data
     *
     * @return string
     */
    public function serialize();

    /**
     * Adds the given data to a registry.
     *
     * @param string $type
     * @param mixed $data
     */
    public function register( $type, $data);

    /**
     * Registers the given type in a local registry
     *
     * @param Extensions_Webservice_Serializer_Type $type
     * @throws Extensions_Webservice_Serializer_Exception
     */
    public function addType(Extensions_Webservice_Serializer_Type $type);

    /**
     * Registers a custom tag name to be used as the root element in the generated XML document.
     *
     * @param string $tagName
     * @throws Extensions_Webservice_Serializer_Exception
     */
    public function setDocumentRoot($tagName);
}