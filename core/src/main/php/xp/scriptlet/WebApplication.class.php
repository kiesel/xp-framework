<?php namespace xp\scriptlet;

/**
 * Represents a web application
 *
 * @see      xp://xp.scriptlet.WebDebug
 * @see      xp://xp.scriptlet.Runner
 */
class WebApplication extends \lang\Object {
  protected $name = '';
  protected $config = '';
  protected $scriptlet = '';
  protected $route = '';
  protected $arguments = array();
  protected $environment = array();
  protected $debug = 0;

  /**
   * Creates a new web application named by the given name
   *
   * @param   string name
   */
  public function __construct($name) {
    $this->name= $name;
  }

  /**
   * Sets this application's name
   *
   * @param   string name
   */
  public function setName($name) {
    $this->name= $name;
  }

  /**
   * Sets this application's name
   *
   * @param   string name
   * @return  xp.scriptlet.WebApplication this
   */
  public function withName($name) {
    $this->name= $name;
    return $this;
  }
  
  /**
   * Returns this application's name
   *
   * @return  string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Sets this application's config
   *
   * @param   string config
   */
  public function setConfig($config) {
    $this->config= $config;
  }

  /**
   * Sets this application's config
   *
   * @param   string config
   * @return  xp.scriptlet.WebApplication this
   */
  public function withConfig($config) {
    $this->config= $config;
    return $this;
  }
  
  /**
   * Returns this application's config
   *
   * @return  string
   */
  public function getConfig() {
    return $this->config;
  }

  /**
   * Sets this application's debug flags
   *
   * @param   int debug
   */
  public function setDebug($debug) {
    $this->debug= $debug;
  }

  /**
   * Sets this application's debug flags
   *
   * @param   int debug
   * @return  xp.scriptlet.WebApplication this
   */
  public function withDebug($debug) {
    $this->debug= $debug;
    return $this;
  }
  
  /**
   * Returns this application's debug flags
   *
   * @return  int
   */
  public function getDebug() {
    return $this->debug;
  }

  /**
   * Sets this application's scriptlet class name
   *
   * @param   string scriptlet
   */
  public function setScriptlet($scriptlet) {
    $this->scriptlet= $scriptlet;
  }

  /**
   * Sets this application's scriptlet class name
   *
   * @param   string scriptlet
   * @return  xp.scriptlet.WebApplication this
   */
  public function withScriptlet($scriptlet) {
    $this->scriptlet= $scriptlet;
    return $this;
  }
  
  /**
   * Returns this application's scriptlet class
   *
   * @return  string
   */
  public function getScriptlet() {
    return $this->scriptlet;
  }

  /**
   * Sets this application's route class name
   *
   * @param   string route
   */
  public function setRoute($route) {
    $this->route= $route;
  }

  /**
   * Sets this application's route class name
   *
   * @param   string route
   * @return  xp.Route.WebApplication this
   */
  public function withRoute($route) {
    $this->route= $route;
    return $this;
  }

  /**
   * Returns this application's route class
   *
   * @return  string
   */
  public function getRoute() {
    return $this->route;
  }

  /**
   * Decide whether the application can handle the given route
   *
   * @param  string $url
   * @return boolean
   */
  public function handlesRoute($url) {
    return ('/' === $this->route || preg_match('#^('.preg_quote($this->route, '#').')($|/.+)#', $url));
  }

  /**
   * Sets this application's arguments
   *
   * @param   string[] arguments
   */
  public function setArguments($arguments) {
    $this->arguments= $arguments;
  }

  /**
   * Sets this application's arguments
   *
   * @param   string[] arguments
   * @return  xp.scriptlet.WebApplication this
   */
  public function withArguments($arguments) {
    $this->arguments= $arguments;
    return $this;
  }
  
  /**
   * Returns this application's arguments
   *
   * @return  string[]
   */
  public function getArguments() {
    return $this->arguments;
  }

  /**
   * Sets this application's environment
   *
   * @param   [:string] environment
   */
  public function setEnvironment($environment) {
    $this->environment= $environment;
  }

  /**
   * Sets this application's environment
   *
   * @param   [:string] environment
   * @return  xp.scriptlet.WebApplication this
   */
  public function withEnvironment($environment) {
    $this->environment= $environment;
    return $this;
  }
  
  /**
   * Returns this application's environment
   *
   * @return  [:string]
   */
  public function getEnvironment() {
    return $this->environment;
  }
  
  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() {
    return sprintf(
      "%s(%s)@{\n".
      "  [config       ] %s\n".
      "  [scriptlet    ] %s\n".
      "  [route        ] %s\n".
      "  [debug        ] %s\n".
      "  [arguments    ] [%s]\n".
      "  [environment  ] %s\n".
      "}",
      $this->getClassName(),
      $this->name,
      $this->config,
      $this->scriptlet,
      $this->route,
      implode(' | ', WebDebug::namesOf($this->debug)),
      implode(', ', $this->arguments),
      \xp::stringOf($this->environment, '  ')
    );
  }
  
  /**
   * Returns whether another object is equal to this
   *
   * @param   lang.Generic cmp
   * @return  bool
   */
  public function equals($cmp) {
    return (
      $cmp instanceof self && 
      $this->name === $cmp->name && 
      $this->config === $cmp->config && 
      $this->scriptlet === $cmp->scriptlet && 
      $this->route === $cmp->route &&
      $this->debug === $cmp->debug && 
      $this->arguments === $cmp->arguments &&
      $this->environment === $cmp->environment
    );
  }
}
