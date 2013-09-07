<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  abstract class NodeEmitter extends Object {
    protected $encoding= NULL;

    public function __construct($encoding) {
      $this->encoding= $encoding;
    }

    protected function encode($string) {
      if (xp::ENCODING != $this->encoding) {
        return iconv(xp::ENCODING, $this->encoding, $string);
      }

      return $string;
    }

    protected function emitContent(Node $node) {
      if ('string' == ($type= gettype($node->content))) {
        return $this->encode(htmlspecialchars($node->content, ENT_COMPAT, xp::ENCODING), $this->encoding);
      } else if ('float' == $type) {
        return ($node->content - floor($node->content) == 0)
          ? number_format($node->content, 0, NULL, NULL)
          : $node->content
        ;
      } else if ($node->content instanceof PCData) {
        return $this->encode($node->content->pcdata, $this->encoding);
      } else if ($node->content instanceof CData) {
        return '<![CDATA['.str_replace(']]>', ']]]]><![CDATA[>', $this->encode($node->content->cdata, $this->encoding)).']]>';
      } else if ($node->content instanceof String) {
        return htmlspecialchars($node->content->getBytes($this->encoding), ENT_COMPAT, $this->encoding);
      } else {
        return $node->content;
      }
    }

    public abstract function emit(Node $node, $inset= '');
  }
?>