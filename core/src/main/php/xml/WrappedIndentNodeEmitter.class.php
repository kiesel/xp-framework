<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  class WrappedIndentNodeEmitter extends Object {
    public function emit(Node $node, $encoding, $inset= '') {
      $xml= $inset.'<'.$node->getName();
      $conv= xp::ENCODING != $encoding;
      
      if ('string' == ($type= gettype($node->content))) {
        $content= $conv
          ? iconv(xp::ENCODING, $encoding, htmlspecialchars($node->content, ENT_COMPAT, xp::ENCODING))
          : htmlspecialchars($node->content, ENT_COMPAT, xp::ENCODING)
        ;
      } else if ('float' == $type) {
        $content= ($node->content - floor($node->content) == 0)
          ? number_format($node->content, 0, NULL, NULL)
          : $node->content
        ;
      } else if ($node->content instanceof PCData) {
        $content= $conv
          ? iconv(xp::ENCODING, $encoding, $node->content->pcdata)
          : $node->content->pcdata
        ;
      } else if ($node->content instanceof CData) {
        $content= '<![CDATA['.str_replace(']]>', ']]]]><![CDATA[>', $conv
          ? iconv(xp::ENCODING, $encoding, $node->content->cdata)
          : $node->content->cdata
        ).']]>';
      } else if ($node->content instanceof String) {
        $content= htmlspecialchars($node->content->getBytes($encoding), ENT_COMPAT, $encoding);
      } else {
        $content= $node->content; 
      }
      
      if ($node->attribute) {
        $sep= (sizeof($node->attribute) < 3) ? '' : "\n".$inset;
        foreach ($node->attribute as $key => $value) {
          $xml.= $sep.' '.$key.'="'.htmlspecialchars(
            $conv ? iconv(xp::ENCODING, $encoding, $value) : $value,
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