<?php namespace net\xp_framework\unittest\reflection;

use unittest\TestCase;


/**
 * TestCase for classloading
 *
 * @see    xp://lang.ClassLoader#registerPath
 */
class ClassPathTest extends TestCase {
  protected $registered= array();

  /**
   * Track registration of a class loader
   *
   * @param   lang.IClassLoader l
   * @return  lang.IClassLoader the given loader
   */
  protected function track($l) {
    $this->registered[]= $l;
    return $l;
  }

  /**
   * Removes all registered paths
   *
   */
  public function tearDown() {
    foreach ($this->registered as $l) {
      \lang\ClassLoader::removeLoader($l);
    }
  }

  /**
   * Test registering a path before all others
   *
   */
  #[@test]
  public function before() {
    $loader= $this->track(\lang\ClassLoader::registerPath('.', true));
    $loaders= \lang\ClassLoader::getLoaders();
    $this->assertEquals($loader, $loaders[0]);
  } 

  /**
   * Test registering a path after all others
   *
   */
  #[@test]
  public function after() {
    $loader= $this->track(\lang\ClassLoader::registerPath('.', false));
    $loaders= \lang\ClassLoader::getLoaders();
    $this->assertEquals($loader, $loaders[sizeof($loaders)- 1]);
  }

  /**
   * Test registering a path after all others is the default
   *
   */
  #[@test]
  public function after_is_default() {
    $loader= $this->track(\lang\ClassLoader::registerPath('.'));
    $loaders= \lang\ClassLoader::getLoaders();
    $this->assertEquals($loader, $loaders[sizeof($loaders)- 1]);
  }

  /**
   * Inspect path: it begins with !, so it is loaded first
   *
   */
  #[@test]
  public function before_via_inspect() {
    $loader= $this->track(\lang\ClassLoader::registerPath('!.', null));
    $loaders= \lang\ClassLoader::getLoaders();
    $this->assertEquals($loader, $loaders[0]);
  }

  /**
   * Inspect path: it does not begin with !, so it is loaded last
   *
   */
  #[@test]
  public function after_via_inspect() {
    $loader= $this->track(\lang\ClassLoader::registerPath('.', null));
    $loaders= \lang\ClassLoader::getLoaders();
    $this->assertEquals($loader, $loaders[sizeof($loaders)- 1]);
  }

  /**
   * Test registering a non-existant path
   *
   */
  #[@test, @expect('lang.ElementNotFoundException')]
  public function non_existant() {
    \lang\ClassLoader::registerPath('@@non-existant@@');
  } 
}
