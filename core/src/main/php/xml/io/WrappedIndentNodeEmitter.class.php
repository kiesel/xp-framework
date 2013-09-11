<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  uses('xml.io.NodeEmitter');

  class WrappedIndentNodeEmitter extends NodeEmitter {

    public function emit(Node $node, $inset= '') {
      $encode= $this->encode;
      $xml= $inset.'<'.$node->getName();

      $content= $this->emitContent($node);

      if ($node->attribute) {
        $sep= (sizeof($node->attribute) < 3) ? '' : "\n".$inset;
        foreach ($node->attribute as $key => $value) {
          $xml.= $sep.' '.$key.'="'.htmlspecialchars(
            $encode($value),
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
          $xml.= $this->emit($child, $inset.'  ');
        }
        $xml= substr($xml, 0, -1).$inset;
      }
      return $xml."\n".$inset.'</'.$node->name.">\n";
    }

    public function emitTo(OutputStream $stream, Node $node, $inset= '') {
      $this->stream= $stream;
      $this->emitNode($node, $inset);
    }

    
  }
?>