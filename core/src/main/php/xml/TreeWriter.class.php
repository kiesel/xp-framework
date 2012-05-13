<?php

	uses(
		'io.streams.OutputStream'
	);

	/**
	 * Write a tree as XML to an OutputStream
	 *
	 */
  abstract class TreeWriter extends Object {
  	protected $out= NULL;

  	/**
  	 * Constructor.
  	 *
  	 * @param 	io.streams.OutputStream out
  	 */
  	public function __construct(OutputStream $out) {
  		$this->out= $out;
  	}

    /**
     * Return underlying output stream
     *
     * @return  io.streams.OutputStream
     */
    public function getStream() {
      return $this->out;
    }

    /**
     * Return underlying output stream
     *
     * @param   io.streams.OutputStream stream
     */
    public function setStream(OutputStream $stream) {
      $this->out= $stream;
    }

    /**
     * Creates a string representation of this writer
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName()."@{\n  ".$this->out->toString()."\n}";
    }
  
    /**
     * Flush output buffer
     *
     */
    public function flush() {
      $this->out->flush();
    }

    /**
     * Write given tree to stream
     *
     * @param 	xml.Tree tree
     */
    public abstract function write(Tree $tree);
  }
?>