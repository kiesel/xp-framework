<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.log.Appender',
    'scriptlet.HttpScriptletResponse',
    'webservices.json.JsonDecoder'
  );

  /**
   * This class implements the FireBug protocol
   *
   * @see      xp://util.log.Appender
   * @purpose  FireBug HTTP protocol
   */
  class FirebugProtocol extends Object {
    protected static $instance= NULL;
    protected $buffer= array();

    /**
     * Constructor
     * 
     */
    protected function __construct() {
    }
    
    /**
     * Retrieve instance
     *
     * @return  util.log.FirebugProtocol
     */
    public function getInstance() {
      if (NULL === self::$instance) {
        self::$instance= new self();
      }

      return self::$instance;
    }

    /**
     * Check if instance exists
     *
     * @return  bool
     */
    public static function hasInstance() {
      return NULL !== self::$instance;
    }
    
    /**
     * Append data
     *
     * @param   util.log.LogLevel level
     * @param   string event
     */
    public function append($level, $event) {
      $this->buffer[]= array($level, $event);
    }

    /**
     * Clears the buffers content.
     *
     */
    public function clear() {
      $this->buffer= array();
    }

   /**
    * Write appender data to response
    *
    * @param   scriptlet.Response response
    */
    public function writeTo(Response $response) {
      $encoder= new JsonDecoder();

      $response->setHeader('X-Wf-Protocol-1', 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2');
      $response->setHeader('X-Wf-1-Plugin-1', 'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.3');
      $response->setHeader('X-Wf-1-Structure-1', 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1');

      $i= 1;
      foreach ($this->buffer as $event) {
        list($level, $line)= $event;
        
        switch ($level) {
          default: $type= LogLevel::nameOf($level); break;
          case LogLevel::DEBUG: $type= "LOG"; break;
        }

        $str= $encoder->encode(array(
          array('Type' => $type),
          $line
        ));

        $offset= 0;
        $out= strlen($str);
        while ($offset < strlen($str)) {
          $use= substr($str, $offset, 4000);

          $out.= '|'.$use.'|';
          $offset+= strlen($use);

          if ($offset < strlen($str)) $out.= '\\';

          $response->setHeader(sprintf('X-Wf-1-1-1-%d', $i++), $out);

          $out= '';
        }
      }
    }
  }
?>
