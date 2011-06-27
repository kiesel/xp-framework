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
    annotations
;

annotations:
    annotation
    | annotations ',' annotation
;

annotation:
    T_AT name
;

name:
    T_STRING { $$= $1; }
;

%%