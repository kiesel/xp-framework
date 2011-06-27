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
      while (1) {
        $token= current($this->tokens);
        next($this->tokens);
        
        // Strip whitespace
        if (is_string($token)) {
          $token= trim($token, ' \n\r\t');
          if (0 == strlen($token)) continue;
        }



        $hasMore= (NULL !== current($this->tokens));


        // Find end
        if (NULL === $token) {
          $this->token= -1;
          $this->value= NULL;

          return FALSE;
        }

        // Normalize token
        if (is_string($token)) {
          var_dump("here");
          $this->token= AnnotationsParser::T_AT;
          $this->value= $token;

          return $hasMore;
        }

        $this->token= $token[0];
        $this->value= $token[1];
        $this->position[0]+= 0;// TBD
        $this->position[1]+= strlen($this->value);

        return $hasMore;
      }
    }
  }
?>
