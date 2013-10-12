<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.io.NodeEmitter');

  class WrappedIndentNodeEmitter extends NodeEmitter {

    /**
     * Emits a node
     *
     * @param  xml.Node $node
     * @param  string $inset
     * @return string
     */
    protected function emitNode($node, $inset) {
      $encode= $this->encode;
      $xml= $inset.'<'.$node->getName();

      $content= $this->emitContent($node);

      if ($node->attribute) {
        $sep= (sizeof($node->attribute) < 3) ? '' : "\n".$inset;
        foreach ($node->attribute as $key => $value) {
          $xml.= $sep.' '.$key.'="'.htmlspecialchars($encode($value), ENT_COMPAT, $this->encoding).'"';
        }
        $xml.= $sep;
      }

      // No content and no children => close tag
      if (0 === strlen($content)) {
        if (!$node->children) return $xml."/>\n";
        $xml.= '>';
      } else {
        $xml.= '>'."\n  ".$inset.$content;
      }

      if ($node->children) {
        $xml.= "\n";
        foreach ($node->children as $child) {
          $xml.= $this->emitNode($child, $inset.'  ');
        }
        $xml= substr($xml, 0, -1).$inset;
      }
      return $xml."\n".$inset.'</'.$node->name.">\n";
    }
  }
?>