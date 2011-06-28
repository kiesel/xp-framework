<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  
#line 1 "src/resources/grammar/annotations.y"

  uses(
    'lang.AnnotationsLexer'
  );

#line 15 "-"

  uses('text.parser.generic.AbstractParser');

  /**
   * Generated parser class
   *
   * @purpose  Parser implementation
   */
  class AnnotationsParser extends AbstractParser {
    const T_WORD= 260;
    const T_STRING= 307;
    const T_LNUMBER= 305;
    const T_DNUMBER= 306;
    const T_CONSTANT_ENCAPSED_STRING= 315;
    const T_ARRAY= 360;
    const YY_ERRORCODE= 256;

    protected static $yyLhs= array(-1,
          0,     1,     1,     2,     2,     3,     3,     4,     4,     4, 
          5,     5,     5,     6,     6,     6,     6,
    );
    protected static $yyLen= array(2,
          1,     1,     3,     2,     5,     1,     1,     1,     2,     3, 
          1,     3,     6,     1,     1,     1,     1,
    );
    protected static $yyDefRed= array(0,
          0,     0,     0,     2,     6,     7,     0,     0,     0,     3, 
          0,    16,    17,     0,     0,     0,     0,    11,     0,     5,
          0,    14,    15,     0,    12,    10,     0,     0,    13,
    );
    protected static $yyDgoto= array(2,
          3,     4,    15,    16,    17,    18,
    );
    protected static $yySindex = array(          -48,
       -301,     0,   -27,     0,     0,     0,   -22,   -48,  -294,     0,
          0,     0,     0,     0,   -42,   -21,   -18,     0,  -305,     0,
       -294,     0,     0,   -17,     0,     0,  -294,   -19,     0,
    );
    protected static $yyRindex= array(            0,
          0,     0,    27,     0,     0,     0,     3,     0,     0,     0,
        -37,     0,     0,   -36,     0,     0,   -13,     0,     0,     0,
        -11,     0,     0,     0,     0,     0,     0,     0,     0,
    );
    protected static $yyGindex= array(0,
          0,    21,    30,   -12,     0,    13,
    );
    protected static $yyTable = array(12,
         13,    22,     4,    14,    15,     5,    14,    15,    26,    23,
         12,    13,    11,     6,    28,     1,     8,     9,    19,    20,
         14,    29,    27,     6,     7,    21,     1,     8,    10,     9,
          7,    25,     0,     0,     0,     0,     0,     0,     0,     0,
          0,     0,     0,     0,     0,     0,     4,     0,     0,     0,
          0,     0,     0,     0,    24,
    );
    protected static $yyCheck = array(305,
        306,   307,     0,    41,    41,   307,    44,    44,    21,   315,
        305,   306,   307,   315,    27,    64,    44,    40,    61,    41,
        315,    41,    40,    61,    61,    44,     0,    41,     8,    41,
          1,    19,    -1,    -1,    -1,    -1,    -1,    -1,    -1,    -1,
         -1,    -1,    -1,    -1,    -1,    -1,    44,    -1,    -1,    -1,
         -1,    -1,    -1,    -1,   360,
    );
    protected static $yyFinal= 2;
    protected static $yyName= array(    
      'end-of-file', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      "'('", "')'", NULL, NULL, "','", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "'='", NULL, NULL, "'@'", NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'T_WORD', 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, 'T_LNUMBER', 'T_DNUMBER', 'T_STRING', NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, 'T_CONSTANT_ENCAPSED_STRING', NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
      NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'T_ARRAY',
    );

    protected static $yyTableCount= 0, $yyNameCount= 0;

    static function __static() {
      self::$yyTableCount= sizeof(self::$yyTable);
      self::$yyNameCount= sizeof(self::$yyName);
    }

    /**
     * Retrieves name of a given token
     *
     * @param   int token
     * @return  string name
     */
    protected function yyname($token) {
      return isset(self::$yyName[$token]) ? self::$yyName[$token] : '<unknown>';
    }

    /**
     * Helper method for yyexpecting
     *
     * @param   int n
     * @return  string[] list of token names.
     */
    protected function yysearchtab($n) {
      if (0 == $n) return array();

      for (
        $result= array(), $token= $n < 0 ? -$n : 0; 
        $token < self::$yyNameCount && $n+ $token < self::$yyTableCount; 
        $token++
      ) {
        if (@self::$yyCheck[$n+ $token] == $token && !isset($result[$token])) {
          $result[$token]= self::$yyName[$token];
        }
      }
      return array_filter(array_values($result));
    }

    /**
     * Computes list of expected tokens on error by tracing the tables.
     *
     * @param   int state for which to compute the list.
     * @return  string[] list of token names.
     */
    protected function yyexpecting($state) {
      return array_merge($this->yysearchtab(self::$yySindex[$state], self::$yyRindex[$state]));
    }

    /**
     * Parser main method. Maintains a state and a value stack, 
     * currently with fixed maximum size.
     *
     * @param   text.parser.generic.AbstractLexer lexer
.    * @return  mixed result of the last reduction, if any.
     */
    public function yyparse($yyLex) {
      $yyVal= NULL;
      $yyStates= $yyVals= array();
      $yyToken= -1;
      $yyState= $yyErrorFlag= 0;

      while (1) {
        for ($yyTop= 0; ; $yyTop++) {
          $yyStates[$yyTop]= $yyState;
          $yyVals[$yyTop]= $yyVal;

          for (;;) {
            if (($yyN= self::$yyDefRed[$yyState]) == 0) {

              // Check whether it's necessary to fetch the next token
              $yyToken < 0 && $yyToken= $yyLex->advance() ? $yyLex->token : 0;

              if (
                ($yyN= self::$yySindex[$yyState]) != 0 && 
                ($yyN+= $yyToken) >= 0 && 
                $yyN < self::$yyTableCount && 
                self::$yyCheck[$yyN] == $yyToken
              ) {
                $yyState= self::$yyTable[$yyN];       // shift to yyN
                $yyVal= $yyLex->value;
                $yyToken= -1;
                $yyErrorFlag > 0 && $yyErrorFlag--;
                continue 2;
              }
        
              if (
                ($yyN= self::$yyRindex[$yyState]) != 0 && 
                ($yyN+= $yyToken) >= 0 && 
                $yyN < self::$yyTableCount && 
                self::$yyCheck[$yyN] == $yyToken
              ) {
                $yyN= self::$yyTable[$yyN];           // reduce (yyN)
              } else {
                switch ($yyErrorFlag) {
                  case 0: return $this->error(
                    E_PARSE, 
                    sprintf(
                      'Syntax error at %s, line %d (offset %d): Unexpected %s',
                      $yyLex->fileName,
                      $yyLex->position[0],
                      $yyLex->position[1],
                      $this->yyName($yyToken)
                    ), 
                    $this->yyExpecting($yyState)
                  );
                  
                  case 1: case 2: {
                    $yyErrorFlag= 3;
                    do { 
                      if (
                        ($yyN= @self::$yySindex[$yyStates[$yyTop]]) != 0 && 
                        ($yyN+= TOKEN_YY_ERRORCODE) >= 0 && 
                        $yyN < self::$yyTableCount && 
                        self::$yyCheck[$yyN] == TOKEN_YY_ERRORCODE
                      ) {
                        $yyState= self::$yyTable[$yyN];
                        $yyVal= $yyLex->value;
                        break 3;
                      }
                    } while ($yyTop-- >= 0);

                    throw new ParseError(E_ERROR, sprintf(
                      'Irrecoverable syntax error at %s, line %d (offset %d)',
                      $yyLex->fileName,
                      $yyLex->position[0],
                      $yyLex->position[1]
                    ));
                  }

                  case 3: {
                    if (0 == $yyToken) {
                      throw new ParseError(E_ERROR, sprintf(
                        'Irrecoverable syntax error at end-of-file at %s, line %d (offset %d)',
                        $yyLex->fileName,
                        $yyLex->position[0],
                        $yyLex->position[1]
                      ));
                    }

                    $yyToken = -1;
                    break 1;
                  }
                }
              }
            }

            $yyV= $yyTop+ 1 - self::$yyLen[$yyN];
            $yyVal= $yyV > $yyTop ? NULL : $yyVals[$yyV];

            // Actions
            switch ($yyN) {

    case 3:  #line 23 "src/resources/grammar/annotations.y"
    { $yyVal= array_merge($yyVals[-2+$yyTop], $yyVals[0+$yyTop]); } break;

    case 4:  #line 27 "src/resources/grammar/annotations.y"
    { $yyVal= array($yyVals[0+$yyTop] => TRUE); } break;

    case 5:  #line 28 "src/resources/grammar/annotations.y"
    {
        $yyVal= array($yyVals[-3+$yyTop] => $yyVals[-1+$yyTop]);
    } break;

    case 7:  #line 35 "src/resources/grammar/annotations.y"
    { $yyVal= trim($yyVals[0+$yyTop], '"\''); } break;

    case 10:  #line 41 "src/resources/grammar/annotations.y"
    { $yyVal= array_merge($yyVals[-2+$yyTop], $yyVals[0+$yyTop]); } break;

    case 11:  #line 45 "src/resources/grammar/annotations.y"
    { $yyVal= array($yyVals[0+$yyTop]); } break;

    case 12:  #line 46 "src/resources/grammar/annotations.y"
    { $yyVal= array($yyVals[-2+$yyTop] => $yyVals[0+$yyTop]); } break;

    case 13:  #line 47 "src/resources/grammar/annotations.y"
    { $yyVal= array($yyVals[-5+$yyTop] => $yyVals[-1+$yyTop]); } break;

    case 14:  #line 51 "src/resources/grammar/annotations.y"
    { switch (strtolower($yyVals[0+$yyTop])) {
        case 'true': $yyVal= TRUE; break;
        case 'false': $yyVal= FALSE; break;
        case 'null': $yyVal= NULL; break;
        default: $yyVal= $yyVals[0+$yyTop]; break;
    } } break;

    case 15:  #line 57 "src/resources/grammar/annotations.y"
    { $yyVal= trim($yyVals[0+$yyTop], '"\''); } break;

    case 16:  #line 58 "src/resources/grammar/annotations.y"
    { $yyVal= intval($yyVals[0+$yyTop]); } break;

    case 17:  #line 59 "src/resources/grammar/annotations.y"
    { $yyVal= floatval($yyVals[0+$yyTop]); } break;
#line 303 "-"
            }
                   
            $yyTop-= self::$yyLen[$yyN];
            $yyState= $yyStates[$yyTop];
            $yyM= self::$yyLhs[$yyN];

            if (0 == $yyState && 0 == $yyM) {
              $yyState= self::$yyFinal;

              // Check whether it's necessary to fetch the next token
              $yyToken < 0 && $yyToken= $yyLex->advance() ? $yyLex->token : 0;

              // We've reached the final token!
              if (0 == $yyToken) return $yyVal;
              continue 2;
            }

            $yyState= (
              ($yyN= self::$yyGindex[$yyM]) != 0 && 
              ($yyN+= $yyState) >= 0 && 
              $yyN < self::$yyTableCount && 
              self::$yyCheck[$yyN] == $yyState
            ) ? self::$yyTable[$yyN] : self::$yyDgoto[$yyM];
            continue 2;
          }
        }
      }
    }

  }
?>
