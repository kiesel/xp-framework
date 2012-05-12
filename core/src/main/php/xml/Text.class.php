<?php
/* This class is part of the XP framework
 *
 * $Id$
 *
 */

  uses('xml.Element');

  /**
   * Represent text value
   *
   */
  class Text extends Object implements Element {
    private $content= NULL;
    private static $XML_ILLEGAL_CHARS= "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x0b\x0c\x0e\x0f\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f";

    /**
     * Constructor.
     *
     * @param   var content
     */
    public function __construct($content) {
      $this->setContent($content);
    }

    /**
     * Set content
     *
     * @param   var content either scalar value or lang.types.String
     * @throws  xml.XMLFormatException if illegal XML character contained
     */
    public function setContent($content) {
      if ($content instanceof String) {

        // Cast getBytes() to string, so lang.types.Bytes::__toString() will be called
        $content= (string)$content->getBytes('iso-8859-1');
      }

      if (!is_scalar($content)) {
        throw new IllegalArgumentException('Node::setContent() may only be called with string, '.xp::typeOf($content).' given.');
      }

      // TODO: These should be converted to entity representation
      if (strlen($content) > ($p= strcspn($content, self::$XML_ILLEGAL_CHARS))) {
        throw new XMLFormatException(
          'Content contains illegal character at position '.$p. ' / chr('.ord($content{$p}).')'
        );
      }

      $this->content= $content;
    }

    /**
     * Retrieve content data
     *
     * @return   string
     */
    public function getContent() {
      return $this->content;
    }

    /**
     * Retrieve source representation
     *
     * @param   int indent default INDENT_WRAPPED
     * @param   string encoding default "iso-8859-1"
     * @param   string inset default ""
     * @return  string
     */
    public function getSource($indent= INDENT_WRAPPED, $encoding= 'iso-8859-1', $inset= '') {
      return $inset.htmlspecialchars(
        iconv('iso-8859-1', $encoding, $this->content),
        ENT_COMPAT,
        $encoding
      );
    }
  }