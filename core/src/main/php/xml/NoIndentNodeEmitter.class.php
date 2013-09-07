<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  class NoIndentNodeEmitter extends Object {
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
      
      foreach ($node->attribute as $key => $value) {
        $xml.= ' '.$key.'="'.htmlspecialchars(
          $conv ? iconv(xp::ENCODING, $encoding, $value) : $value,
          ENT_COMPAT,
          xp::ENCODING
        ).'"';
      }
      $xml.= '>'.$content;
      foreach ($node->children as $child) {
        $xml.= $this->emit($child, $encoding, $inset);
      }
      return $xml.'</'.$node->name.'>';
    }
  }
?>