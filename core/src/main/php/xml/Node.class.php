<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  uses(
    'xml.PCData',
    'xml.CData',
    'xml.Element',
    'xml.Text',
    // 'xml.Comment',
    'xml.Element',
    'xml.XMLFormatException'
  );
  
  define('INDENT_DEFAULT',    0);
  define('INDENT_WRAPPED',    1);
  define('INDENT_NONE',       2);

  define('XML_ILLEGAL_CHARS',   "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x0b\x0c\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f");

  /**
   * Represents a node
   *
   * @see   xp://xml.Tree#addChild
   * @test  xp://net.xp_framework.unittest.xml.NodeTest
   */
  class Node extends Object implements Element {
    const
      XML_ILLEGAL_CHARS   = XML_ILLEGAL_CHARS;

    public 
      $name         = '',
      $attribute    = array(),
      $content      = NULL,  // TODO: Remove obsolete member
      $children     = array();

    /**
     * Constructor
     *
     * <code>
     *   $n= new Node('document');
     *   $n= new Node('text', 'Hello World');
     *   $n= new Node('article', '', array('id' => 42));
     * </code>
     *
     * @param   string name
     * @param   string content default NULL
     * @param   [:string] attribute default array() attributes
     * @throws  lang.IllegalArgumentException
     */
    public function __construct($name, $content= NULL, $attribute= array()) {
      $this->name= $name;
      $this->attribute= $attribute;
      $this->setContent($content);
    }

    /**
     * Create a node from an array
     *
     * Usage example:
     * <code>
     *   $n= Node::fromArray($array, 'elements');
     * </code>
     *
     * @param   array arr
     * @param   string name default 'array'
     * @return  xml.Node
     */
    public static function fromArray($a, $name= 'array') {
      $n= new self($name);
      $sname= rtrim($name, 's');
      foreach (array_keys($a) as $field) {
        $nname= is_numeric($field) || '' == $field ? $sname : $field;
        if (is_array($a[$field])) {
          $n->addChild(self::fromArray($a[$field], $nname));
        } else if ($a[$field] instanceof String) {
          $n->addChild(new self($nname, $a[$field]));
        } else if (is_object($a[$field])) {
          $n->addChild(self::fromObject($a[$field], $nname));
        } else {
          $n->addChild(new self($nname, $a[$field]));
        }
      }
      return $n;  
    }
    
    /**
     * Create a node from an object. Will use class name as node name
     * if the optional argument name is omitted.
     *
     * Usage example:
     * <code>
     *   $n= Node::fromObject($object);
     * </code>
     *
     * @param   lang.Generic obj
     * @param   string name default NULL
     * @return  xml.Node
     */
    public static function fromObject($obj, $name= NULL) {
      if (!method_exists($obj, '__sleep')) {
        $vars= get_object_vars($obj);
      } else {
        $vars= array();
        foreach ($obj->__sleep() as $var) $vars[$var]= $obj->{$var};
      }

      if (NULL !== $name) return self::fromArray($vars, $name);

      $class= get_class($obj);
      return self::fromArray($vars, (FALSE !== ($p= strrpos($class, '::'))) ? substr($class, $p+ 2): $class);
    }

    /**
     * Set Name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Set content
     *
     * @param   string content
     * @throws  xml.XMLFormatException in case content contains illegal characters
     */
    public function setContent($content) {
      if (NULL === $content) return;

      // Append new text child
      if (is_scalar($content)) {
        if (0 == strlen($content)) return;
        $this->children[]= new Text($content);
      } else if ($content instanceof String) {
        if (0 == $content->length()) return;
        $this->children[]= new Text($content);
      } else if ($content instanceof CData) {
        $this->children[]= $content;
      } else if ($content instanceof PCData) {
        $this->children[]= $content;
      }
    }
    
    /**
     * Get content (all CDATA)
     *
     * @return  string content
     */
    public function getContent() {
      $content= '';

      foreach ($this->children as $child) {
        if (!$child instanceof Text) continue;

        $content.= $child->getContent();
      }

      return $content;
    }

    /**
     * Set an attribute
     *
     * @param   string name
     * @param   string value
     */
    public function setAttribute($name, $value) {
      $this->attribute[$name]= $value;
    }
    
    /**
     * Retrieve an attribute by its name. Returns the default value if the
     * attribute is non-existant
     *
     * @param   string name
     * @param   var default default NULL
     * @return  string
     */
    public function getAttribute($name, $default= NULL) {
      return isset($this->attribute[$name]) ? $this->attribute[$name] : $default;
    }

    /**
     * Checks whether a specific attribute is existant
     *
     * @param   string name
     * @return  bool
     */
    public function hasAttribute($name) {
      return isset($this->attribute[$name]);
    }
    
    /**
     * Retrieve XML representation
     *
     * Setting indent to 0 (INDENT_DEFAULT) yields this result:
     * <pre>
     *   <item>  
     *     <title>Website created</title>
     *     <link/>
     *     <description>The first version of the XP web site is online</description>
     *     <dc:date>2002-12-27T13:10:00</dc:date>
     *   </item>
     * </pre>
     *
     * Setting indent to 1 (INDENT_WRAPPED) yields this result:
     * <pre>
     *   <item>
     *     <title>
     *       Website created
     *     </title>
     *     <link/>
     *     <description>
     *       The first version of the XP web site is online
     *     </description>
     *     <dc:date>
     *       2002-12-27T13:10:00
     *     </dc:date>  
     *   </item>
     * </pre>
     *
     * Setting indent to 2 (INDENT_NONE) yields this result (wrapped for readability,
     * returned XML is on one line):
     * <pre>
     *   <item><title>Website created</title><link></link><description>The 
     *   first version of the XP web site is online</description><dc:date>
     *   2002-12-27T13:10:00</dc:date></item>
     * </pre>
     *
     * @param   int indent default INDENT_WRAPPED
     * @param   string encoding default 'iso-8859-1'
     * @param   string inset default ''
     * @return  string XML
     */
    public function getSource($indent= INDENT_WRAPPED, $encoding= 'iso-8859-1', $inset= '') {
      $xml= $inset.'<'.$this->name;
      $conv= 'iso-8859-1' != $encoding;
      
      if (INDENT_NONE === $indent) {
        foreach ($this->attribute as $key => $value) {
          $xml.= ' '.$key.'="'.htmlspecialchars(
            $conv ? iconv('iso-8859-1', $encoding, $value) : $value,
            ENT_COMPAT,
            'iso-8859-1'
          ).'"';
        }
        $xml.= '>';
        foreach ($this->children as $child) {
          $xml.= $child->getSource($indent, $encoding, $inset);
        }
        return $xml.'</'.$this->name.'>';
      } else {
        if ($this->attribute) {
          $sep= (sizeof($this->attribute) < 3) ? '' : "\n".$inset;
          foreach ($this->attribute as $key => $value) {
            $xml.= $sep.' '.$key.'="'.htmlspecialchars(
              $conv ? iconv('iso-8859-1', $encoding, $value) : $value,
              ENT_COMPAT,
              'iso-8859-1'
            ).'"';
          }
          $xml.= $sep;
        }

        // No content and no children => close tag
        if (!$this->children) return $xml."/>\n";
        $xml.= '>'.($indent ? "\n" : '');

        // Handle special case: only one child, child is Text => apply special
        // formatting rules (no indent, direct inlined content)
        if (1 == sizeof($this->children) /* && $this->children[0] instanceof Text */) {
          $xml.= $this->children[0]->getSource(INDENT_NONE, $encoding);
        } else {
          if ($this->children) {
            $xml.= ($indent ? '' : $inset)."\n";
            foreach ($this->children as $child) {
              $xml.= $child->getSource($indent, $encoding, $inset.'  ');
            }
            $xml= ($indent ? substr($xml, 0, -1) : $xml).$inset;
          }
        }
        return $xml.($indent ? "\n".$inset : '').'</'.$this->name.">\n";
      }
    }
    
    /**
     * Add a child node
     *
     * @param   xml.Node child
     * @return  xml.Node added child
     * @throws  lang.IllegalArgumentException in case the given argument is not a Node
     */
    public function addChild(Element $child) {
      $this->children[]= $child;
      return $child;
    }

    /**
     * Add a child node and return this node
     *
     * @param   xml.Node child
     * @return  xml.Node this
     * @throws  lang.IllegalArgumentException in case the given argument is not a Node
     */
    public function withChild(Element $child) {
      $this->addChild($child);
      return $this;
    }
    
    /**
     * Returns whether another object is equal to this node
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $this->toString() === $cmp->toString();
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $a= '';
      foreach ($this->attribute as $name => $value) {
        $a.= ' @'.$name.'= '.xp::stringOf($value);
      }
      $s= $this->getClassName().'('.$this->name.$a.') {';
      if (!$this->children) {
        $s.= NULL === $this->content ? ' ' : ' '.xp::stringOf($this->content).' ';
      } else {
        $s.= NULL === $this->content ? "\n" : "\n  ".xp::stringOf($this->content)."\n";
        foreach ($this->children as $child) {
          $s.= '  '.str_replace("\n", "\n  ", xp::stringOf($child))."\n";
        }
      }
      return $s.'}';
    }
  }
?>
