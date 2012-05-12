<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.Element');

  /**
   * CData allows to insert a CDATA section:
   *
   * Example:
   * <code>
   *   $tree= new Tree();
   *   $tree->addChild(new Node('data', new CData('<Hello World>')));
   * </code>
   *
   * The output will then be:
   * <pre>
   *   <document>
   *     <data><![CDATA[<Hello World>]]></data>
   *   </document>
   * </pre>
   *
   * @purpose  Wrapper
   */
  class CData extends Object implements Element {
    public $cdata= '';
      
    /**
     * Constructor
     *
     * @param   string cdata
     */
    public function __construct($cdata) {
      $this->cdata= $cdata;
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
      return '<![CDATA['.$this->cdata.']]>';
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->cdata.')';
    }
  }
?>
