<?php namespace net\xp_framework\unittest\core;

/**
 * Tests the xp::stringOf() core utility
 *
 * @see     xp://net.xp_framework.unittest.core.NullTest
 * @see   https://github.com/xp-framework/xp-framework/issues/325
 */
class StringOfTest extends \unittest\TestCase {

  /**
   * Returns a class with a toString() method that always returns the following:
   * <pre>
   *   TestString(6) { String }
   * </pre>
   *
   * @return  lang.Object
   */
  protected function testStringInstance() {
    return newinstance('lang.Object', array(), '{
      function toString() {
        return "TestString(6) { String }";
      }
    }');
  }

  #[@test]
  public function stringArgument() {
    $this->assertEquals('"Hello"', \xp::stringOf('Hello'));
  }

  #[@test]
  public function booleanArgument() {
    $this->assertEquals('true', \xp::stringOf(true));
    $this->assertEquals('false', \xp::stringOf(false));
  }

  #[@test]
  public function nullArgument() {
    $this->assertEquals('null', \xp::stringOf(null));
  }

  #[@test]
  public function xpNullArgument() {
    $this->assertEquals('<null>', \xp::stringOf(\xp::null()));
  }

  #[@test]
  public function numericArgument() {
    $this->assertEquals('1', \xp::stringOf(1));
    $this->assertEquals('-1', \xp::stringOf(-1));
    $this->assertEquals('1.5', \xp::stringOf(1.5));
    $this->assertEquals('-1.5', \xp::stringOf(-1.5));
  }

  #[@test]
  public function objectArgument() {
    $this->assertEquals('TestString(6) { String }', \xp::stringOf($this->testStringInstance()));
  }

  #[@test]
  public function simpleArrayArgument() {
    $this->assertEquals(
      "[\n  0 => 1\n  1 => 2\n  2 => 3\n]", 
      \xp::stringOf(array(1, 2, 3))
    );
  }

  #[@test]
  public function arrayOfArraysArgument() {
    $this->assertEquals(
      "[\n  0 => [\n    0 => 1\n    1 => 2\n    2 => 3\n  ]\n]", 
      \xp::stringOf(array(array(1, 2, 3)))
    );
  }

  #[@test]
  public function hashmapArgument() {
    $this->assertEquals(
      "[\n  foo => \"bar\"\n  bar => 2\n  baz => TestString(6) { String }\n]", 
      \xp::stringOf(array(
        'foo' => 'bar', 
        'bar' => 2, 
        'baz' => $this->testStringInstance()
      ))
    );
  }

  #[@test]
  public function builtinObjectsArgument() {
    $this->assertEquals("php.stdClass {\n}", \xp::stringOf(new \stdClass()));
    $this->assertEquals("php.Directory {\n}", \xp::stringOf(new \Directory('.')));
  }

  #[@test]
  public function resourceArgument() {
    $fd= fopen('php://stdin', 'r');
    $this->assertTrue((bool)preg_match('/resource\(type= stream, id= [0-9]+\)/', \xp::stringOf($fd)));
    fclose($fd);
  }

  #[@test]
  public function arrayRecursion() {
    $a= array();
    $a[0]= 'Outer array';
    $a[1]= array();
    $a[1][0]= 'Inner array';
    $a[1][1]= &$a;
    $this->assertEquals('[
  0 => "Outer array"
  1 => [
    0 => "Inner array"
    1 => ->{:recursion:}
  ]
]', 
    \xp::stringOf($a));
  }

  #[@test]
  public function objectRecursion() {
    $o= new \stdClass();
    $o->child= new \stdClass();
    $o->child->parent= $o;
    $this->assertEquals('php.stdClass {
  child => php.stdClass {
    parent => ->{:recursion:}
  }
}',
    \xp::stringOf($o));
  }

  #[@test]
  public function noRecursion() {
    $test= newinstance('lang.Object', array(), '{
      public function toString() {
        return "Test";
      }
    }');
    $this->assertEquals(
      "[\n  a => Test\n  b => Test\n]", 
      \xp::stringOf(array(
        'a' => $test,
        'b' => $test
      ))
    );
  }
  
  #[@test]
  public function noRecursionWithLargeNumbers() {
    $test= newinstance('lang.Object', array(), '{
      public function hashCode() {
        return 9E100;
      }
      
      public function toString() {
        return "Test";
      }
    }');
    $this->assertEquals(
      "[\n  a => Test\n  b => Test\n]", 
      \xp::stringOf(array(
        'a' => $test,
        'b' => $test
      ))
    );
  }

  /**
   * Tests toString() isn't invoked recursively by sourcecode such as:
   *
   * ```php
   * class MaliciousRecursionGenerator extends Object {
   *   function toString() {
   *     return xp::stringOf($this);
   *   }
   * }
   *
   * echo xp::stringOf(new MaliciousRecursionGenerator());
   * ```
   */
  #[@test]
  public function toStringRecursion() {
    $test= newinstance('lang.Object', array(), '{
      public function toString() {
        return xp::stringOf($this);
      }
    }');
    $this->assertEquals(
      $test->getClassName()." {\n  __id => \"".$test->hashCode()."\"\n}",
      \xp::stringOf($test)
    );
  }
  
  #[@test]
  public function repeatedCalls() {
    $object= new \lang\Object();
    $stringRep= $object->toString();
    
    $this->assertEquals($stringRep, \xp::stringOf($object), 'first');
    $this->assertEquals($stringRep, \xp::stringOf($object), 'second');
  }

  #[@test]
  public function indenting() {
    $cl= \lang\ClassLoader::defineClass('net.xp_framework.unittest.core.StringOfTest_IndentingFixture', 'lang.Object', array(), '{
      protected $inner= NULL;
      public function __construct($inner) {
        $this->inner= $inner;
      }
      public function toString() {
        return "object {\n  ".xp::stringOf($this->inner, "  ")."\n}";
      }
    }');
    $this->assertEquals(
      "object {\n  object {\n    null\n  }\n}",
      $cl->newInstance($cl->newInstance(NULL))->toString()
    );
  }

  #[@test]
  public function closure() {
    $this->assertEquals('<function()>', \xp::stringOf(function() { }));
  }

  #[@test]
  public function closureWithOneParam() {
    $this->assertEquals('<function($a)>', \xp::stringOf(function($a) { }));
  }

  #[@test]
  public function closureWithTwoParams() {
    $this->assertEquals('<function($a, $b)>', \xp::stringOf(function($a, $b) { }));
  }
}
