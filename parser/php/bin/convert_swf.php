#! /usr/bin/env php
<?php

$VERSION = "v0.0.1";
$USAGE = <<< __

SWFを中間コードJSON,SVG,JPEG,PNGに変換します。

変換したデータの集合は指定したディレクトリ上で下記の構成で展開されます
    dir/
      - index.json
      - defines/
          - *char.json   -- おもにSpriteと動的Text
          - *char.svgz   -- おもにShapeと静的Text
          - *font.svgz   -- 埋め込みフォント
          - *bitmap.jpeg -- JPEGデータ
          - *bitmap.png  -- PNGデータ(GIFもPNGに変換されます)

Usage: $ /usr/bin/env php {$argv[0]} <option> [swfname] [output directory]

Options: 
  --help, -h, -?                 ヘルプを表示します
  --version, -v                  バージョンを表示します
  --compress, -c                 圧縮オプションを使います(SVGをzlib圧縮してsvgzとします) 
  --compact, --minimum, -m       ファイル最小化オプションを使います(ムービークリップによらず可能な限りファイル数を最小にします)
  --swf, -f                      変換対象のSWFファイル名(パラメータでも構いません)
  --output-dir, --save-dir, -o   保存先ディレクトリ(パラメータでも構いません)

Arguments:
  swfname                        変換対象のSWFファイル名(Optionでも構いません)
  output directory               保存先ディレクトリ(Optionでも構いません)


__;

{ // require
  set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__) . '/../lib');
  require_once 'Media/SWF.class.php';
  require_once 'Media/SWF/Parser.class.php';
  require_once 'Media/SWF/Tag.class.php';
  require_once 'Media/SWF/Tag/DisplayObjectContainer.class.php';
  require_once 'Media/SWF/Tag/Stage.class.php';
  require_once 'Media/SWF/Tag/DefineShape.class.php';
  require_once 'Media/SWF/Tag/DefineShape2.class.php';
  require_once 'Media/SWF/Tag/DefineShape3.class.php';
  require_once 'Media/SWF/Tag/DefineSprite.class.php';
  require_once 'Media/SWF/Tag/DoAction.class.php';
  require_once 'Media/SWF/Tag/PlaceObject.class.php';
  require_once 'Media/SWF/Tag/PlaceObject2.class.php';
  require_once 'Media/SWF/Tag/RemoveObject.class.php';
  require_once 'Media/SWF/Tag/RemoveObject2.class.php';
  require_once 'Media/SWF/Tag/DefineBitsLossless.class.php';
  require_once 'Media/SWF/Tag/DefineBitsLossless2.class.php';
  require_once 'Media/SWF/Tag/DefineBits.class.php';
  require_once 'Media/SWF/Tag/DefineBitsJPEG2.class.php';
  require_once 'Media/SWF/Tag/SetBackgroundColor.class.php';
  require_once 'Media/SWF/Tag/FrameLabel.class.php';
  require_once 'Media/SWF/Tag/DefineFont.class.php';
  require_once 'Media/SWF/Tag/DefineFont2.class.php';
  require_once 'Media/SWF/Tag/DefineFontName.class.php';
  require_once 'Media/SWF/Tag/DefineText.class.php';
  require_once 'Media/SWF/Tag/DefineEditText.class.php';
  require_once 'Media/SWF/Tag/DefineButton2.class.php';
  require_once 'Media/SWF/Converter.class.php';
  require_once 'Media/SWF/SVGUtil.class.php';
  require_once 'Media/SVG/Element.class.php';
  require_once 'Media/SVG/Path.class.php';
  require_once 'Media/SVG/Null.class.php';
  require_once 'Media/SVG.class.php';
}

$swfname = $output = null;
$saveWithCompress     = false;
$useCompactSaveMode   = false;

for ($i = 0; $i < $argc; ++$i) {
  if ($i === 0) {
    continue;
  }
  switch ($argv[$i]) {
    case "-v":
    case "--version":
      echo $argv[0]." ".$VERSION.PHP_EOL;
      exit(0);
    case "-?":
    case "-h":
    case "--help":
      echo $USAGE;
      exit(0);
    case "-f":
    case "--swf":
      $swfname = $argv[++$i];
      break;
    case "-o":
    case "--output-dir":
    case "--save-dir":
      $output = $argv[++$i];
      break;
    case "-c":
    case "--compress":
      $saveWithCompress = true;
      break;
    case "--compact":
    case "--minimum":
    case "-m":
      $useCompactSaveMode = true;
      break;
    default:
      if ($argv[$i]{0} === "-") {
        echo "Unknown Option. \"".$argv[$i]."\"\n";
        exit(1);
      }
      if ($swfname === null) {
        $swfname = $argv[$i];
      } elseif ($output === null) {
        $output = $argv[$i];
      }
      break;
  }
}

if ($swfname === null) {
  echo "Use --swfname option\n";
  exit(1);
}

if ($output === null) {
  echo "Use --output option\n";
  exit(1);
}

if (!file_exists($swfname)) {
  echo "Input file is not found. \"".$swfname."\"\n";
  exit(1);
}

try {
  $editor = new Media_SWF_Converter();
  $editor->parse(file_get_contents($swfname));
  $editor->stage->saveWithCompress = $saveWithCompress;
  $editor->stage->useCompactSaveMode = $useCompactSaveMode;
  $editor->saveDeliveryContent($output);
} catch (Exception $e) {
  die($swfname." is error '{$e->getMessage()}'");
}
