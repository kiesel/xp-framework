%{
  uses(
    'lang.AnnotationsLexer'
  );

%}

%token T_STRING   307
%token T_LNUMBER  305
%token T_DNUMBER  306
%token T_CONSTANT_ENCAPSED_STRING 315
%token T_ARRAY    360

%%

start:
    annotations
;

annotations:
    annotation
    | annotations ',' annotation { $$= array_merge($1, $3); }
;

annotation:
    '@' name { $$= array($2 => NULL); }
    | '@' name '(' values ')' {
        $$= array($2 => $4);
    }
;

name:
    T_STRING
    | T_CONSTANT_ENCAPSED_STRING { $$= stripcslashes(trim($1, '"\'')); }
;

values:
    value
    | value ','
    | value ',' values { $$= array_merge((array)$1, (array)$3); }
;

value:
    scalar
    | name '=' scalar { $$= array($1 => $3); }
    | name '=' T_ARRAY '(' values ')' { $$= array($1 => $5); }
;

scalar:
    T_STRING { switch (strtolower($1)) {
        case 'true': $$= TRUE; break;
        case 'false': $$= FALSE; break;
        case 'null': $$= NULL; break;
        default: $$= $1; break;
    } }
    | T_CONSTANT_ENCAPSED_STRING { $$= stripcslashes(trim($1, '"\'')); }
    | T_LNUMBER { $$= intval($1); }
    | T_DNUMBER { $$= floatval($1); }
;

%%