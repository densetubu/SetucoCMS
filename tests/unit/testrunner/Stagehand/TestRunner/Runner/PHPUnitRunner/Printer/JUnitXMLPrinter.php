<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2009-2011 KUBO Atsuhiro <kubo@iteman.jp>,
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
 * @copyright  2009-2011 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 2.19.0
 * @link       http://www.phpunit.de/
 * @since      File available since Release 2.10.0
 */

require_once 'PHPUnit/Util/Class.php';
require_once 'PHPUnit/Util/Filter.php';
require_once 'PHPUnit/Util/Printer.php';
require_once 'PHPUnit/Util/XML.php';

/**
 * A result printer for PHPUnit.
 *
 * @package    Stagehand_TestRunner
 * @copyright  2009-2011 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 2.19.0
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 2.10.0
 */
class Stagehand_TestRunner_Runner_PHPUnitRunner_Printer_JUnitXMLPrinter extends PHPUnit_Util_Printer implements PHPUnit_Framework_TestListener, Stagehand_TestRunner_Runner_JUnitXMLWriterAdapter
{
    protected $autoFlush = true;
    protected $xmlWriter;
    protected $testSuitesWrote = false;
    protected $testStarted = false;

    public function flush()
    {
        $this->xmlWriter->endTestSuites();
        parent::flush();
    }

    /**
     * @param PHPUnit_Framework_Test $test
     * @param Exception              $e
     * @param float                  $time
     */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->writeError($test, $e, $time);
    }

    /**
     * @param PHPUnit_Framework_Test                 $test
     * @param PHPUnit_Framework_AssertionFailedError $e
     * @param float                                  $time
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->writeFailure($test, $e, $time);
    }

    /**
     * @param PHPUnit_Framework_Test $test
     * @param Exception              $e
     * @param float                  $time
     */
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->writeError($test, $e, $time);
    }

    /**
     * Skipped test.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  Exception              $e
     * @param  float                  $time
     */
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->writeError($test, $e, $time);
    }

    /**
     * @param PHPUnit_Framework_TestSuite $suite
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        if (!$this->testSuitesWrote) {
            $this->xmlWriter->startTestSuites();
            $this->testSuitesWrote = true;
        }

        $name = $suite->getName();
        if (preg_match('/^(.+)::(.+)/', $name, $matches)) {
            $this->currentTestClassName = $matches[1];
        } else {
            $this->currentTestClassName = $name;
        }

        $this->xmlWriter->startTestSuite($name, count($suite));
    }

    /**
     * @param PHPUnit_Framework_TestSuite $suite
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $this->xmlWriter->endTestSuite();
    }

    /**
     * @param PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        $this->xmlWriter->startTestCase($test->getName(), $test, $test->getName(false));
        $this->testStarted = true;
    }

    /**
     * @param PHPUnit_Framework_Test $test
     * @param float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        if ($test instanceof PHPUnit_Framework_TestCase) {
            $this->xmlWriter->endTestCase($time, $test->getNumAssertions());
        } else {
            $this->xmlWriter->endTestCase($time);
        }
        $this->testStarted = false;
    }

    /**
     * @param Stagehand_TestRunner_JUnitXMLWriter $xmlWriter
     */
    public function setXMLWriter(Stagehand_TestRunner_JUnitXMLWriter $xmlWriter)
    {
        $this->xmlWriter = $xmlWriter;
    }

    /**
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     * @since Method available since Release 2.17.0
     */
    protected function writeError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->writeFailureOrError($test, $e, $time, 'error');
    }

    /**
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     * @since Method available since Release 2.17.0
     */
    protected function writeFailure(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        $this->writeFailureOrError($test, $e, $time, 'failure');
    }

    /**
     * @param PHPUnit_Framework_Test $test
     * @param Exception $e
     * @param float $time
     * @param string $failureOrError
     */
    protected function writeFailureOrError(PHPUnit_Framework_Test $test, Exception $e, $time, $failureOrError)
    {
        $testIsArtificial = false;
        if (!$this->testStarted) {
            $this->startTest($test);
            $testIsArtificial = true;
        }

        if ($test instanceof PHPUnit_Framework_SelfDescribing) {
            $message = $test->toString() . "\n\n";
        } else {
            $message = '';
        }

        $message .= PHPUnit_Framework_TestFailure::exceptionToString($e) . "\n";

        if ($test instanceof PHPUnit_Framework_Warning) {
            $testClass = new ReflectionClass($this->currentTestClassName);
            $file = $testClass->getFileName();
            $line = 1;
        } else {
            list($file, $line) = $this->findFileAndLineOfFailureOrError($e, new ReflectionClass($test));
        }
        $trace = PHPUnit_Util_Filter::getFilteredStacktrace($e, false);
        $this->xmlWriter->{ 'write' . $failureOrError }(
            $message .
            $trace,
            get_class($e),
            $file,
            $line,
            $message,
            $trace
        );

        if ($testIsArtificial) {
            $this->endTest($test, 0);
        }
    }

    /**
     * @param Exception $e
     * @param ReflectionClass $class
     * @return array
     * @since Method available since Release 2.16.0
     */
    protected function findFileAndLineOfFailureOrError(Exception $e, ReflectionClass $class)
    {
        if ($class->getName() == 'PHPUnit_Framework_TestCase') return;
        if ($e->getFile() == $class->getFileName()) {
            return array($e->getFile(), $e->getLine());
        }
        foreach ($e->getTrace() as $trace) {
            if (array_key_exists('file', $trace) && $trace['file'] == $class->getFileName()) {
                return array($trace['file'], $trace['line']);
            }
        }
        return $this->findFileAndLineOfFailureOrError($e, $class->getParentClass());
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
