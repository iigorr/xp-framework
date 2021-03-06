<?php namespace net\xp_framework\unittest\tests;

use unittest\TestCase;
use unittest\TestSuite;
use util\collections\HashTable;
use lang\types\String;
use lang\types\ArrayList;

/**
 * TestCase
 *
 * @see   xp://unittest.TestListener
 */
class ListenerTest extends TestCase implements \unittest\TestListener {
  protected
    $suite        = null,
    $invocations  = null;  
    
  /**
   * Setup method. Creates a new test suite.
   */
  public function setUp() {
    $this->invocations= create('new util.collections.HashTable<string, lang.types.ArrayList>()');
    $this->suite= new TestSuite();
    $this->suite->addListener($this);
  }

  /**
   * Remove listener again at tearDown.
   */
  public function tearDown() {
    $this->suite->removeListener($this);
  }
  
  /**
   * Called when a test case starts.
   *
   * @param   unittest.TestCase failure
   */
  public function testStarted(TestCase $case) {
    $this->invocations[__FUNCTION__]= new ArrayList($case);
  }

  /**
   * Called when a test fails.
   *
   * @param   unittest.TestFailure failure
   */
  public function testFailed(\unittest\TestFailure $failure) {
    $this->invocations[__FUNCTION__]= new ArrayList($failure);
  }

  /**
   * Called when a test errors.
   *
   * @param   unittest.TestFailure error
   */
  public function testError(\unittest\TestError $error) {
    $this->invocations[__FUNCTION__]= new ArrayList($error);
  }

  /**
   * Called when a test raises warnings.
   *
   * @param   unittest.TestWarning warning
   */
  public function testWarning(\unittest\TestWarning $warning) {
    $this->invocations[__FUNCTION__]= new ArrayList($warning);
  }

  /**
   * Called when a test finished successfully.
   *
   * @param   unittest.TestSuccess success
   */
  public function testSucceeded(\unittest\TestSuccess $success) {
    $this->invocations[__FUNCTION__]= new ArrayList($success);
  }

  /**
   * Called when a test is not run because it is skipped due to a 
   * failed prerequisite.
   *
   * @param   unittest.TestSkipped skipped
   */
  public function testSkipped(\unittest\TestSkipped $skipped) {
    $this->invocations[__FUNCTION__]= new ArrayList($skipped);
  }

  /**
   * Called when a test is not run because it has been ignored by using
   * the @ignore annotation.
   *
   * @param   unittest.TestSkipped ignore
   */
  public function testNotRun(\unittest\TestSkipped $ignore) {
    $this->invocations[__FUNCTION__]= new ArrayList($ignore);
  }

  /**
   * Called when a test run starts.
   *
   * @param   unittest.TestSuite suite
   */
  public function testRunStarted(TestSuite $suite) {
    $this->invocations[__FUNCTION__]= new ArrayList($suite);
  }

  /**
   * Called when a test run finishes.
   *
   * @param   unittest.TestSuite suite
   * @param   unittest.TestResult result
   */
  public function testRunFinished(TestSuite $suite, \unittest\TestResult $result) {
    $this->invocations[__FUNCTION__]= new ArrayList($suite, $result);
  }

  #[@test]
  public function notifiedOnSuccess() {
    with ($case= new SimpleTestCase('succeeds')); {
      $this->suite->runTest($case);
      $this->assertEquals($this->suite, $this->invocations['testRunStarted'][0]);
      $this->assertEquals($case, $this->invocations['testStarted'][0]);
      $this->assertSubclass($this->invocations['testSucceeded'][0], 'unittest.TestSuccess');
      $this->assertEquals($this->suite, $this->invocations['testRunFinished'][0]);
      $this->assertClass($this->invocations['testRunFinished'][1], 'unittest.TestResult');
    }
  }    

  #[@test]
  public function notifiedOnFailure() {
    with ($case= new SimpleTestCase('fails')); {
      $this->suite->runTest($case);
      $this->assertEquals($this->suite, $this->invocations['testRunStarted'][0]);
      $this->assertEquals($case, $this->invocations['testStarted'][0]);
      $this->assertSubclass($this->invocations['testFailed'][0], 'unittest.TestFailure');
      $this->assertEquals($this->suite, $this->invocations['testRunFinished'][0]);
      $this->assertClass($this->invocations['testRunFinished'][1], 'unittest.TestResult');
    }
  }    

  #[@test]
  public function notifiedOnException() {
    with ($case= new SimpleTestCase('throws')); {
      $this->suite->runTest($case);
      $this->assertEquals($this->suite, $this->invocations['testRunStarted'][0]);
      $this->assertEquals($case, $this->invocations['testStarted'][0]);
      $this->assertSubclass($this->invocations['testError'][0], 'unittest.TestFailure');
      $this->assertEquals($this->suite, $this->invocations['testRunFinished'][0]);
      $this->assertClass($this->invocations['testRunFinished'][1], 'unittest.TestResult');
    }
  }    

  #[@test]
  public function notifiedOnError() {
    with ($case= new SimpleTestCase('raisesAnError')); {
      $this->suite->runTest($case);
      $this->assertEquals($this->suite, $this->invocations['testRunStarted'][0]);
      $this->assertEquals($case, $this->invocations['testStarted'][0]);
      $this->assertSubclass($this->invocations['testWarning'][0], 'unittest.TestFailure');
      $this->assertEquals($this->suite, $this->invocations['testRunFinished'][0]);
      $this->assertClass($this->invocations['testRunFinished'][1], 'unittest.TestResult');
    }
  }    

  #[@test]
  public function notifiedOnSkipped() {
    with ($case= new SimpleTestCase('skipped')); {
      $this->suite->runTest($case);
      $this->assertEquals($this->suite, $this->invocations['testRunStarted'][0]);
      $this->assertEquals($case, $this->invocations['testStarted'][0]);
      $this->assertSubclass($this->invocations['testSkipped'][0], 'unittest.TestSkipped');
      $this->assertEquals($this->suite, $this->invocations['testRunFinished'][0]);
      $this->assertClass($this->invocations['testRunFinished'][1], 'unittest.TestResult');
    }
  }    

  #[@test]
  public function notifiedOnIgnored() {
    with ($case= new SimpleTestCase('ignored')); {
      $this->suite->runTest($case);
      $this->assertEquals($this->suite, $this->invocations['testRunStarted'][0]);
      $this->assertEquals($case, $this->invocations['testStarted'][0]);
      $this->assertSubclass($this->invocations['testNotRun'][0], 'unittest.TestSkipped');
      $this->assertEquals($this->suite, $this->invocations['testRunFinished'][0]);
      $this->assertClass($this->invocations['testRunFinished'][1], 'unittest.TestResult');
    }
  }    
}
