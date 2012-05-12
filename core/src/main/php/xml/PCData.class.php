<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.Element');

  /**
   * PCData allows to insert literal XML into a nodes contents.
   *
   * Example:
   * <code>
   *   $tree= new Tree();
   *   $tree->addChild(new Node('data', new PCData('Hello<br/>World')));
   * </code>
   *
   * The output will then be:
   * <pre>
   *   <document>
   *     <data>Hello<br/>World</data>
   *   </document>
   * </pre>
   *
   * Note: The XML passed to PCDatas constructor is not validated!
   * Passing incorrect XML to this class will result in a not-
   * wellformed output document.
   *
   * @purpose  Wrapper
   */
  class PCData extends Object implements Element {
    public $pcdata= '';
      
    /**
     * Constructor
     *
     * @param   string pcdata
     */
    public function __construct($pcdata) {
      $this->pcdata= $pcdata;
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
      return $this->pcdata;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.$this->pcdata.')';
    }
  }
?>
