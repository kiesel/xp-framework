<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a JNLP resource
   *
   * @see      xp://com.sun.webstart.JnlpDocument
   * @purpose  Abstract base class
   */
  class JnlpResource extends Object {
  
    /**
     * Get name
     *
     * @model   abstract
     * @access  public
     * @return  string
     */
    public abstract function getTagName();

    /**
     * Get attributes
     *
     * @model   abstract
     * @access  public
     * @return  array
     */
    public abstract function getTagAttributes();
  }
?>
