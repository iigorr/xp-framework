<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

  /**
   * TestCase
   *
   * @see  xp://lang.ClassLoader#declareModule
   */
  class ModuleDeclarationTest extends TestCase {

    /**
     * Assertion helper
     *
     * @param  string name The expected name
     * @param  string version The expected version
     * @param  string moduleInfo The module.xp file contents
     * @throws unittest.AssertionFailedError
     */
    protected function assertModule($name, $version, $moduleInfo) {
      ClassLoader::declareModule(newinstance('lang.IClassLoader', array($moduleInfo), '{
        protected $moduleInfo= "";
        public function __construct($moduleInfo) { $this->moduleInfo= $moduleInfo; }
        public function providesResource($name) { return TRUE; }
        public function providesClass($name) { return FALSE; }
        public function providesPackage($name) { return FALSE; }
        public function packageContents($name) { /* Not reached */ }
        public function loadClass($name) { /* Not reached */ }
        public function loadClass0($name) { /* Not reached */ }
        public function getResource($name) { return $this->moduleInfo; }
        public function getResourceAsStream($name) { /* Not reached */ }
        public function toString() { return $this->getClassName()."(`".$this->moduleInfo."`)"; }
      }'));

      try {
        $this->assertEquals($version, Module::forName($name)->getVersion());
      } catch (XPException $e) {
        //
      } ensure($e); {
        unset(xp::$registry['modules'][$name]);
        if ($e) throw($e);
      }
    }

    /**
     * Test a module with a number in its name
     *
     */
    #[@test]
    public function with_number_inside_name() {
      $this->assertModule('mp3', '2.1.0', '<?php module mp3(2.1.0) { } ?>');
    }

    /**
     * Test a module with "-" in its name
     *
     */
    #[@test]
    public function with_dash_inside_name() {
      $this->assertModule('jenkins-api', '2.1.0', '<?php module jenkins-api(2.1.0) { } ?>');
    }

    /**
     * Test a module with "_" in its name
     *
     */
    #[@test]
    public function with_underscore_inside_name() {
      $this->assertModule('com_dotnet', '2.1.0', '<?php module com_dotnet(2.1.0) { } ?>');
    }

    /**
     * Test a module with a "-" in its version
     *
     */
    #[@test]
    public function alpha_version() {
      $this->assertModule('test', '0.1.0-alpha7', '<?php module test(0.1.0-alpha7) { } ?>');
    }

    /**
     * Test a module with letters in its version
     *
     */
    #[@test]
    public function release_candidate() {
      $this->assertModule('test', '5.8.3RC4', '<?php module test(5.8.3RC4) { } ?>');
    }

    /**
     * Test a module with letters in its version
     *
     */
    #[@test]
    public function release() {
      $this->assertModule('test', '5.9.1', '<?php module test(5.9.1) { } ?>');
    }
  }
?>
