=============================
PHPUnit WebServices Extension
=============================

Assertions Component
====================
Hopefully this gets integraded into PHPUnit.

Test Listener Component
=======================
This component fetches the response from the 

Example Configuation
--------------------

    <listeners>
      <listener class="WebServiceListener">
        <arguments>
            <object class="Factory"/>
            <object class="ConfigurationLoader"/>
            <array>
              <element key="httpClient">
                <string>Buzz</string>
              </element>
              <element key="logger">
                <string>MonoLog</string>
              </element>
              <element key="configuration">
                <string>/path/to/configuration.(xml|yml|json|txt)</string>
              </element>
            </array>
          </arguments>
        </listener>
      </listeners>

Dependencies
============

Monolog
-------