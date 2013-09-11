<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  uses('xml.io.NodeEmitter');

  class NoIndentNodeEmitter extends NodeEmitter {
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

    public function emitTo(OutputStream $stream, Node $node, $inset= '') {
      $this->stream= $stream;
      $this->emitNode($node, $inset);
    }

    protected function emitNode(Node $node, $inset= '') {
      $encode= $this->encode;
      $this->stream->write($inset.'<'.$node->name);

      $content= $this->emitContent($node);

      foreach ($node->attribute as $key => $value) {
        $this->stream->write(' '.$key.'="'.htmlspecialchars(
          $encode($value),
          ENT_COMPAT,
          xp::ENCODING
        ).'"');
      }
      $this->stream->write('>'.$this->emitContent($node));
      foreach ($node->children as $child) {
        $this->emitNode($child, $inset);
      }
      $this->stream->write('</'.$node->name.'>');
    }
  }
?>