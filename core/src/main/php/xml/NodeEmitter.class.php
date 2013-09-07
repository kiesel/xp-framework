<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  abstract class NodeEmitter extends Object {

    public abstract function emit(Node $node, $encoding, $inset= '');

    protected function encode($string, $encoding) {
      if (xp::ENCODING != $encoding) {
        return iconv(xp::ENCODING, $encoding, $string);
      }

      return $string;
    }

    protected function emitContent(Node $node, $encoding) {
      if ('string' == ($type= gettype($node->content))) {
        return $this->encode(htmlspecialchars($node->content, ENT_COMPAT, xp::ENCODING), $encoding);
      } else if ('float' == $type) {
        return ($node->content - floor($node->content) == 0)
          ? number_format($node->content, 0, NULL, NULL)
          : $node->content
        ;
      } else if ($node->content instanceof PCData) {
        return $this->encode($node->content->pcdata, $encoding);
      } else if ($node->content instanceof CData) {
        return '<![CDATA['.str_replace(']]>', ']]]]><![CDATA[>', $this->encode($node->content->cdata, $encoding)).']]>';
      } else if ($node->content instanceof String) {
        return htmlspecialchars($node->content->getBytes($encoding), ENT_COMPAT, $encoding);
      } else {
        return $node->content;
      }
    }


  }
?>