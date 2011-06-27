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

    protected function parse($annotation) {
      return create(new AnnotationsParser())->parse(new AnnotationsLexer($annotation));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function simpleAnnotation() {
      $this->assertEquals(
        array('arg' => TRUE),
        $this->parse('@arg')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function twoAnnotations() {
      $this->assertEquals(
        array('one' => TRUE, 'two' => TRUE),
        $this->parse('@one, @two')
      );
    }
  }
?>
