<?php

  uses(
    'unittest.TestCase',
    'xml.parser.TreeParser',
    'xml.Tree',
    'xml.Node'
  );

  class TreeParserTest extends TestCase {

    private function parse($source) {
      return Tree::fromString($source);
    }

    #[@test]
    public function parseEmptyNode() {
      $this->assertEquals(
        new Node('document'), 
        $this->parse('<document/>')->root
      );
    }

    #[@test]
    public function nodeWithCData() {
      $this->assertEquals(
        new Node('document', 'Hello World'),
        $this->parse('<document>Hello World</document>')->root
      );
    }

    #[@test]
    public function whitespace_at_end_of_cdata_is_removed() {
      $this->assertEquals(
        new Node('document', 'Hello World '),
        $this->parse('<document>Hello World </document>')->root
      );
    }

    #[@test]
    public function whitespace_at_begin_of_cdata_is_removed() {
      $this->assertEquals(
        new Node('document', ' Hello World'),
        $this->parse('<document> Hello World</document>')->root
      );
    }


    #[@test]
    public function whitespace_just_before_inner_tag_is_not_removed() {
      $expect= create(new Node('document'))
        ->withChild(new Text('Hello '))
        ->withChild(new Node('b', 'World'));

      $this->assertEquals(
        $expect,
        $this->parse('<document>Hello <b>World</b></document>')->root
      );
    }

    #[@test]
    public function whitespace_along_text_is_preserved() {
      $this->assertEquals(
        new Node('document', '      .'),
        $this->parse('<document>      .</document>')->root
      );
    }

    #[@test]
    public function whitespace_content_between_elements_is_discarded() {
      $this->assertEquals(
        new Node('document'),
        $this->parse('<document>       </document>')->root
      );
    }

    #[@test]
    public function text_and_cdata_are_merged() {
      $this->assertEquals(
        new Node('document', 'Hello to the World'),
        $this->parse('<document>Hello <![CDATA[to the ]]>World</document>')->root
      );
    }

    #[@test]
    public function entities_are_replaced() {
      $this->assertEquals(
        new Node('document', 'Hello World'),
        $this->parse('<document>Hello&#32;World</document>')->root
      );
    }

    #[@test]
    public function special_characters_in_cdata_are_decoded() {
      $this->assertEquals(
        new Node('document', 'e > 0'),
        $this->parse('<document><![CDATA[e > 0]]></document>')->root
      );
    }

    #[@test]
    public function encoded_characters_are_decoded() {
      $this->assertEquals(
        new Node('document', 'e > 0'),
        $this->parse('<document>e &gt; 0</document>')->root
      );
    }

    #[@test]
    public function whitespace_between_elements_is_discarded() {
      $tree= new Tree('document');
      $tree->addChild(new Node('contents', 'Here'));

      $parsed= $this->parse('<document>
        <contents>Here</contents>
      </document>');

      $this->assertEquals(1, sizeof($parsed->root->children));
    }

    #[@test]
    public function whitespace_does_not_add_up() {
      $this->assertEquals(
        new Node('element', 'Some text...'),
        $this->parse('<document>      <element>Some text...</element></document>')->root->children[0]
      );
    }
  }
?>