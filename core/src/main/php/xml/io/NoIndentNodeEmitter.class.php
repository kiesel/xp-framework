<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.io.NodeEmitter');

  class NoIndentNodeEmitter extends NodeEmitter {

    /**
     * Emits a node
     *
     * @param  xml.Node $node
     * @param  string $inset
     * @return string
     */
    public function emit(Node $node, $inset= '') {
      $encode= $this->encode;
      $xml= $inset.'<'.$node->getName();

      $content= $this->emitContent($node);

      foreach ($node->attribute as $key => $value) {
        $xml.= ' '.$key.'="'.htmlspecialchars(
          $encode($value),
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