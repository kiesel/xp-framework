<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicates the response was unexpected
   *
   * @see      xp://peer.http.HttpUtil
   * @purpose  Exception
   */
  class UnexpectedResponseException extends Exception {
    var
      $code = 0;

    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     * @param   int code
     */
    function __construct($message, $code= 0) {
      parent::__construct($message);
      $this->code= $code;
    }

    /**
     * Set Code
     *
     * @access  public
     * @param   int code
     */
    function setCode($code) {
      $this->code= $code;
    }

    /**
     * Get Code
     *
     * @access  public
     * @return  int
     */
    function getCode() {
      return $this->code;
    }
    
    /**
     * Returns string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $s= sprintf(
        "Exception %s (code %d: %s)\n",
        $this->getClassName(),
        $this->code,
        $this->message
      );
      for ($i= 0, $t= sizeof($this->trace); $i < $t; $i++) {
        $s.= $this->trace[$i]->toString();
      }
      return $s;
    }

  }
?>
