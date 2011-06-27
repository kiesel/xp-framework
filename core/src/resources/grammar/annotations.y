%{
  uses(
    'lang.AnnotationsLexer'
  );

%}

%token T_WORD     260
%token T_AT       261
%token T_STRING   307

%%

start:
    annotations { $$= $1; }
;

annotations:
    annotation { $$= $1; }
    | annotations ',' annotation { $$= array_merge($1, array($2)); }
;

annotation:
    T_AT name { $$= array($2 => TRUE); }
;

name:
    T_STRING { $$= $1; }
;

%%