<?php namespace net\xp_framework\unittest\scriptlet;

use unittest\TestCase;
use xp\scriptlet\Runner;
use xml\Node;
use util\log\Traceable;
use util\log\BufferedAppender;
use scriptlet\HttpScriptlet;
use scriptlet\xml\XMLScriptlet;
use lang\Runtime;
use lang\System;

/**
 * TestCase
 *
 * @see   xp://xp.scriptlet.Runner
 */
class RunnerTest extends TestCase {
  protected static $welcomeScriptlet= null;
  protected static $errorScriptlet= null;
  protected static $debugScriptlet= null;
  protected static $xmlScriptlet= null;
  protected static $exitScriptlet= null;
  protected static $propertySource= null;
  
  /**
   * Defines scriptlet classes used as fixtures
   *
   */
  #[@beforeClass]
  public static function defineScriptlets() {
    self::$errorScriptlet= \lang\ClassLoader::defineClass('ErrorScriptlet', 'scriptlet.HttpScriptlet', array('util.log.Traceable'), '{
      protected function _request() {
        $req= parent::_request();
        $req->method= "GET";
        $req->env["SERVER_PROTOCOL"]= "HTTP/1.1";
        $req->env["REQUEST_URI"]= "/error";
        $req->env["HTTP_HOST"]= "localhost";
        return $req;
      }
      
      public function setTrace($cat) {
        $cat->debug("Injected", $cat->getClassName());
      }
      
      protected function _setupRequest($request) {
        // Intentionally empty
      }
      
      public function doGet($request, $response) {
        throw new IllegalAccessException("No shoes, no shorts, no service");
      }
    }');
    self::$welcomeScriptlet= \lang\ClassLoader::defineClass('WelcomeScriptlet', 'scriptlet.HttpScriptlet', array(), '{
      protected function _request() {
        $req= parent::_request();
        $req->method= "GET";
        $req->env["SERVER_PROTOCOL"]= "HTTP/1.1";
        $req->env["REQUEST_URI"]= "/welcome";
        $req->env["HTTP_HOST"]= "localhost";
        return $req;
      }
      
      protected function _setupRequest($request) {
        // Intentionally empty
      }
      
      public function doGet($request, $response) {
        $response->write("<h1>Welcome, we are open</h1>");
      }
    }');
    self::$xmlScriptlet= \lang\ClassLoader::defineClass('XmlScriptletImpl', 'scriptlet.xml.XMLScriptlet', array(), '{
      protected function _request() {
        $req= parent::_request();
        $req->method= "GET";
        $req->env["SERVER_PROTOCOL"]= "HTTP/1.1";
        $req->env["REQUEST_URI"]= "/welcome";
        $req->env["HTTP_HOST"]= "localhost";
        return $req;
      }

      protected function _response() {
        $res= parent::_response();
        $stylesheet= create(new \xml\Stylesheet())
          ->withEncoding("iso-8859-1")
          ->withOutputMethod("xml")
          ->withTemplate(create(new XslTemplate())->matching("/")
            ->withChild(create(new Node("h1"))
              ->withChild(new Node("xsl:value-of", NULL, array("select" => "/formresult/result")))
            )
          )
        ;
        $res->setStylesheet($stylesheet, XSLT_TREE);
        return $res;
      }
      
      protected function _setupRequest($request) {
        // Intentionally empty
      }
      
      public function doGet($request, $response) {
        $response->addFormresult(new Node("result", "Welcome, we are open"));
      }
    }');
    self::$debugScriptlet= \lang\ClassLoader::defineClass('DebugScriptlet', 'scriptlet.HttpScriptlet', array(), '{
      protected $title, $date;

      public function __construct($title, $date) {
        $this->title= $title;
        $this->date= $date;
      }
      
      protected function _request() {
        $req= parent::_request();
        $req->method= "GET";
        $req->env["SERVER_PROTOCOL"]= "HTTP/1.1";
        $req->env["REQUEST_URI"]= "/debug";
        $req->env["HTTP_HOST"]= "localhost";
        return $req;
      }
      
      protected function _setupRequest($request) {
        // Intentionally empty
      }
      
      public function doGet($request, $response) {
        $response->write("<h1>".$this->title." @ ".$this->date."</h1>");

        $response->write("<ul>");
        $response->write("  <li>ENV.DOMAIN = ".$request->getEnvValue("DOMAIN")."</li>");
        $response->write("  <li>ENV.ADMINS = ".$request->getEnvValue("ADMINS")."</li>");
        $response->write("</ul>");

        $config= PropertyManager::getInstance()->getProperties("debug")->getFileName();
        $response->write("<h2>".strtr($config, DIRECTORY_SEPARATOR, "/")."</h2>");
      }
    }');
    self::$exitScriptlet= \lang\ClassLoader::defineClass('ExitScriptlet', 'scriptlet.HttpScriptlet', array(), '{
      protected function _request() {
        $req= parent::_request();
        $req->method= "GET";
        $req->env["SERVER_PROTOCOL"]= "HTTP/1.1";
        $req->env["REQUEST_URI"]= "/exit";
        $req->env["HTTP_HOST"]= "localhost";
        $req->setParams($_REQUEST);
        return $req;
      }
      
      protected function _setupRequest($request) {
        // Intentionally empty
      }
      
      public function doGet($request, $response) {
        \lang\Runtime::halt($request->getParam("code"), $request->getParam("message"));
      }
    }');
  }

  /**
   * Sets up property source
   *
   */
  #[@beforeClass]
  public static function setupPropertySource() {
    self::$propertySource= \util\PropertyManager::getInstance()->appendSource(newinstance('util.PropertySource', array(), '{
      public function provides($name) { return "debug" === $name; }
      public function fetch($name) { return new Properties("/var/www/etc/dev/debug.ini"); }
    }'));
  }

  /**
   * Sets up property source
   *
   */
  #[@afterClass]
  public static function removePropertySource() {
    \util\PropertyManager::getInstance()->removeSource(self::$propertySource);
  }

  /**
   * Creates a new runner
   *
   */ 
  protected function newRunner($profile= null) {
    $r= new Runner('/var/www', $profile);
    
    // The debug application
    $r->mapApplication('/debug', create(new \xp\scriptlet\WebApplication('debug'))
      ->withScriptlet(self::$debugScriptlet->getName())
      ->withConfig($r->expand('{WEBROOT}/etc/{PROFILE}'))
      ->withEnvironment(array('DOMAIN' => 'example.com', 'ADMINS' => 'admin@example.com,root@localhost'))
      ->withArguments(array('Debugging', 'today'))
    );

    // The error application
    $r->mapApplication('/error', create(new \xp\scriptlet\WebApplication('error'))
      ->withScriptlet(self::$errorScriptlet->getName())
      ->withConfig($r->expand('{WEBROOT}/etc'))
      ->withDebug('dev' === $profile 
        ? \xp\scriptlet\WebDebug::XML | \xp\scriptlet\WebDebug::ERRORS | \xp\scriptlet\WebDebug::STACKTRACE | \xp\scriptlet\WebDebug::TRACE
        : \xp\scriptlet\WebDebug::NONE
      )
    );

    // The incomplete app (missing a scriptlet)
    $r->mapApplication('/incomplete', create(new \xp\scriptlet\WebApplication('incomplete'))
      ->withScriptlet(null)
      ->withDebug(\xp\scriptlet\WebDebug::STACKTRACE)
    );

    // The XML application
    $r->mapApplication('/xml', create(new \xp\scriptlet\WebApplication('xml'))
      ->withScriptlet(self::$xmlScriptlet->getName())
      ->withDebug('dev' === $profile 
        ? \xp\scriptlet\WebDebug::XML 
        : \xp\scriptlet\WebDebug::NONE
      )
    );
    
    // The exit scriptlet
    $r->mapApplication('/exit', create(new \xp\scriptlet\WebApplication('exit'))
      ->withScriptlet(self::$exitScriptlet->getName())
    );

    // The welcome application
    $r->mapApplication('/', create(new \xp\scriptlet\WebApplication('welcome'))
      ->withScriptlet(self::$welcomeScriptlet->getName())
      ->withConfig($r->expand('{WEBROOT}/etc'))
      ->withDebug('dev' === $profile 
        ? \xp\scriptlet\WebDebug::XML | \xp\scriptlet\WebDebug::ERRORS | \xp\scriptlet\WebDebug::STACKTRACE
        : \xp\scriptlet\WebDebug::NONE
      )
    );
    
    return $r;
  }

  /**
   * Test expand() method
   *
   */
  #[@test]
  public function expandServerProfile() {
    $this->assertEquals('etc/dev/', $this->newRunner('dev')->expand('etc/{PROFILE}/'));
  }

  /**
   * Test expand() method
   *
   */
  #[@test]
  public function expandWebRoot() {
    $this->assertEquals('/var/www/htdocs', $this->newRunner('dev')->expand('{WEBROOT}/htdocs'));
  }

  /**
   * Test expand() method
   *
   */
  #[@test]
  public function expandWebRootAndServerProfile() {
    $this->assertEquals('/var/www/etc/prod/', $this->newRunner('prod')->expand('{WEBROOT}/etc/{PROFILE}/'));
  }

  /**
   * Test expand() method
   *
   */
  #[@test]
  public function expandUnknownVariable() {
    $this->assertEquals('{ROOT}', $this->newRunner('prod')->expand('{ROOT}'));
  }

  /**
   * Test matching of URL against configuration works
   *
   */
  #[@test, @expect(class= 'lang.IllegalArgumentException', withMessage= 'Could not find app responsible for request to /')]
  public function noApplication() {
    with ($p= \util\Properties::fromString('')); {
      $p->writeSection('app');
      $p->writeString('app', 'map.service', '/service');
      $p->writeSection('app::service');

      $r= new Runner('/htdocs');
      $r->configure($p);
      $r->applicationAt('/');
    }
  }

  /**
   * Test matching of URL against configuration works
   *
   */
  #[@test]
  public function welcomeApplication() {
    $this->assertEquals(
      create(new \xp\scriptlet\WebApplication('welcome'))->withConfig('/var/www/etc')->withScriptlet('WelcomeScriptlet')->withRoute('/'),
      $this->newRunner()->applicationAt('/')
    );
  }

  /**
   * Test matching of URL against configuration works
   *
   */
  #[@test]
  public function welcomeApplicationAtEmptyUrl() {
    $this->assertEquals(
      create(new \xp\scriptlet\WebApplication('welcome'))->withConfig('/var/www/etc')->withScriptlet('WelcomeScriptlet')->withRoute('/'),
      $this->newRunner()->applicationAt('')
    );
  }

  /**
   * Test matching of URL against configuration works
   *
   */
  #[@test]
  public function welcomeApplicationAtDoubleSlash() {
    $this->assertEquals(
      create(new \xp\scriptlet\WebApplication('welcome'))->withConfig('/var/www/etc')->withScriptlet('WelcomeScriptlet')->withRoute('/'),
      $this->newRunner()->applicationAt('//')
    );
  }

  /**
   * Test matching of URL against configuration works
   *
   */
  #[@test]
  public function errorApplication() {
    $this->assertEquals(
      create(new \xp\scriptlet\WebApplication('error'))->withConfig('/var/www/etc')->withScriptlet('ErrorScriptlet')->withRoute('/error'),
      $this->newRunner()->applicationAt('/error')
    );
  }

  /**
   * Test matching of URL against configuration works
   *
   */
  #[@test]
  public function welcomeApplicationAtUrlEvenWithErrorInside() {
    $this->assertEquals(
      create(new \xp\scriptlet\WebApplication('welcome'))->withConfig('/var/www/etc')->withScriptlet('WelcomeScriptlet')->withRoute('/'),
      $this->newRunner()->applicationAt('/url/with/error/inside')
    );
  }

  /**
   * Test matching of URL against configuration works
   *
   */
  #[@test]
  public function welcomeApplicationAtUrlBeginningWithErrors() {
    $this->assertEquals(
      create(new \xp\scriptlet\WebApplication('welcome'))->withConfig('/var/www/etc')->withScriptlet('WelcomeScriptlet')->withRoute('/'),
      $this->newRunner()->applicationAt('/errors')
    );
  }

  /**
   * Test matching of URL against configuration works
   *
   */
  #[@test]
  public function errorApplicationAtErrorPath() {
    $this->assertEquals(
      create(new \xp\scriptlet\WebApplication('error'))->withConfig('/var/www/etc')->withScriptlet('ErrorScriptlet')->withRoute('/error'),
      $this->newRunner()->applicationAt('/error/happened')
    );
  }

  /**
   * Runs a scriptlet
   *
   * @param   string profile
   * @param   string url
   * @param   [:string] params
   * @return  string content
   */
  protected function runWith($profile, $url, $params= array()) {
    ob_start();
    $_REQUEST= $params;
    $this->newRunner($profile)->run($url);
    $_REQUEST= array();
    $content= ob_get_contents();
    ob_end_clean();
    return $content;
  }
  
  /**
   * Test normal page display
   *
   */
  #[@test]
  public function pageInProdMode() {
    $this->assertEquals(
      '<h1>Welcome, we are open</h1>', 
      $this->runWith('prod', '/')
    );
  }

  /**
   * Test normal page display
   *
   */
  #[@test]
  public function pageWithWarningsInProdMode() {
    $warning= 'Warning! Do not read if you have work to do!';
    with (trigger_error($warning)); {
      preg_match(
        '#'.preg_quote($warning).'#', 
        $this->runWith('prod', '/'),
        $matches
      );
      \xp::gc(__FILE__);
    }
    $this->assertEquals(array(), $matches);
  }

  /**
   * Test normal page display with warnings
   *
   */
  #[@test]
  public function pageWithWarningsInDevMode() {
    $warning= 'Warning! Do not read if you have work to do!';
    with (trigger_error($warning)); {
      preg_match(
        '#'.preg_quote($warning).'#', 
        $this->runWith('dev', '/'),
        $matches
      );
      \xp::gc(__FILE__);
    }
    $this->assertEquals($warning, $matches[0]);
  }

  /**
   * Test error page display
   *
   */
  #[@test]
  public function errorPageInProdMode() {
    $content= $this->runWith('prod', '/error');
    preg_match('#<xmp>(.+)</xmp>#', $content, $matches);
    preg_match('#ERROR ([0-9]+)#', $content, $error);

    $this->assertEquals('500', $error[1], 'error message');
    $this->assertEquals(
      'Request processing failed [doGet]: No shoes, no shorts, no service', 
      $matches[1]
    );
  }

  /**
   * Asserts a given buffer contains the given bytes       
   *
   * @param   string bytes
   * @param   string buffer
   * @throws  unittest.AssertionFailedError
   */
  protected function assertContained($bytes, $buffer, $message= 'Not contained') {
    strstr($buffer, $bytes) || $this->fail($message, $buffer, $bytes);
  }

  /**
   * Asserts a given buffer does not contain the given bytes       
   *
   * @param   string bytes
   * @param   string buffer
   * @throws  unittest.AssertionFailedError
   */
  protected function assertNotContained($bytes, $buffer, $message= 'Contained') {
    strstr($buffer, $bytes) && $this->fail($message, $buffer, $bytes);
  }

  /**
   * Test error page display
   *
   */
  #[@test]
  public function errorPageLoggingInProdMode() {
    with ($cat= \util\log\Logger::getInstance()->getCategory('scriptlet')); {
      $appender= $cat->addAppender(new BufferedAppender());
      $this->runWith('prod', '/error');
      $buffer= $appender->getBuffer();
      $cat->removeAppender($appender);
      
      $this->assertNotContained(
        'Injected util.log.LogCategory',
        $buffer
      );
      $this->assertContained(
        'Exception scriptlet.ScriptletException (500:Request processing failed [doGet]: No shoes, no shorts, no service)', 
        $buffer
      );
    }
  }

  /**
   * Test error page display
   *
   */
  #[@test]
  public function errorPageLoggingInDevMode() {
    with ($cat= \util\log\Logger::getInstance()->getCategory('scriptlet')); {
      $appender= $cat->addAppender(new BufferedAppender());
      $this->runWith('dev', '/error');
      $buffer= $appender->getBuffer();
      $cat->removeAppender($appender);
      
      $this->assertContained(
        'Injected util.log.LogCategory',
        $buffer
      );
      $this->assertContained(
        'Exception scriptlet.ScriptletException (500:Request processing failed [doGet]: No shoes, no shorts, no service)', 
        $buffer
      );
    }
  }

  /**
   * Test error page display
   *
   */
  #[@test]
  public function errorPageInDevMode() {
    $content= $this->runWith('dev', '/error');
    preg_match('#ERROR ([0-9]+)#', $content, $error);
    preg_match('#<xmp>(.+)#', $content, $compound);
    preg_match('#Caused by (.+)#', $content, $cause);

    $this->assertEquals('500', $error[1], 'error message');
    $this->assertEquals(
      'Exception scriptlet.ScriptletException (500:Request processing failed [doGet]: No shoes, no shorts, no service)', 
      $compound[1],
      'exception compound message'
    );
    $this->assertEquals(
      'Exception lang.IllegalAccessException (No shoes, no shorts, no service)',
      $cause[1],
      'exception cause'
    );
  }

  /**
   * Test debug page display
   *
   */
  #[@test]
  public function debugPage() {
    $content= $this->runWith('dev', '/debug');
    preg_match('#<h1>(.+)</h1>#', $content, $params);
    preg_match('#<h2>(.+)</h2>#', $content, $config);
    preg_match_all('#<li>(ENV\..+)</li>#U', $content, $env);

    $this->assertEquals('Debugging @ today', $params[1], 'params');
    $this->assertEquals('/var/www/etc/dev/debug.ini', $config[1], 'config');
    $this->assertEquals(
      array('ENV.DOMAIN = example.com', 'ENV.ADMINS = admin@example.com,root@localhost'),
      $env[1],
      'environment'
    );
  }

  /**
   * Test error page display
   *
   */
  #[@test]
  public function incompleteApp() {
    $content= $this->runWith(null, '/incomplete');
    preg_match('#ERROR ([0-9]+)#', $content, $error);
    preg_match('#<xmp>(.+)#', $content, $compound);

    $this->assertEquals('412', $error[1], 'error message');
    $this->assertEquals(
      'Exception lang.ClassNotFoundException (Class "" could not be found) {', 
      $compound[1],
      'exception compound message'
    );
  }

  /**
   * Test XML app display
   *
   */
  #[@test]
  public function xmlScriptletAppInProdMode() {
    $content= $this->runWith('prod', '/xml');
    $this->assertEquals(
      '<?xml version="1.0" encoding="iso-8859-1"?><h1>Welcome, we are open</h1>',
      str_replace("\n", '', $content)
    );
  }

  /**
   * Test XML app display
   *
   */
  #[@test]
  public function xmlScriptletAppInDevMode() {
    $content= $this->runWith('dev', '/xml');
    preg_match('#<h1>(.+)</h1>#', $content, $output);
    preg_match('#<result>(.+)</result>#', $content, $source);
    
    $this->assertEquals('Welcome, we are open', $output[1], 'output');
    $this->assertEquals('Welcome, we are open', $source[1], 'source');
    $this->assertContained('<formresult', $content, 'formresult');
    $this->assertContained('<formvalues', $content, 'formvalues');
    $this->assertContained('<formerrors', $content, 'formerrors');
  }

  /**
   * Test exit app
   *
   */
  #[@test]
  public function exitScriptletWithZeroExitCode() {
    $content= $this->runWith('dev', '/exit', array('code' => '0'));
    $this->assertEquals('', $content);
  }

  /**
   * Test exit app
   *
   */
  #[@test]
  public function exitScriptletWithZeroExitCodeAndMessage() {
    $content= $this->runWith('dev', '/exit', array('code' => '0', 'message' => 'Sorry'));
    $this->assertEquals('Sorry', $content);
  }

  /**
   * Test exit app
   *
   */
  #[@test]
  public function exitScriptletWithNonZeroExitCode() {
    $content= $this->runWith('dev', '/exit', array('code' => '1'));
    preg_match('#ERROR ([0-9]+)#', $content, $error);
    preg_match('#<xmp>(.+)</xmp>#', $content, $compound);

    $this->assertEquals('500', $error[1], 'error message');
    $this->assertEquals(array(), $compound, 'exception compound message');
  }

  /**
   * Test exit app
   *
   */
  #[@test]
  public function exitScriptletWithNonZeroExitCodeAndMessage() {
    $content= $this->runWith('dev', '/exit', array('code' => '1', 'message' => 'Sorry'));
    preg_match('#ERROR ([0-9]+)#', $content, $error);
    preg_match('#<xmp>(.+)</xmp>#', $content, $compound);

    $this->assertEquals('500', $error[1], 'error message');
    $this->assertEquals('Sorry', $compound[1], 'exception compound message');
  }

  /**
   * Test main() method, which receives the following arguments:
   *
   * <ol>
   *   <li>The web root</li>
   *   <li>The configuration directory</li>
   *   <li>The server profile</li>
   *   <li>The script URL</li>
   * </ol>
   */
  #[@test]
  public function callingMain() {
    $temp= System::tempDir();

    // Create web.ini in system's temp dir
    $ini= new \io\File($temp, 'web.ini');
    $ini->open(FILE_MODE_WRITE);
    $ini->write(
      "[app]\n".
      "mappings=\"/:welcome\"\n".
      "[app::welcome]\n".
      "class=undefined\n".
      "[app::welcome@dev]\n".
      "class=\"".self::$welcomeScriptlet->getName()."\"\n"
    );
    $ini->close();

    // Run
    ob_start();
    Runner::main(array($temp, $temp, 'dev', '/'));
    $content= ob_get_contents();
    ob_end_clean();
    $ini->unlink();

    // Assert
    $this->assertEquals('<h1>Welcome, we are open</h1>', $content);
  }
}
