<?php

  uses(
    'xml.TreeWriter'
  );

  class NoIndentTreeWriter extends TreeWriter {
    public function write(Tree $tree) {
      $this->out->write('<?xml version="1.0" encoding="'.$tree->getEncoding().'"?>'."\n");
      $this->writeNode($tree->root);
    }

    protected function writeElement(Element $element) {
      if ($element instanceof Node) return $this->writeNode($element);
      if ($element instanceof Text) return $this->writeText($element);
      if ($element instanceof CData) return $this->writeCData($element);
      if ($element instanceof Fragment) return $this->writeFragment($element);
      if ($element instanceof Comment) return $this->writeComment($comment);

      throw new IllegalStateException('Encountered unexpeced element of type '.xp::typeOf($element));
    }

    public function writeNode(Node $node) {
      $this->out->write('<'.$node->getName());

      if (sizeof($node->attribute) > 0) {
        foreach ($node->attribute as $attr => $value) {
          $this->out->write(' '.$attr.'="'.$value.'"');
        }
      }

      if (0 == sizeof($node->children)) {
        $this->out->write('/>');
        return;
      }

      $this->out->write('>');
      foreach ($node->children as $child) { $this->writeElement($child); }
      $this->out->write('</'.$node->getName().'>');
    }

    public function writeText(Text $text) {
      $this->out->write(htmlspecialchars(trim($text->getContent())));
    }
  }

?>