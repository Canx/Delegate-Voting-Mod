#!/bin/bash
##################################################
# Coloreador de todo's, by Canx
#
# El formato de los comentarios ha de ser del tipo
# <!-- @todo P<1|2|3> <comentario> -->
# o bien 
# // @todo P<1|2|3> <comentario> 
###################################################
#
# @todo 1 No ordena bien las lineas a partir de ficheros de 100 lineas
# @todo 1 No formatea bien los comentarios del tipo # @todo ...

for fichero in `find . -regex ".*\.\(php\|xml\)"`; do
	cat $fichero | grep -m1 "@todo" | sed -e 's:.*:'`printf "\033[37m"`'--- '$fichero' ---'`printf "\033[0m"`' :'
	cat -n $fichero | grep "@todo" | sed -e 's:^[ \t]*\([0-9]*\):linea \1:' -e 's:[ \t]*<!-- @todo \([0-9]\)\(.*\)-->:\tP\1\2:' -e 's:[ \t]*// @todo :\tP:' | sort -n | sed -e 's/.*P1.*/'`printf "\033[31m"`'&'`printf "\033[0m"`'/' | sed -e 's/.*P2.*/'`printf "\033[33m"`'&'`printf "\033[0m"`'/' | sed -e 's/.*P3.*/'`printf "\033[34m"`'&'`printf "\033[0m"`'/'
done
