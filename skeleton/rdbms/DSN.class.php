<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * DSN
   *
   * DSN examples:
   * <pre>
   *   type://username:password@host:port/database
   * </pre>
   *
   * @purpose  Unified connect string
   */
  class DSN extends Object {
    var 
      $parts    = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string str
     */
    function __construct($str) {
      $this->parts= parse_url($str);
      $this->parts['dsn']= $str;
      parent::__construct();
    }
    
    /**
     * Retreive flags
     *
     * @access  public
     * @return  int flags
     */
    function getFlags() {
      if (!isset($this->parts['query'])) return 0;
      
      $flags= 0;
      parse_str($this->parts['query'], $config);
      foreach ($config as $key => $value) {
        if ($value) {
          $flags= $flags | constant('DB_'.strtoupper($key));
        }
      }
      return $flags;
    }
    
    /**
     * Retreive host
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string host or default if none is set
     */
    function getHost($default= NULL) {
      return isset($this->parts['host']) ? $this->parts['host'] : $default;
    }

    /**
     * Retreive database
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string databse or default if none is set
     */
    function getDatabase($default= NULL) {
      return isset($this->parts['path']) ? substr($this->parts['path'], 1) : $default;
    }

    /**
     * Retreive user
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string user or default if none is set
     */
    function getUser($default= NULL) {
      return isset($this->parts['user']) ? $this->parts['user'] : $default;
    }

    /**
     * Retreive password
     *
     * @access  public
     * @param   mixed default default NULL  
     * @return  string password or default if none is set
     */
    function getPassword($default= NULL) {
      return isset($this->parts['pass']) ? $this->parts['pass'] : $default;
    }

  }
?>
