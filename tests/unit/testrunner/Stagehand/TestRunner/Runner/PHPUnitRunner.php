<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2007-2011 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Stagehand_TestRunner
 * @copyright  2007-2011 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 2.19.0
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.1.0
 */

/**
 * A test runner for PHPUnit.
 *
 * @package    Stagehand_TestRunner
 * @copyright  2007-2011 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 2.19.0
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 2.1.0
 */
class Stagehand_TestRunner_Runner_PHPUnitRunner extends Stagehand_TestRunner_Runner
{
    /**
     * Runs tests based on the given PHPUnit_Framework_TestSuite object.
     *
     * @param PHPUnit_Framework_TestSuite $suite
     */
    public function run($suite)
    {
        $testResult = new PHPUnit_Framework_TestResult();
        $printer = new Stagehand_TestRunner_Runner_PHPUnitRunner_Printer_ResultPrinter(
                       null, true, $this->config->colors()
                   );

        $arguments = array();
        $arguments['printer'] = $printer;

        Stagehand_TestRunner_Runner_PHPUnitRunner_TestDox_Stream::register();
        $arguments['listeners'] =
            array(
                new Stagehand_TestRunner_Runner_PHPUnitRunner_Printer_TestDoxPrinter(
                    fopen('testdox://' . spl_object_hash($testResult), 'w'),
                    $this->config->colors(),
                    $this->prettifier()
                )
            );
        if (!$this->config->printsDetailedProgressReport) {
            $arguments['listeners'][] =
                new Stagehand_TestRunner_Runner_PHPUnitRunner_Printer_ProgressPrinter(
                    null, false, $this->config->colors()
                );
        } else {
            $arguments['listeners'][] =
                new Stagehand_TestRunner_Runner_PHPUnitRunner_Printer_DetailedProgressPrinter(
                    null, false, $this->config->colors()
                );
        }

        if ($this->config->logsResultsInJUnitXML) {
            $junitXMLListener = new Stagehand_TestRunner_Runner_PHPUnitRunner_Printer_JUnitXMLPrinter($this->config->junitXMLFile);
            if (!$this->config->logsResultsInJUnitXMLInRealtime) {
                $xmlWriter =
                    new Stagehand_TestRunner_JUnitXMLWriter_JUnitXMLDOMWriter(
                        array($junitXMLListener, 'write')
                    );
            } else {
                $xmlWriter = $this->junitXMLStreamWriter(array($junitXMLListener, 'write'));
            }
            $junitXMLListener->setXMLWriter($xmlWriter);
            $arguments['listeners'][] = $junitXMLListener;
        }

        if ($this->config->stopsOnFailure) {
            $arguments['stopOnFailure'] = true;
            $arguments['stopOnError'] = true;
        }

        if (!is_null($this->config->phpunitConfigFile)) {
            $arguments['configuration'] = $this->config->phpunitConfigFile;
        }

        $testRunner = new Stagehand_TestRunner_Runner_PHPUnitRunner_TestRunner();
        $testRunner->setTestResult($testResult);
        $testRunner->doRun($suite, $arguments);

        if ($this->config->usesNotification) {
            if ($testResult->failureCount() + $testResult->errorCount() + $testResult->skippedCount() + $testResult->notImplementedCount() == 0) {
                $notificationResult = Stagehand_TestRunner_Notification_Notification::RESULT_PASSED;
            } else {
                $notificationResult = Stagehand_TestRunner_Notification_Notification::RESULT_FAILED;
            }

            ob_start();
            $printer->printResult($testResult);
            $output = ob_get_contents();
            ob_end_clean();

            if (preg_match('/^(?:\x1b\[30;42m\x1b\[2K)?(OK .+)/m', $output, $matches)) {
                $notificationMessage = $matches[1];
            } elseif (preg_match('/^(?:\x1b\[37;41m\x1b\[2K)?(FAILURES!)\s^(?:\x1b\[0m\x1b\[37;41m\x1b\[2K)?(.+)/m', $output, $matches)) {
                $notificationMessage = $matches[1] . "\n" . $matches[2];
            } elseif (preg_match('/^(?:\x1b\[30;43m\x1b\[2K)?(OK, but incomplete or skipped tests!)\s^(?:\x1b\[0m\x1b\[30;43m\x1b\[2K)?(.+)/m', $output, $matches)) {
                $notificationMessage = $matches[1] . "\n" . $matches[2];
            }

            $this->notification = new Stagehand_TestRunner_Notification_Notification($notificationResult, $notificationMessage);
        }
    }

    /**
     * @return PHPUnit_Util_TestDox_NamePrettifier
     * @since Method available since Release 2.7.0
     */
    protected function prettifier()
    {
        if (version_compare(PHPUnit_Runner_Version::id(), '3.5.14', '>=')) {
            require_once 'PHPUnit/Util/TestDox/NamePrettifier.php';
            return new PHPUnit_Util_TestDox_NamePrettifier();
        } else {
            return new Stagehand_TestRunner_Runner_PHPUnitRunner_TestDox_NamePrettifier();
        }
    }

    /**
     * @param callback $streamWriter
     * @return Stagehand_TestRunner_JUnitXMLWriter_JUnitXMLStreamWriter
     * @since Method available since Release 2.10.0
     */
    protected function junitXMLStreamWriter($streamWriter)
    {
        return new Stagehand_TestRunner_JUnitXMLWriter_JUnitXMLStreamWriter($streamWriter);
    }
}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
