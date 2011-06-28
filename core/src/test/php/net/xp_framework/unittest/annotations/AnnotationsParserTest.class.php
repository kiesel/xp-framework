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
   * AnnotationsParserTest
   *
   * @see       xp://lang.AnnotationsParser
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
        array('arg' => NULL),
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
        array('one' => NULL, 'two' => NULL),
        $this->parse('@one, @two')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function multilineAnnotation() {
      $this->assertEquals(
        array('one' => NULL, 'two' => NULL),
        $this->parse("@one , \n@two")
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function annotationWithValue() {
      $this->assertEquals(
        array('key' => 'value'),
        $this->parse("@key('value')")
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function annotationWithStringValue() {
      $this->assertEquals(
        array('key' => 'This class\' annotation contains a \\'),
        $this->parse("@key('This class\' annotation contains a \\\\')")
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function annotationWithKeyValue() {
      $this->assertEquals(
        array('key' => array('key' => 'value')),
        $this->parse("@key(key= 'value')")
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function annotationWithMultipleKeyValue() {
      $this->assertEquals(
        array('key' => array('inner' => 'value', 'second' => 'anotherVal')),
        $this->parse("@key(inner= 'value', second= 'anotherVal')")
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function integerParameter() {
      $this->assertEquals(
        array('annotation' => array('memory' => 100)),
        $this->parse('@annotation(memory= 100)')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function floatParameter() {
      $this->assertEquals(
        array('annotation' => array('time' => 0.1)),
        $this->parse('@annotation(time= 0.1)')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function falseParameter() {
      $this->assertEquals(
        array('annotation' => array('time' => FALSE)),
        $this->parse('@annotation(time= FALSE)')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function trueParameter() {
      $this->assertEquals(
        array('annotation' => array('time' => TRUE)),
        $this->parse('@annotation(time= TRUE)')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function nullParameter() {
      $this->assertEquals(
        array('annotation' => array('time' => NULL)),
        $this->parse('@annotation(time= NULL)')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function multilineAnnotationWithArray() {
      $this->assertEquals(
        array('interceptors' => array('classes' => array(
          'net.xp_framework.unittest.core.FirstInterceptor',
          'net.xp_framework.unittest.core.SecondInterceptor'
        ))),
        $this->parse("@interceptors(classes= array(
          'net.xp_framework.unittest.core.FirstInterceptor',
          'net.xp_framework.unittest.core.SecondInterceptor',
        ))")
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function phpClassKeywordAnnotation() {
      $this->assertEquals(
        array('class' => NULL),
        $this->parse('@class')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function phpInterfaceKeywordAnnotation() {
      $this->assertEquals(
        array('interface' => NULL),
        $this->parse('@interface')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function annotationsOverwrite() {
      $this->assertEquals(
        array('one' => 'another'),
        $this->parse('@one(word),@one(another)')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function complexAnnotation() {
      $this->assertEquals(
        array('config' => array(
          'key' => 'value',
          'times' => 5,
          'disabled' => FALSE,
          'null' => NULL,
          'list' => array(1, 2)
        )),
        $this->parse("@config(key = 'value', times= 5, disabled= FALSE, null = NULL, list= array(1, 2))")
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function bla() {
      $this->assertEquals(
        NULL,
        $this->parse("@overloaded(signatures= array(
          array('string'),
          array('string', 'string')
        ))"
      ));


    }
  }
?>
