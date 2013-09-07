<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  uses('xml.NodeEmitter');

  class DefaultIndentNodeEmitter extends NodeEmitter {
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
        $xml.= '>'.trim($content);
      }

      if ($node->children) {
        $xml.= $inset."\n";
        foreach ($node->children as $child) {
          $xml.= $this->emit($child, $encoding, $inset.'  ');
        }
        $xml= $xml.$inset;
      }
      return $xml.'</'.$node->name.">\n";
    }
  }
?>