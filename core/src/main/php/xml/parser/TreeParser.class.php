<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses(
    'xml.Tree',
    'xml.Node',
    'xml.parser.ParserCallback'
  );

  /**
   * Parse XML documents into Tree objects
   *
   */
  class TreeParser extends Object implements ParserCallback {
    private $stack      = NULL;
    private $root       = NULL;
    private $treeClass  = 'Tree';
    private $nodeClass  = 'Node';
    private $cdata      = '';

    public function setTreeClass(XPClass $c) {
      $this->treeClass= $c->getSimpleName();
    }

    public function setNodeClass(XPClass $c) {
      $this->nodeClass= $c->getSimpleName();
    }

    private function _tree(Node $root) {
      $t= new $this->treeClass();
      return $t->withRoot($root);
    }

    private function _node($name, $attrs) {
      return new $this->nodeClass($name, NULL, $attrs);
    }

    /**
     * Callback function for XMLParser
     *
     * @param   resource parser
     * @param   string name
     * @param   string attrs
     * @see     xp://xml.parser.XMLParser
     */
    public function onStartElement($parser, $name, $attrs) {
      $this->processCData();

      $element= $this->_node($name, $attrs);
      $this->stack[]= $element;
    }

    private function processCData() {
      if ('' != $this->cdata) {
        $this->stack[sizeof($this->stack)- 1]->addChild(new Text($this->cdata));
        $this->cdata= '';
      }
    }
   
    /**
     * Callback function for XMLParser
     *
     * @param   resource parser
     * @param   string name
     * @see     xp://xml.parser.XMLParser
     */
    public function onEndElement($parser, $name) {
      $this->processCData();

      $element= array_pop($this->stack);
      if (sizeof($this->stack) > 0) {
        // Register popped element with parent
        $this->stack[sizeof($this->stack)- 1]->addChild($element);
      } else {
        // This was the last element on stack, thus
        // the root element
        $this->root= $element;
      }
    }

    /**
     * Callback function for XMLParser
     *
     * @param   resource parser
     * @param   string cdata
     * @see     xp://xml.parser.XMLParser
     */
    public function onCData($parser, $cdata) {
      if (strlen(trim($cdata))) {
        $this->cdata.= $cdata;
      }
    }

    /**
     * Callback function for XMLParser
     *
     * @param   resource parser
     * @param   string data
     * @see     xp://xml.parser.XMLParser
     */
    public function onDefault($parser, $data) {
      $this->processCData();

      // NOOP
      if ('<!--' == substr($data, 0, 4)) {
        $this->stack[sizeof($this->stack)- 1]->addChild(new Comment($data));
      }
    }

    /**
     * Callback function for XMLParser
     *
     * @param   xml.parser.XMLParser instance
     */
    public function onBegin($instance) {
      $this->encoding= $instance->getEncoding();
      $this->stack= array();
    }

    /**
     * Callback function for XMLParser
     *
     * @param   xml.parser.XMLParser instance
     * @param   xml.XMLFormatException exception
     */
    public function onError($instance, $exception) {
      $this->stack= NULL;
    }

    /**
     * Callback function for XMLParser
     *
     * @param   xml.parser.XMLParser instance
     */
    public function onFinish($instance) {
      $this->stack= NULL;
    }

    public function getTree() {
      $tree= $this->_tree($this->root);
      $this->root= NULL;
      return $tree;
    }
  }
?>