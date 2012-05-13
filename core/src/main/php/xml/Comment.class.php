<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  uses('xml.Element');

  /**
   * Represent comment
   *
   */
  class Comment extends Object implements Element {
    private $content= NULL;

    /**
     * Constructor.
     *
     * @param   var content
     */
    public function __construct($content) {
      $this->setComment($content);
    }

    /**
     * Set content
     *
     * @param   string comment
     */
    public function setComment($content) {
      if ('<!--' == substr($content, 0, 4) && '-->' == substr($content, -3)) {
        $content= substr($content, 4, -3);
      }

      $this->content= trim($content);
    }

    /**
     * Retrieve comment
     *
     * @return   string
     */
    public function getComment() {
      return $this->content;
    }

    /**
     * Retrieve source representation
     *
     * @param   int indent default INDENT_WRAPPED
     * @param   string encoding default "iso-8859-1"
     * @param   string inset default ""
     * @return  string
     */
    public function getSource($indent= INDENT_WRAPPED, $encoding= 'iso-8859-1', $inset= '') {
      return $inset.'<!-- '.$this->getComment()." -->\n";
    }

    /**
     * Retrieve string representation
     *
     * @return   string
     */
    public function toString() {
      return $this->getClassName().'{"'.$this->getComment().'"}';
    }
  }