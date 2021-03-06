<?php namespace net\xp_framework\unittest\math;

use unittest\TestCase;
use math\BigInt;
use math\BigFloat;

/**
 * TestCase
 *
 * @see     xp://math.BigFloat
 * @see     xp://math.BigInt
 */
class BigIntAndFloatTest extends TestCase {
  
  #[@test]
  public function addFloatToInt() {
    $this->assertEquals(new BigFloat(2.9), create(new BigInt(1))->add(new BigFloat(1.9)));
  }

  #[@test]
  public function addFloatToInt0() {
    $this->assertEquals(new BigInt(2), create(new BigInt(1))->add0(new BigFloat(1.9)));
  }

  #[@test]
  public function subtractFloatFromInt() {
    $this->assertEquals(new BigFloat(0.1), create(new BigInt(2))->subtract(new BigFloat(1.9)));
  }

  #[@test]
  public function subtractFloatFromInt0() {
    $this->assertEquals(new BigInt(0), create(new BigInt(2))->subtract0(new BigFloat(1.9)));
  }

  #[@test]
  public function multiplyIntWithFloat() {
    $this->assertEquals(new BigFloat(3.8), create(new BigInt(2))->multiply(new BigFloat(1.9)));
  }

  #[@test]
  public function multiplyIntWithFloat0() {
    $this->assertEquals(new BigInt(3), create(new BigInt(2))->multiply0(new BigFloat(1.9)));
  }

  #[@test]
  public function divideIntByFloat() {
    $this->assertEquals(new BigFloat(4.0), create(new BigInt(2))->divide(new BigFloat(0.5)));
  }

  #[@test]
  public function divideIntByFloat0() {
    $this->assertEquals(new BigInt(4), create(new BigInt(2))->divide0(new BigFloat(0.5)));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function divideIntByFloatZero() {
    create(new BigInt(2))->divide(new BigFloat(0.0));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function divideIntByFloatZero0() {
    create(new BigInt(2))->divide0(new BigFloat(0.0));
  }

  #[@test]
  public function powerNegativeOne() {
    $this->assertEquals(new BigFloat(0.5), create(new BigInt(2))->power(new BigFloat(-1)));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function powerOneHalf() {
    create(new BigInt(2))->power(new BigFloat(0.5));
  }
}
