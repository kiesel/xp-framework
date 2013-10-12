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

      // Tag, attributes and content
      echo $inset, '<', $node->getName();
      foreach ($node->attribute as $key => $value) {
        echo ' ', $key, '="', htmlspecialchars($encode($value), ENT_COMPAT, $this->encoding), '"';
      }
      echo '>', $this->contentOf($node);

      // Children
      foreach ($node->children as $child) {
        $this->emitNode($child, $inset);
      }

      // Closing tag
      echo '</', $node->getName(), '>';
    }
  }
?>