<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'rdbms.ConnectionNotRegisteredException',
    'rdbms.DriverManager'
  );

  /**
   * ConnectionManager
   *
   * @purpose  Hold connections to databases
   */
  class ConnectionManager extends Object {
    protected static $instance= NULL;
    public
      $pool= array();
    
    /**
     * Return the ConnectionManager's instance
     * 
     * @model   static
     * @access  public
     * @return  &rdbms.ConnectionManager
     */
    public static function getInstance() {
      if (!self::$instance) self::$instance= new ConnectionManager();
      return self::$instance;
    }
    
    /**
     * Configure this ConnectionManager
     *
     * A sample configuration file:
     * <pre>
     * [caffeine]
     * dsn="sybase://news:enieffac@gurke/CAFFEINE?autoconnect=1"
     *
     * [caffeine.dbo]
     * dsn="sybase://timm:binford@gurke/CAFFEINE?autoconnect=1"
     * </pre>
     *
     * @access  public
     * @param   &util.Properties properties
     * @return  bool
     * @throws  rdbms.DriverNotSupportedException
     */
    public function configure(Properties $properties) {
      $section= $properties->getFirstSection();
      do {
        try {
          $conn= DriverManager::getConnection($properties->readString($section, 'dsn'));
        } catch (DriverNotSupportedException $e) {
          throw ($e);
        }

        if (FALSE !== ($p= strpos($section, '.'))) {
          self::register($conn, substr($section, 0, $p), substr($section, $p+ 1));
        } else {
          self::register($conn, $section);
        }
        
      } while ($section= $properties->getNextSection());

      return TRUE;
    }
    
    /**
     * Retrieves all registered connections as an array of DBConnection
     * objects.
     *
     * @access  public
     * @return  rdbms.DBConnection[]
     */
    public function getConnections() {
      return array_values($this->pool);
    }
    
    /**
     * Register a connection
     *
     * @param   &rdbms.DBConnection conn A connection object
     * @return  &rdbms.DBConnection The connection object registered
     * @param   string hostAlias default NULL
     * @param   string userAlias default NULL
     */
    public function register(DBConnection $conn, $hostAlias= NULL, $userAlias= NULL) {
      $host= (NULL == $hostAlias) ? $conn->dsn->getHost() : $hostAlias;
      $user= (NULL == $userAlias) ? $conn->dsn->getUser() : $userAlias;
      
      if (!isset($this->pool[$user.'@'.$host])) {
        $this->pool[$user.'@'.$host]= $conn;
      }
      
      return $conn;
    }
    
    /**
     * Return a database connection object by host and user
     *
     * @param   string host
     * @param   string user
     * @return  &rdbms.DBConnection
     * @throws  rdbms.ConnectionNotRegisteredException in case there's no connection for these names
     */
    public function get($host, $user) {
      if (!isset($this->pool[$user.'@'.$host])) {
        throw (new ConnectionNotRegisteredException(
          'No connections registered for '.$user.'@'.$host
        ));
      }
      return $this->pool[$user.'@'.$host];
    }
    
    /**
     * Return one or more connections by host
     *
     * @param   string hostName
     * @param   int num default -1 offset, -1 for all
     * @return  &rdbms.DBConnection
     * @throws  rdbms.ConnectionNotRegisteredException in case there's no connection for these names
     */
    public function getByHost($hostName, $num= -1) {
      $results= array();
      foreach (array_keys($this->pool) as $id) {
        list ($user, $host)= explode('@', $id);
        if ($hostName == $host) $results[]= $this->pool[$id];
      }
      if (sizeof($results) < 1) {
        throw (new ConnectionNotRegisteredException(
          'No connections registered for '.$hostName
        ));
      }
      
      if ($num < 0) {
        return $results;
      }
      return $results[$num];
    }
  }
?>
