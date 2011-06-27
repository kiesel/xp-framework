%{
  uses(
    'lang.AnnotationsLexer'
  );

%}

%token T_WORD     260
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
    '@' annotationname
;

annotationname:
    T_STRING { $$= $1; }
;

%%