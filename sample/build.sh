#! /bin/sh
mine=`dirname $0`
root=`dirname $mine`
cmd="cd $root"
echo $cmd
$cmd

swfs=`find sample/swf -name '*.swf'`
for swf in $swfs
do
  n=`echo $swf | awk -F"/" '{print $3}' | awk -F".swf" '{print $1}'`
  cmd="php parser/php/bin/convert_swf.php --compress $swf sample/ff/$n"
  echo $cmd
  $cmd
done
echo "Finish!"
