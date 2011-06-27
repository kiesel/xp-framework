<?php

  /* This class is part of the XP framework
   *
   * $Id$
   */

  uses(
    'unittest.TestCase',
    'lang.AnnotationsParser'
  );

  /**
   * TestCase
   *
   * @see       ...
   * @purpose   TestCase for
   */
  class AnnotationsParserTest extends TestCase {

    /**
     * Test
     *
     */
    #[@test]
    public function simpleAnnotation() {
      $parser= new AnnotationsParser();
      $lexer= new AnnotationsLexer('@arg');

      $this->assertEquals(
        array('arg' => TRUE),
        $parser->parse($lexer)
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function twoAnnotations() {
      $parser= new AnnotationsParser();
      $lexer= new AnnotationsLexer('@one, @two');

      $this->assertEquals(
        array('one' => TRUE, 'two' => TRUE),
        $parser->parse($lexer)
      );
    }
  }
?>
