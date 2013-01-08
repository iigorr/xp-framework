<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'io.File',
    'util.Properties',
    'util.PropertySource',
    'lang.ResourceProvider'
  );

  /**
   * Resource-based property source
   *
   * To read properties from the following resource: /path/to/inidirectory/test.ini provided by
   * the default class loader:
   * <code>
   *   $src= new ResourcePropertySource('res://path/to/inidirectory');
   *   $properties= $src->fetch('test');
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.util.ResourcePropertySourceTest
   */
  class ResourcePropertySource extends Object implements PropertySource {
    protected $cache = array();
    protected $root  = NULL;

    /**
     * Constructor
     *
     * @param   string path
     */
    public function __construct($path) {
      $this->root= rtrim($path, '/').'/';
    }

    /**
     * Check whether source provides given properties
     *
     * @param   string name
     * @return  bool
     */
    public function provides($name) {
      if (isset($this->cache[$name])) return TRUE;
      return (FALSE !== ResourceProvider::getInstance()->url_stat($this->root.$name.'.ini', 0));
    }

    /**
     * Load properties by given name
     *
     * @param   string name
     * @return  util.Properties
     * @throws  lang.IllegalArgumentException if property requested is not available
     */
    public function fetch($name) {
      if (!$this->provides($name))
        throw new IllegalArgumentException('No properties '.$name.' found at '.$this->root);

      if (!isset($this->cache[$name])) {
        $this->cache[$name]= Properties::fromFile(new File($this->root.$name.'.ini'));
      }

      return $this->cache[$name];
    }

    /**
     * Returns hashcode for this source
     *
     * @return  string
     */
    public function hashCode() {
      return md5($this->root);
    }

    /**
     * Check if this instance equals another
     *
     * @param   Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->root === $this->root;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<'.$this->root.'>';
    }
  }
?>
