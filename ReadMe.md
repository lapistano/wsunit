=============================
PHPUnit WebServices Extension
=============================
The possibility to register test listeners to PHPUnit makes it extremly easy to execute actions on a certain state of the test runner (e.g. when the test is started). WSUnit make highly use of this possibility and listens to some of the emmited signals.

Purpose
========
WSUnit lifts unit tests to functional tests by configuration. Once configured the test listener sends a request to a specified location, records the respond body & header, and persists it onto the filesystem. The test itself then e.g. verifies the correctness of the response. The Idea behind this was not to be forced to write API test more than once and to increase the reuse of already written verifications.

Installation
============
There is not much to install but to download the sources, configure the test listener in the phpunit.xml.dist, and create the configuration file to tell the listener which test shall call which url ot fetch the response. If you are using composer you simply have to define the dependency to WSUnit in your configuration and set up WSUnit accourding to the following description.

GitHub
------
$ git clone git://github.com/lapistano/wsunit.git

Configuation
============
The configuration has two parts. One is the registration of the actual test listener to PHPUnit, the 2nd is the definition of which test (identified by it's name) shall be request the response of which location.

PHPUnit
-------
For the PHPUnit configuration please visit http://www.phpunit.de/manual/current/en/appendixes.configuration.html and
read the 'Test Listener' section or copy the example to your configuration and simply adapt the location of your configuration file.

```xml
<listeners>
  <listener class="WebServiceListener">
    <arguments>
        <object class="Extensions_Webservice_Listener_Factory"/>
        <object class="Extensions_Webservice_Listener_Loader_Configuration"/>
        <array>
          <element key="httpClient">
            <string>Extensions_Webservice_Listener_Http_Client</string>
          </element>
          <element key="logger">
            <string>Extensions_Webservice_Listener_Logger</string>
          </element>
          <element key="configuration">
            <string>/path/to/configuration.xml</string>
          </element>
        </array>
      </arguments>
    </listener>
  </listeners>
```

Arguments
~~~~~~~~~

(object) Extensions_Webservice_Listener_Factory
    Factory class providing objects mandatory for the operation of the listener.
    
(object) Extensions_Webservice_Listener_Loader_Configuration
    Object to load the configuration file.

(array) Contains the names of classes to be registered to the factory and the location of the location definition file.


Test listener configuration
----------------------------
Beside making PHPUnit aware of the test listener and to actually make each test aware of the loaction the response shall be fetched from, a 2nd configuration file is needed. The following example show such a configuration.

**NOTE:**
The name and the location of the configuration file is set in the `element[key='configuration']` element of the test listener registration in PHPUnit.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<listener>
    <serializer>Extensions_Webservice_Serializer_Http_Response</serializer>
    <test case="Example_TestCase" name="testGetData">
        <location href="http://example.org/data.txt" />
    </test>
    <test case="Extensions_Webservice_Constraint_JsonErrorMessageProviderTest" name='testTranslateTypeToPrefix with data set "expected"'>
        <serializer>Extensions_Webservice_Serializer_Http_Response</serializer>
        <location dataName="expected" href="http://blog.bastian-feder.de/blog.rss">
            <query>
              <param name="mascott[]">tux</param>
              <param name="mascott[RedHat]">beastie</param>
              <param name="os">Linux</param>
            </query>
        </location>
    </test>
</listener>
```
Available tags
~~~~~~~~~~~~~~

- **test**
Encapsulated with the 'listener' tag as root tag each test to be recognized is represented by a 'test' tag and section. A test is recognized by the test case (case attribute) and name (name attribute) of a test. If your test registers a dataprovider be aware that the name of the test will be altered by PHPUnit (see the second test section in the example).

- **serializer**
This tag can be defined in the <listener> tag to be to overall used serialize or in the <test> configuration to override a globally set serializer. 

- **location**
Defined within the test section it defines the location and it's optional query string. Once these information are set the configured location will be tackled for it's response.

Dependencies
============
- [PHPUnit](http://github.com/sebastianbergmann/phpunit)
