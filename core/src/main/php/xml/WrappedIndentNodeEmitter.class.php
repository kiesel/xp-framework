<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  uses('xml.NodeEmitter');

  class WrappedIndentNodeEmitter extends NodeEmitter {

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

    public function emit(Node $node, $encoding, $inset= '') {
      $xml= $inset.'<'.$node->getName();

      $content= $this->emitContent($node, $encoding);

      if ($node->attribute) {
        $sep= (sizeof($node->attribute) < 3) ? '' : "\n".$inset;
        foreach ($node->attribute as $key => $value) {
          $xml.= $sep.' '.$key.'="'.htmlspecialchars(
            $this->encode($value, $encoding),
            ENT_COMPAT,
            xp::ENCODING
          ).'"';
        }
        $xml.= $sep;
      }

      // No content and no children => close tag
      if (0 == strlen($content)) {
        if (!$node->children) return $xml."/>\n";
        $xml.= '>';
      } else {
        $xml.= '>'."\n  ".$inset.$content;
      }

      if ($node->children) {
        $xml.= "\n";
        foreach ($node->children as $child) {
          $xml.= $this->emit($child, $encoding, $inset.'  ');
        }
        $xml= substr($xml, 0, -1).$inset;
      }
      return $xml."\n".$inset.'</'.$node->name.">\n";
    }
  }
?>