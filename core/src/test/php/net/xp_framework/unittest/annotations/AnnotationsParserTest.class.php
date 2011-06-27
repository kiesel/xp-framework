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
    public function parseSimpleAnnotation() {
      $parser= new AnnotationsParser();
      $lexer= new AnnotationsLexer('@arg');

      $this->assertEquals(
        array('arg' => TRUE),
        $parser->parse($lexer)
      );

    }
  }
?>
