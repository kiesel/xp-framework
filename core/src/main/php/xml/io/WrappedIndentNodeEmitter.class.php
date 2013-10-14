<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.io.NodeEmitter');

  /**
   * Wrapped emitter
   *
   * <pre>
   *   <item>
   *     <title>
   *       Website created
   *     </title>
   *     <link/>
   *     <description>
   *       The first version of the XP web site is online
   *     </description>
   *     <dc:date>
   *       2002-12-27T13:10:00
   *     </dc:date>  
   *   </item>
   * </pre>
   *
   * @see  xp://xml.io.NodeEmitter
   */
  class WrappedIndentNodeEmitter extends NodeEmitter {

    /**
     * Emits a node
     *
     * @param  io.streams.OutputStream $stream
     * @param  xml.Node $node
     * @param  string $inset
     * @return string
     */
    protected function emitNode($stream, $node, $inset) {
      $encode= $this->encode;
      $name= $node->getName();

      $stream->write($inset.'<'.$node->getName());
      if ($node->attribute) {
        $sep= (sizeof($node->attribute) < 3) ? '' : "\n".$inset;
        foreach ($node->attribute as $key => $value) {
          $stream->write($sep.' '.$key.'="'.htmlspecialchars($encode($value), ENT_COMPAT, $this->encoding).'"');
        }
        $stream->write($sep);
      }

      // No content and no children => close tag
      $content= trim($this->contentOf($node));
      if (0 === strlen($content)) {
        if (!$node->children) {
          $stream->write("/>\n");
          return;
        }
        $stream->write('>');
      } else {
        $stream->write('>'."\n  ".$inset.$content);
      }

      $stream->write("\n");
      if ($node->children) {
        foreach ($node->children as $child) {
          $this->emitNode($stream, $child, $inset.'  ');
        }
      }
      $stream->write($inset.'</'.$name.">\n");
    }
  }
?>