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
    private $targetEncoding= 'iso-8859-1';

    public function setTreeClass(XPClass $c) {
      $this->treeClass= $c->getSimpleName();
    }

    public function setNodeClass(XPClass $c) {
      $this->nodeClass= $c->getSimpleName();
    }

    private function _node($name, $attrs) {
      return new $this->nodeClass($name, NULL, $attrs);
    }

    public function setTargetEncoding($e) {
      $this->targetEncoding= $e;
    }

    public function getTargetEncoding() {
      return $this->targetEncoding;
    }

    public function withTargetEncoding($e) {
      $this->setTargetEncoding($e);
      return $this;
    }

    public function parse($text) {
      $xml= new XMLParser($this->targetEncoding);
      $xml->withCallback($this)->parse($text);

      $tree= create(new $this->treeClass())
        ->withRoot($this->root)
        ->withEncoding($xml->getEncoding());

      $this->root= NULL;
      return $tree;
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
      if ('' != trim($this->cdata)) {
        $this->stack[sizeof($this->stack)- 1]->addChild(new Text($this->cdata));
      }
      $this->cdata= '';
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
      $this->cdata.= $cdata;
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
  }
?>