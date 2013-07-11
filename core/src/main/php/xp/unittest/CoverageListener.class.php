<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestListener',
    'io.streams.OutputStreamWriter',
    'xml.DomXSLProcessor',
    'xml.Tree',
    'xml.Node',
    'io.FileUtil',
    'webservices.json.JsonDecoder',
    'lang.Runtime',
    'lang.RuntimeError'
  );

  /**
   * Coverage listener - only shows details for failed tests.
   *
   * @purpose  TestListener
   */
  class CoverageListener extends Object implements TestListener {
    private
      $paths            = array(),
      $packages         = array(),
      $processor        = NULL,
      $reportFileJson   = NULL,
      $reportFileHtml   = NULL,
      $reportFileXml    = NULL;

    private $coverage = NULL;
    private $xml      = NULL;

    private static $coverageValues= array(
      -2  => 'dead',     // line w/o executable code
      -1  => 'noexec',   // not executed line
      1   => 'exec'      // executed line
    );

    /**
     * Register a path to include in coverage report
     *
     * @param string
     */
    #[@arg(name= 'registerPath')]
    public function setRegisterPath($path) {
      $this->paths[]= realpath($path);
    }

    /**
     * Register a package to check for coverage
     *
     * @param string $package
     */
    #[@arg(name= 'package')]
    public function setRegisterPackage($package) {
      $this->packages[]= $package;
    }

    /**
     * Set path for the report file
     *
     * @param string
     */
    #[@arg(name= 'html')]
    public function setHtml($reportFile) {
      $this->reportFileHtml= $reportFile;
    }

    /**
     * Set path for the report file
     *
     * @param string
     */
    #[@arg(name= 'xml')]
    public function setXml($reportFile) {
      $this->reportFileXml= $reportFile;
    }

    /**
     * Set path for the report file
     *
     * @param string
     */
    #[@arg(name= 'json')]
    public function setJson($reportFile) {
      $this->reportFileJson= $reportFile;
    }

    /**
     * Create a new instance for a listener
     *
     * @param  io.streams.OutputStreamWriter $out
     * @return var
     */
    public function newInstance(OutputStreamWriter $out) {
      return new DefaultListener($out);
    }

    /**
     * Constructor
     *
     * @param io.streams.OutputStreamWriter out
     */
    public function __construct() {
      if (!Runtime::getInstance()->extensionAvailable('xdebug')) {
        throw new RuntimeError('Code coverage not available. Please install the xdebug extension.');
      }

      $this->processor= new DomXSLProcessor();
      $this->processor->setXSLBuf($this->getClass()->getPackage()->getResource('coverage.xsl'));

      xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
    }

    /**
     * Called when a test case starts.
     *
     * @param   unittest.TestCase failure
     */
    public function testStarted(TestCase $case) {

    }

    /**
     * Called when a test fails.
     *
     * @param   unittest.TestFailure failure
     */
    public function testFailed(TestFailure $failure) {

    }

    /**
     * Called when a test errors.
     *
     * @param   unittest.TestFailure error
     */
    public function testError(TestError $error) {

    }

    /**
     * Called when a test raises warnings.
     *
     * @param   unittest.TestWarning warning
     */
    public function testWarning(TestWarning $warning) {

    }

    /**
     * Called when a test finished successfully.
     *
     * @param   unittest.TestSuccess success
     */
    public function testSucceeded(TestSuccess $success) {

    }

    /**
     * Called when a test is not run because it is skipped due to a
     * failed prerequisite.
     *
     * @param   unittest.TestSkipped skipped
     */
    public function testSkipped(TestSkipped $skipped) {

    }

    /**
     * Called when a test is not run because it has been ignored by using
     * the @ignore annotation.
     *
     * @param   unittest.TestSkipped ignore
     */
    public function testNotRun(TestSkipped $ignore) {

    }

    /**
     * Called when a test run starts.
     *
     * @param   unittest.TestSuite suite
     */
    public function testRunStarted(TestSuite $suite) {
      xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
    }

    /**
     * Determine whether to include given class in report
     *
     * @param  string $fileName
     * @param  string $className
     * @return bool
     */
    protected function includeInReport($fileName, $className) {
      $pathOk= empty($this->paths);
      foreach ($this->paths as $path) {
        if (substr($fileName, 0, strlen($path)) === $path) {
          $pathOk= TRUE;
          break;
        }
      }

      $packageOk= empty($this->packages);
      foreach ($this->packages as $package) {
        if (0 == strncmp($package, $className, strlen($package))) {
          $packageOk= TRUE;
          break;
        }
      }

      return $pathOk && $packageOk;
    }

    /**
     * Store coverage results
     *
     * @param var $coverage
     */
    protected function setCoverage($coverage) {
      $this->coverage= $coverage;
    }

    /**
     * Write JSON output file, if requested.
     *
     */
    public function writeOutputJson() {
      if (!$this->reportFileJson) return;
      $codec= new JSonDecoder();

      FileUtil::setContents(new File($this->reportFileJson), $codec->encode($this->coverage));
    }

    /**
     * Retrieve coverage as XML representation
     *
     * @return xml.Tree
     */
    protected function retrieveCoverageXml() {
      if ($this->xml) return $this->xml;

      $tree= new Tree('packages');
      $tree->root()->setAttribute('created', date('Y-m-d H:i:s'));

      foreach ($this->coverage as $package => $classes) {
        $pkg= $tree->addChild(new Node('package', NULL, array('name' => $package)));

        foreach ($classes as $className => $meta) {
          $classNode= $pkg->addChild(new Node('class', NULL, array(
            'name'     => $className,
            'fileName' => $meta['fileName']
          )));

          $num= 1;
          foreach (explode("\n", $meta['source']) as $line) {
            $lineNode = $classNode->addChild(new Node('line', new CData($line)));

            if (isset($meta['coverage'][$num])) {
              $lineNode->setAttribute('checked', self::$coverageValues[$meta['coverage'][$num]]);
            }

            ++$num;
          }
        }
      }
      $this->xml= $tree;
      return $this->xml;
    }

    /**
     * Write XML output file, if requested
     *
     */
    public function writeOutputXml() {
      if (!$this->reportFileXml) return;

      $tree= $this->retrieveCoverageXml();
      FileUtil::setContents(new File($this->reportFileXml), $tree->getDeclaration()."\n".$tree->getSource(0));
    }

    /**
     * Write HTML output file, if requested
     *
     */
    public function writeOutputHtml() {
      if (!$this->reportFileHtml) return;

      $tree= $this->retrieveCoverageXml();

      $this->processor->setXMLBuf($tree->getDeclaration()."\n".$tree->getSource());
      $this->processor->run();

      FileUtil::setContents(new File($this->reportFileHtml), $this->processor->output());
    }

    /**
     * Called when a test run finishes.
     *
     * @param   unittest.TestSuite suite
     * @param   unittest.TestResult result
     */
    public function testRunFinished(TestSuite $suite, TestResult $result) {
      $coverage= xdebug_get_code_coverage();
      xdebug_stop_code_coverage();

      $results= array(); $cl= ClassLoader::getDefault();
      foreach ($coverage as $fileName => $data) {

        $class= $cl->mapToClasS($fileName);
        if (!$class) continue;

        if ($this->includeInReport($fileName, $class->getName())) {
          $results[$class->getPackage()->getName()][$class->getName()]= array(
            'fileName' => $fileName,
            'coverage' => $data,
            'source'   => $class->getClassLoader()->loadClassBytes($class->getName())
          );
        }
      }
      $this->setCoverage($results);

      $this->writeOutputJson();
      $this->writeOutputXml();
      $this->writeOutputHtml();
    }
  }
?>
