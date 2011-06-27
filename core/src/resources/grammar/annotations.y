%{
  uses(
    'lang.AnnotationsLexer'
  );

%}

%token T_WORD     260
%token T_STRING   307
%token T_CONSTANT_ENCAPSED_STRING 315

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
    | '@' name '(' value ')' {
        $$= array($2 => $4);
    }
    | '@' name '(' keyValues ')' {
        $$= array($2 => $4);
    }
;

name:
    T_STRING { $$= $1; }
;

value:
    T_STRING { $$= $1; }
    | T_CONSTANT_ENCAPSED_STRING { $$= trim($1, '"\''); }
;

keyValues:
    keyValue
    | keyValue ',' keyValues { $$= array_merge($1, $3); }
;

keyValue:
    name '=' value { $$= array($1 => $3); }
;

%%