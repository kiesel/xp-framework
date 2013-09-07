<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  uses('xml.NodeEmitter');

  class NoIndentNodeEmitter extends NodeEmitter {
    public function emit(Node $node, $inset= '') {
      $xml= $inset.'<'.$node->getName();

      $content= $this->emitContent($node);

      foreach ($node->attribute as $key => $value) {
        $xml.= ' '.$key.'="'.htmlspecialchars(
          $this->encode($value),
          ENT_COMPAT,
          xp::ENCODING
        ).'"';
      }
      $xml.= '>'.$content;
      foreach ($node->children as $child) {
        $xml.= $this->emit($child, $inset);
      }
      return $xml.'</'.$node->name.'>';
    }
  }
?>