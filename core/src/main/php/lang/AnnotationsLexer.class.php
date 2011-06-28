<?php
/* This class is part of the XP Framework
 *
 * $Id$
 */

  uses('text.parser.generic.AbstractLexer');

  /**
   * Lexical analyzer for annotations
   *
   * @test    xp://net.xp_framework.unittest.annotations.AnnotationsParserTest
   */
  class AnnotationsLexer extends AbstractLexer {
    protected
      $tokens = array(),
      $tokenMap = array(
        T_CLASS     => T_STRING,
        T_INTERFACE => T_STRING,
        T_LIST      => T_STRING,
        // TBD: Add more map tokens
      );

    public 
      $token    = NULL,
      $value    = NULL,
      $position = array(0, 0);

    public function __construct($string) {
      $this->tokens= array_slice(token_get_all('<?php '.$string.'?>'), 1, -1);
    }

    public function advance() {
      while (NULL !== current($this->tokens)) {
        $token= current($this->tokens);
        next($this->tokens);

        // Normalize
        if (is_string($token)) {
          $token= array(T_STRING, $token);
        }

        // Strip whitespace
        if (in_array($token[0], array(T_STRING, T_WHITESPACE))) {
          $token[1]= trim($token[1], " \n\r\t");
          if (0 == strlen($token[1])) continue;
        }

        $hasMore= (NULL !== current($this->tokens));

        // Find end
        if (NULL === $token) {
          $this->token= -1;
          $this->value= NULL;

          return FALSE;
        }

        // Map single-char "words" to their own token
        if (T_STRING == $token[0] && 1 == strlen($token[1])) {
          $this->token= ord($token[1]);
        } else if (isset($this->tokenMap[$token[0]])) {
          $this->token= $this->tokenMap[$token[0]];
        } else {
          $this->token= $token[0];
        }

        $this->value= $token[1];
        $this->position[0]+= 0; // TBD
        $this->position[1]+= strlen($this->value);

        return $hasMore;
      }
    }
  }
?>
