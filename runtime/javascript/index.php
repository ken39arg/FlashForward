<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>FlashForward Sample SWF's</title>
  <link rel="stylesheet" href="http://code.jquery.com/mobile/1.0a4.1/jquery.mobile-1.0a4.1.min.css" />
</head>
<body>
<div data-role="page">
  <header data-role="header">
    <h1>Sample SWF's</h1>
  </header>
  <div data-role="content">
    <?php
    
    $dir = dirname(__FILE__) . '/swf';
    
    echo "<ul class='main' data-role='listview'>\n";
    foreach (glob($dir.'/*.swf') as $_swffile) {
      $swffile = str_replace(".swf", "", basename($_swffile));
      echo "<li><strong class='fname'>$swffile</strong>
      <ul class='sub'>
      <li><a href='swf/$swffile.swf' rel='external'>SWF(default)</a></li>
      <li><a href='player.html?f=$swffile&t=svg' rel='external'>HTML5(SVG)</a></li>
      <li><a href='player.html?f=$swffile&t=canvas' rel='external'>HTML5(canvas)</a></li>
      </ul>
      </li>\n";
    }
    echo "</ul>\n";
    
    ?>
  </div>
  <footer data-role="footer">
    <p>FlashForward</p>
  </footer>
</div>
<script src="http://code.jquery.com/jquery-1.5.2.min.js"></script>
<script src="http://code.jquery.com/mobile/1.0a4.1/jquery.mobile-1.0a4.1.min.js"></script>
</body>
</html>
