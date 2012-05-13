<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'xml.Tree',
    'xml.NoIndentTreeWriter'
  );

  /**
   * Test NoIndentTreeWriter class
   *
   */
  class NoIndentTreeWriterTest extends TestCase {

    private function outputFor($tree) {
      $mos= new MemoryOutputStream();
      create(new NoIndentTreeWriter($mos))->write($tree);
      return $mos->getBytes();
    }
    
    #[@test]
    public function simpleTree() {
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".'<document/>',
        $this->outputFor(Tree::fromString('<document/>'))
      );
    }

    #[@test]
    public function simpleTreeWithAttribute() {
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".'<document attr="value"/>',
        $this->outputFor(Tree::fromString('<document attr="value"/>'))
      );
    }

    #[@test]
    public function simpleTreeNonEmptyRoot() {
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".'<document><tag/></document>',
        $this->outputFor(Tree::fromString('<document><tag/></document>'))
      );
    }

    #[@test]
    public function simpleMultiNodeTree() {
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".'<document><tag><node/></tag></document>',
        $this->outputFor(Tree::fromString('<document><tag><node/></tag></document>'))
      );
    }

    #[@test]
    public function simpleTreeWithMultipleAttributes() {
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".'<document attr="value" second="anothervalue"/>',
        $this->outputFor(Tree::fromString('<document attr="value" second="anothervalue"/>'))
      );
    }

    #[@test]
    public function simpleTreeWithTextContent() {
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".'<document>Hello World!</document>',
        $this->outputFor(Tree::fromString('<document>Hello World!</document>'))
      );
    }

    #[@test]
    public function simpleTreeWithTextContentContainingEntity() {
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".'<document>Hello &amp; World!</document>',
        $this->outputFor(Tree::fromString('<document>Hello &amp; World!</document>'))
      );
    }

    #[@test]
    public function treeWithContentAndSubnodes() {
      $this->assertEquals(
        '<?xml version="1.0" encoding="iso-8859-1"?>'."\n".'<document>Hello <b>to the</b> World!</document>',
        $this->outputFor(Tree::fromString('<document>Hello <b>to the      </b> World!</document>'))
      );
    }
  }
?>
