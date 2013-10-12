<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.io.NodeEmitter');

  /**
   * Non-indenting emitter
   *
   * <pre>
   *   <item><title>Website created</title><link></link><description>The 
   *   first version of the XP web site is online</description><dc:date>
   *   2002-12-27T13:10:00</dc:date></item>
   * </pre>
   *
   * @see  xp://xml.io.NodeEmitter
   */
  class NoIndentNodeEmitter extends NodeEmitter {

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

      $content= $this->contentOf($node);

      foreach ($node->attribute as $key => $value) {
        $xml.= ' '.$key.'="'.htmlspecialchars($encode($value), ENT_COMPAT, $this->encoding).'"';
      }
      $xml.= '>'.$content;
      foreach ($node->children as $child) {
        $xml.= $this->emitNode($child, $inset);
      }
      return $xml.'</'.$node->name.'>';
    }
  }
?>