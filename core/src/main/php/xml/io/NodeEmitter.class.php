<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  uses(
    'xml.Node',
    'io.streams.OutputStream'
  );
  
  abstract class NodeEmitter extends Object {
    protected $encoding= NULL;
    public $encode= NULL;

    public function __construct($encoding) {
      $this->encoding= $encoding;

      if (xp::ENCODING == $this->encoding) {
        $this->encode= function($v) { return $v; };
      } else {
        $self= $this;
        $this->encode= function($v) use ($self) {
          return iconv(xp::ENCODING, $self->encoding, $v);
        };
      }
    }

    protected function emitContent(Node $node) {
      $encode= $this->encode;
      if ('string' == ($type= gettype($node->content))) {
        return $encode(htmlspecialchars($node->content, ENT_COMPAT, xp::ENCODING), $this->encoding);
      } else if ('float' == $type) {
        return ($node->content - floor($node->content) == 0)
          ? number_format($node->content, 0, NULL, NULL)
          : $node->content
        ;
      } else if ($node->content instanceof PCData) {
        return $encode($node->content->pcdata, $this->encoding);
      } else if ($node->content instanceof CData) {
        return '<![CDATA['.str_replace(']]>', ']]]]><![CDATA[>', $encode($node->content->cdata, $this->encoding)).']]>';
      } else if ($node->content instanceof String) {
        return htmlspecialchars($node->content->getBytes($this->encoding), ENT_COMPAT, $this->encoding);
      } else {
        return $node->content;
      }
    }

    public abstract function emit(Node $node, $inset= '');

    public abstract function emitTo(OutputStream $stream, Node $node, $inset= '');
  }
?>