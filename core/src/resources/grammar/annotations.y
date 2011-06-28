%{
  uses(
    'lang.AnnotationsLexer'
  );

%}

%token T_WORD     260
%token T_STRING   307
%token T_LNUMBER  305
%token T_DNUMBER  306
%token T_CONSTANT_ENCAPSED_STRING 315
%token T_ARRAY    360
%token T_FALSE    500
%token T_TRUE     501
%token T_NULL     502

%%

start:
    annotations { $$= $1; }
;

annotations:
    annotation { $$= $1; }
    | annotations ',' annotation { $$= array_merge($1, $3); }
;

annotation:
    '@' name { $$= array($2 => TRUE); }
    | '@' name '(' values ')' {
        $$= array($2 => $4);
    }
;

name:
    T_STRING { $$= $1; }
    | T_CONSTANT_ENCAPSED_STRING { $$= trim($1, '"\''); }
;

values:
    value
    | value ',' { $$= $1; }
    | value ',' values { $$= array_merge($1, $3); }
;

value:
    scalar { $$= array($1); }
    | name '=' scalar { $$= array($1 => $3); }
    | name '=' T_ARRAY '(' values ')' { $$= array($1 => $5); }
;

scalar:
    T_STRING { $$= $1; }
    | T_FALSE { $$= FALSE; }
    | T_TRUE { $$= TRUE; }
    | T_NULL { $$= NULL; }
    | T_CONSTANT_ENCAPSED_STRING { $$= trim($1, '"\''); }
    | T_LNUMBER { $$= intval($1); }
    | T_DNUMBER { $$= floatval($1); }
;

%%