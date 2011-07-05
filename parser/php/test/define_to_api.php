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
$id = (int) @$_GET['id'];

if ($swfname == null || !file_exists($dir.'/'.$swfname)) {
  throw new Exception('file not exists '.$swfname);
}

if (!$id) {
  throw new Exception('id require');
}

$editor = new Media_SWF_Converter();
$editor->parse(file_get_contents($dir.'/'.$swfname));

$tag = $editor->stage->getTagByCharacterId($id);

if ($tag instanceof Media_SWF_Tag_DefineBits) {
  header("Content-type: image/".$tag->filetype);
  echo $tag->convertImageData();
} elseif ($tag instanceof Media_SWF_Tag_DefineBitsLossless) {
  header("Content-type: image/png");
  echo $tag->convertImageData();
} elseif ($tag instanceof Media_SWF_Tag_DefineShape) {
  header("Content-type: image/svg+xml");
  echo $tag->saveAsSVG();
} elseif ($tag instanceof Media_SWF_Tag_DefineSprite) {
  echo json_encode($tag->convertArray());
}

