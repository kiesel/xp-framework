<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.io.NodeEmitter');

  /**
   * Default emitter
   *
   * <pre>
   *   <item>  
   *     <title>Website created</title>
   *     <link/>
   *     <description>The first version of the XP web site is online</description>
   *     <dc:date>2002-12-27T13:10:00</dc:date>
   *   </item>
   * </pre>
   *
   * @see  xp://xml.io.NodeEmitter
   */
  class DefaultIndentNodeEmitter extends NodeEmitter {

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
        $xml.= '>'.trim($content);
      }

      if ($node->children) {
        $xml.= $inset."\n";
        foreach ($node->children as $child) {
          $xml.= $this->emitNode($child, $inset.'  ');
        }
        $xml= $xml.$inset;
      }
      return $xml.'</'.$node->name.">\n";
    }
  }
?>