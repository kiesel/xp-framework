<?php

  /* This class is part of the XP Framework
   *
   * $Id$
   */

  uses('text.parser.generic.AbstractLexer');

  /**
   * Description of AnnotationsLexer.class
   *
   * @purpose
   */
  class AnnotationsLexer extends AbstractLexer {
    protected
      $tokens = array();

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

        // Map token
        // if (T_STRING == $token[0] && '@' == $token[1]) {
          // var_dump($token);
          // $token[0]= AnnotationsParser::T_AT;
        // }

        // Map single-char "words" to their own token
        if (T_STRING == $token[0] && 1 == strlen($token[1])) {
          $this->token= ord($token[1]);
        } else {
          switch (strtolower($token[1])) {
            case 'false': $this->token= AnnotationsParser::T_FALSE; break;
            case 'true': $this->token= AnnotationsParser::T_TRUE; break;
            case 'null': $this->token= AnnotationsParser::T_NULL; break;
            default: $this->token= $token[0]; break;
          }
        }
        $this->value= $token[1];
        $this->position[0]+= 0; // TBD
        $this->position[1]+= strlen($this->value);

        // var_dump($this->token, $this->value, '***');

        return $hasMore;
      }
    }
  }
?>
