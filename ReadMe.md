
> ===============================================================================
>
> **!! DISCONTINUED !!**
> 
> » Please use the successing project [PHP-VCR](http://github.com/adri/php-vcr) «
>
>================================================================================


PHPUnit WebServices Extension
=============================
The possibility to register test listeners to PHPUnit makes it extremly easy to execute actions on a certain state of
the test runner (e.g. when the test is started). WSUnit make highly use of this possibility and listens to some of
the emmited signals.

Current travis status: [![Build Status](https://secure.travis-ci.org/lapistano/wsunit.png?branch=master)](http://travis-ci.org/lapistano/wsunit)

Purpose
========
WSUnit lifts unit tests to functional tests by configuration. Once configured the test listener sends a request to a
specified location, records the respond body & header, and persists it onto the filesystem. The test itself then
e.g. verifies the correctness of the response. The Idea behind this was not to be forced to write API test more than
once and to increase the reuse of already written verifications.

Installation
============
There is not much to install but to download the sources, configure the test listener in the phpunit.xml.dist,
and create the configuration file to tell the listener which test shall call which url ot fetch the response. If you
are using composer you simply have to define the dependency to WSUnit in your configuration and set up WSUnit
accourding to the following description.

Composer
--------
```json
{
    "require-dev": {
        "lapistano/wsunit": " 2.*"
    }
}
```
This composer configuration will checkout the sources tagged as the 2nd release. In case your want the 'cutting eadge' version
replace '2.*' by 'dev-master'. But be alarmed that this might be broken sometimes.

**NOTE:**
In case you do not know what this means the [composer project website](http://getcomposer.org) is a good place to start.

Github
------
Thus I recommend the composer way to make proxy-object a dependency to your project. 
The sources are also available via github. Just clone it as you might be familiar with.

```bash
$ git clone git://github.com/lapistano/wsunit.git
$ mkdir -p wsunit/vendor/lapistano
$ cd wsunit/vendor/lapistano
$ git clone git://github.com/lapistano/proxy-object.git
```

Configuation
============
The configuration has two parts. One is the registration of the actual test listener to PHPUnit,
the 2nd is the definition of which test (identified by it's name) shall be request the response of which location.

PHPUnit
-------
For the PHPUnit configuration please visit http://www.phpunit.de/manual/current/en/appendixes.configuration.html and
read the 'Test Listener' section or copy the example to your configuration and simply adapt the location of your
configuration file.

```xml
<listeners>
  <listener class="\lapistano\wsunit\WebServiceListener">
    <arguments>
        <object class="\lapistano\wsunit\ExtensionsWebserviceListenerFactory"/>
        <object class="\lapistano\wsunit\Loader\LoaderConfiguration"/>
        <array>
          <element key="httpClient">
            <string>lapistano\wsunit\Http\HttpClient</string>
          </element>
          <element key="logger">
            <string>lapitano\wsunit\Logger\LoggerFilesystem</string>
          </element>
          <element key="configuration">
            <string>/path/to/configuration.xml</string>
          </element>
        </array>
      </arguments>
    </listener>
  </listeners>
```
### Arguments

`(object) Extensions_Webservice_Listener_Factory`
    Factory class providing objects mandatory for the operation of the listener.

`(object) Extensions_Webservice_Listener_Loader_Configuration`
    Object to load the configuration file.

`(array)` Contains the names of classes to be registered to the factory and the location of the location definition file.


Test listener configuration
----------------------------
Beside making PHPUnit aware of the test listener and to actually make each test aware of the loaction the response
shall be fetched from, a 2nd configuration file is needed. The following example show such a configuration.

**NOTE:**
The name and the location of the configuration file is set in the `element[key='configuration']` element of the test
listener registration in PHPUnit.


**WARNING:**
Beware that if you decide to use namespaces they also have to be used in the phpunit configuration file to identify the used classes.
In case you did something wrong here PHPUnit will just ignore your listener without any warning or error being thrown. Don't ask me why I know this.
This behavior is fixed in PHPUnit 3.7.


```xml
<?xml version="1.0" encoding="UTF-8"?>
<listener>
    <serializer>\lapistano\wsunit\Serializer\Http\Extensions_Webservice_Serializer_Http_Response</serializer>
    <test case="Example_TestCase" name="testGetData">
        <location href="http://example.org/data.txt" />
    </test>
    <test case="\lapistano\wsunit\Extensions_Webservice_Constraint_JsonErrorMessageProviderTest"
          name='testTranslateTypeToPrefix with data set "expected"'
    >
        <serializer>\lapistano\wsunit\Serializer\Http\Extensions_Webservice_Serializer_Http_Response</serializer>
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

###Available tags

- **test**
Encapsulated with the 'listener' tag as root tag each test to be recognized is represented by a 'test' tag and
section. A test is recognized by the test case (case attribute) and name (name attribute) of a test. If your test
registers a dataprovider be aware that the name of the test will be altered by PHPUnit (see the second test section
in the example).

- **serializer**
This tag can be defined in the <listener> tag to be to overall used serialize or in the <test> configuration to
override a globally set serializer.

- **location**
Defined within the test section it defines the location and it's optional query string. Once these information are
set the configured location will be tackled for it's response.

Dependencies
============
- [PHPUnit](http://github.com/sebastianbergmann/phpunit)

Optional
--------
- [proxy-object](http://github.com/lapistano/proxy-object) in case you want to run the test suite. It has to be put in
  the `vendor` directory, located in the project root. You either clone it from github or use composer to fetch it.

To be done
==========
As far as ws unit has come by now, unfortunately it is far from being complete. The following list shall give you an
idea about what to be expected next:

- Add name of the test case to name of persisted fixture file to ensure uniqueness.
- Implement a generic (fallback) serializer to be able to persits any response.
- Provide ability to use wsunit with Symfony2 (this might already be ok, but it is not tested yet)
- Open new repository for serialisers, loaders, and loggers to be as compatible as one could be.
- Provide more serializer, loader, and logger implementations (e.g to load the configuration from yml- files)

If you have other usecases, ideas and/or demands feel free to fork and contribute. You are more than welcome ;)
