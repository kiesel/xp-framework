##
# Makefile to generate parsers from grammar files
#
# $Id$

GEN=~/dev/svn/xp.public/trunk/ports/technologies/opt/jay

all: src/main/php/lang/AnnotationsParser.class.php

clean:
	-rm y.output

#syntax/xp/Parser.class.php: grammar/xp.jay $(GEN)/skel/php5.skl
#	sh $(GEN)/generate.sh grammar/xp.jay php5 "" xp.compiler.syntax.xp > $@

#syntax/php/Parser.class.php: grammar/php.jay $(GEN)/skel/php5.skl
#	sh $(GEN)/generate.sh grammar/php.jay php5 "" xp.compiler.syntax.php > $@

src/main/php/lang/AnnotationsParser.class.php: src/resources/grammar/annotations.y $(GEN)/skel/php5.skl
	sh $(GEN)/generate.sh src/resources/grammar/annotations.y php5 "" xp.compiler.syntax.xp > $@
