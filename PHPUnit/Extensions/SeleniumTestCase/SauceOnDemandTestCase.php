<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2010-2011, Sebastian Bergmann <sb@sebastian-bergmann.de>.
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
 * @package    PHPUnit_Selenium
 * @author     Jan Sorgalla <jan.sorgalla@dotsunited.de>
 * @copyright  2010-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.5.0
 */

require_once 'PHPUnit/Extensions/SeleniumTestCase/SauceOnDemandTestCase/Driver.php';
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
require_once('SymfonyComponents/YAML/sfYamlParser.php');

/**
 * TestCase class that uses Sauce OnDemand to provide
 * the functionality required for web testing.
 *
 * @package    PHPUnit_Selenium
 * @author     Jan Sorgalla <jan.sorgalla@dotsunited.de>
 * @copyright  2010-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 3.5.0
 */
abstract class PHPUnit_Extensions_SeleniumTestCase_SauceOnDemandTestCase extends PHPUnit_Extensions_SeleniumTestCase
{
    /**
     * Whether to automatically report if a test passed/failed to Sauce OnDemand
     *
     * @see http://saucelabs.com/docs/sauce-ondemand#passed
     * @var boolean
     */
    protected $reportPassFailToSauceOnDemand = true;

    /**
     * @param  array $browser
     * @return PHPUnit_Extensions_SeleniumTestCase_Driver
     * @since  Method available since Release 3.3.0
     */
    protected function getDriver(array $browser)
    {
        if (isset($browser['name'])) {
            if (!is_string($browser['name'])) {
                throw new InvalidArgumentException(
                  'Array element "name" is not an string.'
                );
            }
        } else {
            $browser['name'] = '';
        }

        if (isset($browser['browser'])) {
            if (!is_string($browser['browser'])) {
                throw new InvalidArgumentException(
                  'Array element "browser" is not a string.'
                );
            }
            $this->browserName = $browser['browser'];
        } else {
            $browser['browser'] = '';
        }


        if (isset($browser['host'])) {
            if (!is_string($browser['host'])) {
                throw new InvalidArgumentException(
                  'Array element "host" is not a string.'
                );
            }
        } else {
            $browser['host'] = 'ondemand.saucelabs.com';
        }

        if (isset($browser['port'])) {
            if (!is_int($browser['port'])) {
                throw new InvalidArgumentException(
                  'Array element "port" is not an integer.'
                );
            }
        } else {
            $browser['port'] = 80;
        }

        if (isset($browser['timeout'])) {
            if (!is_int($browser['timeout'])) {
                throw new InvalidArgumentException(
                  'Array element "timeout" is not an integer.'
                );
            }
        } else {
            $browser['timeout'] = 30;
        }

        if (isset($browser['httpTimeout'])) {
            if (!is_int($browser['httpTimeout'])) {
                throw new InvalidArgumentException(
                  'Array element "httpTimeout" is not an integer.'
                );
            }
        } else {
            $browser['httpTimeout'] = 45;
        }

        $driver = new PHPUnit_Extensions_SeleniumTestCase_SauceOnDemandTestCase_Driver;
        $driver->setName($browser['name']);
        $driver->setBrowser($browser['browser']);
        $driver->setHost($browser['host']);
        $driver->setPort($browser['port']);
        $driver->setTimeout($browser['timeout']);
        $driver->setHttpTimeout($browser['httpTimeout']);
        $driver->setTestCase($this);
        $driver->setTestId($this->testId);

		/*
		 * N.B.	$_SERVER['HOME'] is not available on Windows. 
		 * 		Instead, the variable is split into $_SERVER['HOMEDRIVE'] and $_SERVER['HOMEPATH']
		 */
		$home=isset($_SERVER['HOME'])?$_SERVER['HOME']:$_SERVER['HOMEDRIVE'].$_SERVER['HOMEPATH']; 
		
        $yml_path = realpath($home) . '/.sauce/ondemand.yml';
        $yml_found = file_exists($yml_path);
        if(!$yml_found) {
            $yml_path = '/.sauce/ondemand.yml';
            $yml_found = file_exists($yml_path);
        }
        if($yml_found) {
            if(!isset($this->yaml)) {
                $this->yaml = new sfYamlParser();
            }
            $pearsauce_config = $this->yaml->parse(file_get_contents($yml_path));
        }

        if (isset($browser['username'])) {
            if (!is_string($browser['username'])) {
                throw new InvalidArgumentException(
                  'Array element "username" is not a string.'
                );
            }

            $driver->setUsername($browser['username']);
        } elseif($yml_found && isset($pearsauce_config['username'])) {
            $driver->setUsername($pearsauce_config['username']);
        } else {
            error_log('Warning: no username provided. This may result in "Could not connect to Selenium RC serve".  Run "sauce configure <username> <accesskey>" or call $this->setUsername to fix');
        }

        if (isset($browser['accessKey'])) {
            if (!is_string($browser['accessKey'])) {
                throw new InvalidArgumentException(
                  'Array element "accessKey" is not a string.'
                );
            }

            $driver->setAccessKey($browser['accessKey']);
        } elseif($yml_found && isset($pearsauce_config['access_key'])) {
            $driver->setAccessKey($pearsauce_config['access_key']);
        } else {
            error_log('Warning: no access key provided. This may result in "Could not connect to Selenium RC serve".  Run "sauce configure <username> <accesskey>" or call $this->setAccessKey to fix');
        }

        if (isset($browser['browserVersion'])) {
            if (!is_string($browser['browserVersion'])) {
                throw new InvalidArgumentException(
                  'Array element "browserVersion" is not a string.'
                );
            }

            $driver->setBrowserVersion($browser['browserVersion']);
            $this->browserName .= " ".$browser['browserVersion'];
        }

        if (isset($browser['os'])) {
            if (!is_string($browser['os'])) {
                throw new InvalidArgumentException(
                  'Array element "os" is not a string.'
                );
            }

            $driver->setOs($browser['os']);
            $this->browserName .= " ".$browser['os'];
        }

        if (isset($browser['jobName'])) {
            if (!is_string($browser['jobName'])) {
                throw new InvalidArgumentException(
                  'Array element "jobName" is not a string.'
                );
            }

            $driver->setJobName($browser['jobName']);
        }

        if (isset($browser['public'])) {
            if (!is_bool($browser['public'])) {
                throw new InvalidArgumentException(
                  'Array element "public" is not a boolean.'
                );
            }

            $driver->setPublic($browser['public']);
        }

        if (isset($browser['tags'])) {
            if (!is_array($browser['tags'])) {
                throw new InvalidArgumentException(
                  'Array element "tags" is not an array.'
                );
            }

            $driver->setTags($browser['tags']);
        }

        if (isset($browser['passed'])) {
            if (!is_bool($browser['passed'])) {
                throw new InvalidArgumentException(
                  'Array element "passed" is not a boolean.'
                );
            }

            $driver->setPassed($browser['passed']);
        }

        if (isset($browser['recordVideo'])) {
            if (!is_bool($browser['recordVideo'])) {
                throw new InvalidArgumentException(
                  'Array element "recordVideo" is not a boolean.'
                );
            }

            $driver->setRecordVideo($browser['recordVideo']);
        }

        if (isset($browser['recordScreenshots'])) {
            if (!is_bool($browser['recordScreenshots'])) {
                throw new InvalidArgumentException(
                  'Array element "recordScreenshots" is not a boolean.'
                );
            }

            $driver->setRecordScreenshots($browser['recordScreenshots']);
        }

        if (isset($browser['sauceAdvisor'])) {
            if (!is_bool($browser['sauceAdvisor'])) {
                throw new InvalidArgumentException(
                  'Array element "sauceAdvisor" is not a boolean.'
                );
            }

            $driver->setSauceAdvisor($browser['sauceAdvisor']);
        }

        if (isset($browser['singleWindow'])) {
            if (!is_bool($browser['singleWindow'])) {
                throw new InvalidArgumentException(
                  'Array element "singleWindow" is not a boolean.'
                );
            }

            $driver->setSingleWindow($browser['singleWindow']);
        }

        if (isset($browser['userExtensionsUrl'])) {
            if (!is_string($browser['userExtensionsUrl']) && !is_array($browser['userExtensionsUrl'])) {
                throw new InvalidArgumentException(
                  'Array element "userExtensionsUrl" is not a string/array.'
                );
            }

            $driver->setUserExtensionsUrl($browser['userExtensionsUrl']);
        }

        if (isset($browser['firefoxProfileUrl'])) {
            if (!is_string($browser['firefoxProfileUrl'])) {
                throw new InvalidArgumentException(
                  'Array element "firefoxProfileUrl" is not a string.'
                );
            }

            $driver->setFirefoxProfileUrl($browser['firefoxProfileUrl']);
        }

        if (isset($browser['maxDuration'])) {
            if (!is_int($browser['maxDuration'])) {
                throw new InvalidArgumentException(
                  'Array element "maxDuration" is not an integer.'
                );
            }

            $driver->setMaxDuration($browser['maxDuration']);
        }

        if (isset($browser['idleTimeout'])) {
            if (!is_int($browser['idleTimeout'])) {
                throw new InvalidArgumentException(
                  'Array element "idleTimeout" is not an integer.'
                );
            }

            $driver->setIdleTimeout($browser['idleTimeout']);
        }

        if (isset($browser['build'])) {
            if (!is_string($browser['build'])) {
                throw new InvalidArgumentException(
                  'Array element "build" is not a string.'
                );
            }

            $driver->setBuild($browser['build']);
        }

        if (isset($browser['customData'])) {
            if (!is_array($browser['customData']) && !is_object($browser['customData'])) {
                throw new InvalidArgumentException(
                  'Array element "customData" is not an array/object.'
                );
            }

            $driver->setCustomData($browser['customData']);
        }

        if (isset($browser['avoidProxy'])) {
            if (!is_bool($browser['avoidProxy'])) {
                throw new InvalidArgumentException(
                  'Array element "avoidProxy" is not a boolean.'
                );
            }

            $driver->setAvoidProxy($browser['avoidProxy']);
        }

        $this->drivers[0] = $driver;

        return $driver;
    }

    /**
     * Sets whether to automatically report if a test passed/failed to Sauce OnDemand.
     *
     * @param boolean $flag
     * @throws InvalidArgumentException
     */
    public function setReportPassFailToSauceOnDemand($flag)
    {
        if (!is_bool($flag)) {
            throw PHPUnit_Util_InvalidArgumentHelper::factory(1, 'boolean');
        }

        $this->reportPassFailToSauceOnDemand = $flag;
    }

    /**
     * Gets whether to automatically report if a test passed/failed to Sauce OnDemand.
     *
     * @return boolean
     */
    public function getReportPassFailToSauceOnDemand()
    {
        return $this->reportPassFailToSauceOnDemand;
    }

    /**
     * Intercept stop() call.
     */
    public function stop()
    {
        if ($this->getReportPassFailToSauceOnDemand()) {
            // Set passed option
            if ($this->hasFailed()) {
                $passed = 'false';
            } else {
                $passed = 'true';
            }

            $this->setContext('sauce:job-info={"passed": ' . $passed . '}');
        }

        return $this->__call('stop', array());
    }

    /**
     * This method is called when a test method did not execute successfully.
     *
     * @param Exception $e
     * @since Method available since Release 3.4.0
     */
    protected function onNotSuccessfulTest(Exception $e)
    {
        try {
            $jobUrl = sprintf(
                'https://saucelabs.com/jobs/%s',
                $this->drivers[0]->getSessionId()
            );

            $buffer = 'Current Browser URL: ' . $this->drivers[0]->getLocation() .
                      "\n" .
                      'Sauce Labs Job: ' . $jobUrl .
                      "\n";

            $this->stop();

        } catch (RuntimeException $d) {
            if (!isset($buffer)) $buffer = "";
        }

        $message = $e->getMessage();

        if (!empty($message)) {
            $buffer = "\n".$message."\n".$buffer;
        }

        throw new PHPUnit_Framework_Exception($buffer);
    }
}
