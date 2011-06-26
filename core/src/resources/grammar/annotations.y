%{
  uses(
  );

%}

%token T_WORD 260

%%

start:
    annotations
;

annotations:
    annotation
    | annotations ',' annotation
;

annotation:
    '@' annotationname
;

annotationname:
    T_WORD { $$= $1 }
;

%%