<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.Node',
    'io.streams.OutputStream'
  );
  
  abstract class NodeEmitter extends Object {
    protected $encoding= NULL;
    public $encode= NULL;

    /**
     * Creates a new node emitter
     *
     * @param string $encoding The target encoding
     */
    public function __construct($encoding) {
      $this->encoding= $encoding;

      if (xp::ENCODING === $this->encoding) {
        $this->encode= function($v) { return $v; };
      } else {
        $this->encode= function($v) use ($encoding) {
          return iconv(xp::ENCODING, $encoding, $v);
        };
      }
    }

    protected function emitContent($node) {
      $encode= $this->encode;
      if ('string' === ($type= gettype($node->content))) {
        return $encode(htmlspecialchars($node->content, ENT_COMPAT, xp::ENCODING));
      } else if ('float' === $type) {
        return ($node->content - floor($node->content) == 0)
          ? number_format($node->content, 0, NULL, NULL)
          : $node->content
        ;
      } else if ($node->content instanceof PCData) {
        return $encode($node->content->pcdata, $this->encoding);
      } else if ($node->content instanceof CData) {
        return '<![CDATA['.str_replace(']]>', ']]]]><![CDATA[>', $encode($node->content->cdata)).']]>';
      } else if ($node->content instanceof String) {
        return htmlspecialchars($node->content->getBytes($this->encoding), ENT_COMPAT, $this->encoding);
      } else {
        return $node->content;
      }
    }

    protected abstract function emitNode($node, $inset);

    /**
     * Emits a node
     *
     * @param  xml.Node $node
     * @param  string $inset
     * @return string
     */
    public function emit(Node $node, $inset= '') {
      return $this->emitNode($node, $inset);
    }

    /**
     * Emits a node to a given stream
     *
     * @param  io.streams.OutputStream $stream
     * @param  xml.Node $node
     * @param  string $inset
     */
    public function emitTo(OutputStream $stream, Node $node, $inset= '') {
      $stream->write($this->emit($node, $inset));
    }
  }
?>