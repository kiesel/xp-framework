<?php namespace xp\scriptlet;

use util\Properties;
use util\Hashmap;

/**
 * Web application configuration
 *
 * @see   xp://xp.scriptlet.WebApplication
 * @test  xp://scriptlet.unittest.WebConfigurationTest
 */
class WebConfiguration extends \lang\Object {
  protected $prop= null;
  
  /**
   * Creates a new web configuration instance
   *
   * @param   util.Properties prop
   */
  public function __construct(Properties $prop) {
    $this->prop= $prop;
  }

  /**
   * Read string. First tries special section "section"@"profile", then defaults 
   * to "section"
   *
   * @param   string profile
   * @param   string section
   * @param   string key
   * @param   var default default NULL
   * @return  string
   */
  protected function readString($profile, $section, $key, $default= null) {
    if (null === ($s= $this->prop->readString($section.'@'.$profile, $key, null))) {
      return $this->prop->readString($section, $key, $default);
    }
    return $s;
  }
  
  /**
   * Read array. First tries special section "section"@"profile", then defaults 
   * to "section"
   *
   * @param   string profile
   * @param   string section
   * @param   string key
   * @param   var default default NULL
   * @return  string[]
   */
  protected function readArray($profile, $section, $key, $default= null) {
    if (null === ($a= $this->prop->readArray($section.'@'.$profile, $key, null))) {
      return $this->prop->readArray($section, $key, $default);
    }
    return $a;
  }
  
  /**
   * Read hashmap. First tries special section "section"@"profile", then defaults 
   * to "section"
   *
   * @param   string profile
   * @param   string section
   * @param   string key
   * @param   var default default NULL
   * @return  util.Hashmap
   */
  protected function readHash($profile, $section, $key, $default= null) {
    if (null === ($h= $this->prop->readHash($section.'@'.$profile, $key, null))) {
      return $this->prop->readHash($section, $key, $default);
    }
    return $h;
  }
  
  /**
   * Creates a web application object from a given configuration section
   *
   * @param   string profile
   * @param   string section app name
   * @param   string url default null
   * @return  xp.scriptlet.WebApplication
   * @throws  lang.IllegalStateException if the web is misconfigured
   */
  protected function configuredApp($profile, $section, $url) {
    if (!$this->prop->hasSection($section)) {
      throw new \lang\IllegalStateException('Web misconfigured: Section '.$section.' mapped by '.$url.' missing');
    }

    $app= new WebApplication($section);
    $app->setScriptlet($this->readString($profile, $section, 'class', ''));
    
    // Configuration base
    $app->setConfig($this->readString($profile, $section, 'prop-base', '{WEBROOT}/etc'));

    // Route
    if ($url) {
      $app->setRoute($url);
    } else {
      $app->setRoute($this->readString($profile, $section, 'route'));
    }

    // Determine debug level
    $flags= WebDebug::NONE;
    foreach ($this->readArray($profile, $section, 'debug', array()) as $lvl) {
      $flags |= WebDebug::flagNamed($lvl);
    }
    $app->setDebug($flags);
    
    // Initialization arguments
    $app->setArguments($this->readArray($profile, $section, 'init-params', array()));
 
    // Environment
    $app->setEnvironment($this->readHash($profile, $section, 'init-envs', new Hashmap())->toArray());
   
    return $app;
  }
  
  /**
   * Gets all mapped applications
   *
   * @param   string profile
   * @return  [:xp.scriptlet.WebApplication]
   * @throws  lang.IllegalStateException if the web is misconfigured
   */
  public function mappedApplications($profile= null) {
    $mappings= $this->prop->readHash('app', 'mappings', null);
    $apps= array();

    // Verify configuration
    if ($this->prop->hasSection('app') && null === $mappings) {
      foreach ($this->prop->readSection('app') as $key => $url) {
        if (0 !== strncmp('map.', $key, 4)) continue;
        $apps[]= $this->configuredApp($profile, 'app::'.substr($key, 4), $url);
      }
    } else if ($this->prop->hasSection('app')) {
      foreach ($mappings->keys() as $url) {
        $apps[]= $this->configuredApp($profile, 'app::'.$mappings->get($url), $url);
      }
    } else {
      $settings= $this->prop->readSection('settings');
      $section= $this->prop->getFirstSection();

      do {

        // Only parse sections named 'route' or 'route::n'
        if (0 != strncasecmp('route', $section, 5)) continue;

        $apps[]= $this->configuredApp($profile, $section, null);
      } while ($section= $this->prop->getNextSection());
    }

    if (0 === sizeof($apps)) {
      throw new \lang\IllegalStateException('No webapp found: "app" or "route" section(s) missing or broken.');
    }

    return $apps;
  }

  /**
   * Gets all static resources
   *
   * @param   string profile
   * @return  [:string]
   */
  public function staticResources($profile= null) {
    $hash= $this->prop->readHash('static', 'resources', null);
    return null === $hash ? null : $hash->toArray();
  }
}
