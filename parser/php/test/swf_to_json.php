<?php
require_once 'include.php';
require_once 'Media/SWF/Converter.class.php';

require_once 'Media/SWF/SVGUtil.class.php';
require_once 'Media/SVG/Element.class.php';
require_once 'Media/SVG/Path.class.php';
require_once 'Media/SVG/Null.class.php';
require_once 'Media/SVG.class.php';

$dir = dirname(__FILE__) . '/swf';

$swfname = @$_GET['d'];

if ($swfname == null || !file_exists($dir.'/'.$swfname)) {
  throw new Exception('file not exists '.$swfname);
}

$editor = new Media_SWF_Converter();
$editor->parse(file_get_contents($dir.'/'.$swfname));
//header("Content-type: text/json");
echo $editor->toJSON();
