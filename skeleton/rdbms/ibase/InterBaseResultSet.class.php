<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('rdbms.ResultSet');

  /**
   * Result set
   *
   * @ext      ibase
   * @purpose  Resultset wrapper
   */
  class InterbaseResultSet extends ResultSet {
  
    /**
     * Constructor
     *
     * @param   resource handle
     */
    public function __construct($result, TimeZone $tz= NULL) {
      $fields= array();
      if (is_resource($result)) {
        for ($i= 0, $num= ibase_num_fields($result); $i < $num; $i++) {
          $field= ibase_fetch_field($result, $i);
          $fields[$field->name]= $field->type;
        }
      }
      parent::__construct($result, $fields, $tz);
    }

    /**
     * Seek
     *
     * @param   int offset
     * @return  bool success
     * @throws  rdbms.SQLException
     */
    public function seek($offset) { 
      if (!ibase_data_seek($this->handle, $offset)) {
        throw new SQLException('Cannot seek to offset '.$offset);
      }
      return TRUE;
    }
    
    /**
     * Iterator function. Returns a rowset if called without parameter,
     * the fields contents if a field is specified or FALSE to indicate
     * no more rows are available.
     *
     * @param   string field default NULL
     * @return  mixed
     */
    public function next($field= NULL) {
      if (
        !is_resource($this->handle) ||
        FALSE === ($row= ibase_fetch_assoc($this->handle))
      ) {
        return FALSE;
      }

      foreach (array_keys($row) as $key) {
        if (NULL === $row[$key] || !isset($this->fields[$key])) continue;
        if ('datetime' == $this->fields[$key]) {
          $row[$key]= Date::fromString($row[$key], $this->tz);
        }
      }
      
      if ($field) return $row[$field]; else return $row;
    }
    
    /**
     * Close resultset and free result memory
     *
     * @return  bool success
     */
    public function close() { 
      return ibase_free_result($this->handle);
    }
  }
?>