<?php

/**
 * Unit tests for dev-platform auto config
 */
class ConfigIntegrationTest extends PHPUnit_Framework_TestCase
{
    protected $config;

    public function setUp() {
        $this->config = json_decode('{
          "dev-platform": {
            "host": "http://localhost:5000",
            "v1_path": "/api/config/v1/environments"
          },
          "environment": {
            "name": "local",
            "api_key": "5e351c6a-b385-425d-b7d2-baadb22f9476",
            "api_secret": "SFHrk0XVRZXfQS9hO8stYA"
          }
        }', true);
    }

    public function testConfigure() {
        $result = Maestrano_Config_Client::with('dev-platform')->configure($this->config);

        $this->assertEquals($result['dev-platform.host'], 'http://localhost:5000');
        $this->assertEquals($result['dev-platform.v1_path'], '/api/config/v1/environments');
        $this->assertEquals($result['environment.name'], 'local');
        $this->assertEquals($result['environment.api_key'], '5e351c6a-b385-425d-b7d2-baadb22f9476');
        $this->assertEquals($result['environment.api_secret'], 'SFHrk0XVRZXfQS9hO8stYA');
    }

    public function testConfigureHostError() {
        unset($this->config['dev-platform']['host']);
        $this->setExpectedException('Maestrano_Config_Error', 'Missing \'dev-platform.host\' parameter in dev-platform config file.');
        Maestrano_Config_Client::with('dev-platform')->configure($this->config);
    }

    public function testConfigurePathError() {
        unset($this->config['dev-platform']['v1_path']);
        $this->setExpectedException('Maestrano_Config_Error', 'Missing \'dev-platform.v1_path\' parameter in dev-platform config file.');
        Maestrano_Config_Client::with('dev-platform')->configure($this->config);
    }

    public function testConfigureEnvironmentNameError() {
        unset($this->config['environment']['name']);
        $this->setExpectedException('Maestrano_Config_Error', 'Missing \'environment.name\' parameter in dev-platform config file.');
        Maestrano_Config_Client::with('dev-platform')->configure($this->config);
    }

    public function testConfigureEnvironmentKeyError() {
        unset($this->config['environment']['api_key']);
        $this->setExpectedException('Maestrano_Config_Error', 'Missing \'environment.api_key\' parameter in dev-platform config file.');
        Maestrano_Config_Client::with('dev-platform')->configure($this->config);
    }

    public function testConfigureEnvironmentSecretError() {
        unset($this->config['environment']['api_secret']);
        $this->setExpectedException('Maestrano_Config_Error', 'Missing \'environment.api_secret\' parameter in dev-platform config file.');
        Maestrano_Config_Client::with('dev-platform')->configure($this->config);
    }

    public function testLoadMultipleMarketplaces() {
        $fromServer = json_decode('[
            {
              "marketplace": "maestrano-uat",
              "environment": "local",
              "app": {
                "host": "http://php-demoapp.maestrano.io"
              },
              "api": {
                "id": "e30ac587-54ee-429d-92ff-66efdd1abf32",
                "key": "6z3KsJaDfyFZ3VNTxIVzEg"
              }
            },
            {
              "marketplace": "maestrano-prod",
              "environment": "local",
              "app": {
                "host": "http://php-demoapp.maestrano.io"
              },
              "api": {
                "id": "e30ac587-54ee-429d-92ff-66efdd1abf32",
                "key": "6z3KsJaDfyFZ3VNTxIVzEg"
              }
            }
          ]', true);

        Maestrano_Config_Client::with('dev-platform')->loadMultipleMarketplaces($fromServer);

        $this->assertEquals(Maestrano::with('maestrano-uat')->param('environment'), 'local');
        $this->assertEquals(Maestrano::with('maestrano-prod')->param('environment'), 'local');
    }
}
