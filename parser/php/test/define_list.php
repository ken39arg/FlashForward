<html>
<?php
require_once 'include.php';
require_once 'Media/SWF/Converter.class.php';
require_once 'Media/SWF/Viewer.class.php';
$dir = dirname(__FILE__) . '/swf';
$filename = @$_GET['d'];
$c = isset($_GET['c']) ? $_GET['c'] : '#cccccc';

$editor = new Media_SWF_Viewer();
$editor->parse(file_get_contents($dir.'/'.$filename));
?>
<head>
<title><?php echo $filename ?> | Defines</title>
</head>
<body>
<h1><?php echo $filename ?></h1>
<h2>Info</h2>

<table>
<tr><th>Version</th><td><?php echo $editor->getVersion() ?></td></tr>
<tr><th>FileSize</th><td><?php echo $editor->getSize() ?></td></tr>
<tr><th>Width x Height</th><td><?php echo $editor->getWidth() . 'x' . $editor->getHeight() ?></td></tr>
<tr><th>FrameRate</th><td><?php echo $editor->getFrameRate() ?></td></tr>
<tr><th>FrameCount</th><td><?php echo $editor->getFrameCount() ?></td></tr>
</table>

<h2>Defines List</h2>

<table>
  <tr><th>CharacterId</th><th>TagName(Code)</th><th>ImageSample</th><th>Link</th></tr>
<?php

foreach ($editor->getHumanizedDefines() as $define) {
  $id = $define['CharacterId'];
  $name = $define['TagName'];
  $code = $define['Code'];
  echo "  <tr>"
     . "    <td><a href='define_to_api.php?d={$filename}&id={$id}'>{$id}</a></td>"
     . "    <td>$name($code)</td>"
     . "    <td bgcolor='$c'><img src='define_to_image.php?d={$filename}&id={$id}' /></td>"
     . "  </tr>";
  echo PHP_EOL;
}

?>
</table>
</body>
</html>
