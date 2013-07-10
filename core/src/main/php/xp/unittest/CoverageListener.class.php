<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestListener',
    'io.streams.OutputStreamWriter',
    'xml.DomXSLProcessor',
    'xml.Node',
    'io.FileUtil',
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
      $paths        = array(),
      $processor    = NULL,
      $reportFile   = 'coverage.html';

    private static $coverageValues= array(
      -2  => 'dead',     // line w/o executable code
      -1  => 'noexec',   // not executed line
      1   => 'exec'      // executed line
    );

    /**
     * register a path to include in coverage report
     *
     * @param string
     */
    #[@arg(name= 'registerPath')]
    public function setRegisterPath($path) {
      $this->paths[]=  realpath($path);
    }

    /**
     * set path for the report file
     *
     * @param string
     */
    #[@arg(name= 'reportFile')]
    public function setReportFile($reportFile) {
      $this->reportFile=$reportFile;
    }

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

        if (0 == strncasecmp('dyn://', $fileName, 6)) {
          continue;
        }

        $include= TRUE;

        // FIXME: This code is not platform-agnostic
        // FIXME: This should be improved to use ClassLoader API
        if (sizeof ($this->paths)) {
          $include= FALSE;

          foreach ($this->paths as $path) {
            if (substr($fileName, 0, strlen($path)) === $path) {
              $include= TRUE;
              break;
            }
          }
        }

        if ($include) {
          $results[$class->getPackage()->getName()][$class->getName()]= array(
            'fileName' => $fileName,
            'coverage' => $data
          );
        }
      }

      $tree= new Tree('packages');
      $tree->root()->setAttribute('created', date('Y-m-d H:i:s'));
      foreach ($results as $package => $classes) {
        $pkg= $tree->addChild(new Node('package', NULL, array('name' => $package)));

        foreach ($classes as $className => $meta) {
          $classNode= $pkg->addChild(new Node('class', NULL, array(
            'name'     => $className,
            'fileName' => $meta['fileName']
          )));

          $num= 1;
          $reader= new TextReader(new FileInputStream($meta['fileName']));
          while (($line = $reader->readLine()) !== NULL) {
            $lineNode = $classNode->addChild(new Node('line', new CData($line)));

            if (isset($meta['coverage'][$num])) {
              $lineNode->setAttribute('checked', self::$coverageValues[$meta['coverage'][$num]]);
            }

            ++$num;
          }
        }
      }

      $this->processor->setXMLBuf($tree->getDeclaration()."\n".$tree->getSource());
      $this->processor->run();

      FileUtil::setContents(new File($this->reportFile), $this->processor->output());
      // FileUtil::setContents(new File($this->reportFile), $tree->getDeclaration()."\n".$tree->getSource(0));
    }
  }
?>
