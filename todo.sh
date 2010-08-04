#!/bin/bash
for fichero in `find . -name '*.php'`; do
	cat $fichero | grep -m1 "@todo" | sed -e 's:.*:--------- '$fichero' ---------:'
	cat -n $fichero | grep "@todo" | sed -e 's:^[ /t]*\([0-9]*\):linea \1:' -e 's:[ \t]*<!-- @todo \([0-9]\)\([ \ta-z]*\)-->:\tP\1\2:' -e 's:[ \t]*// @todo :\tP:' | sort -n
done

# TODO: Colorear

# IDEAS
#$ mkdir testcolor
#$ cd testcolor
#$ echo "LINE 001 START 00:01 END 00:11" > mylog
#$ echo "LINE 002 START 01:01 END 01:11" >> mylog
#$ echo "LINE 003 START 02:01 END 02:11" >> mylog
#$ echo "LINE 004 START 03:01 END 03:11" >> mylog
#$ echo "LINE 005 START 04:01 END 04:11" >> mylog
#$ cat mylog
#LINE 001 START 00:01 END 00:11
#LINE 002 START 01:01 END 01:11
#LINE 003 START 02:01 END 02:11
#LINE 004 START 03:01 END 03:11
#LINE 005 START 04:01 END 04:11
#$ cat mylog | sed ''/START/s//`printf "\033[32mSTART\033[0m"`/'' > mylog
#$ cat mylog
#LINE 001 START 00:01 END 00:11
#LINE 002 START 01:01 END 01:11
#LINE 003 START 02:01 END 02:11
#LINE 004 START 03:01 END 03:11
#LINE 005 START 04:01 END 04:11
#$
#View Original: http://www.unix.com/unix-dummies-questions-answers/134824-using-sed-change-specific-words-#color.html#ixzz0lkxUK682


