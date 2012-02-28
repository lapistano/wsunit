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
                <string>/path/to/configuration.(xml|yml|json|txt)</string>
              </element>
            </array>
          </arguments>
        </listener>
      </listeners>

Dependencies
============
