<?php
// via http://labs.gree.jp/blog/2010/10/1263/
class Media_SWF_Bitmap_GIF extends Media_SWF_Bitmap
{
  public function build()
  {
    $im = imagecreatefromgif($this->image_file);
    
    if ($im === false) {
      throw new Exception($this->image_file.' is not gif file.');
    }
    
    $colortable_num = imagecolorstotal($im);
    $transparent_index = imagecolortransparent($im);
    $colortable = '';
    
    if ($transparent_index < 0) {
      for ($i = 0; $i < $colortable_num; ++$i) {
        $rgb = imagecolorsforindex($im, $i);
        $colortable .= chr($rgb['red']);
        $colortable .= chr($rgb['green']);
        $colortable .= chr($rgb['blue']);
      }
    } else {
      for ($i = 0; $i < $colortable_num; ++$i) {
        $rgb = imagecolorsforindex($im, $i);
        $colortable .= chr($rgb['red']);
        $colortable .= chr($rgb['green']);
        $colortable .= chr($rgb['blue']);
        $colortable .= ($i == $transparent_index) ? chr(0) : chr(255);
      }
    }
    
    $pixeldata = '';
    $i = 0;
    $width  = imagesx($im);
    $height = imagesy($im);
    
    for ($y = 0; $y < $height; ++$y) {
      for ($x = 0; $x < $width; ++$x) {
        $pixeldata .= chr(imagecolorat($im, $x, $y));
        $i++;
      }
      while (($i % 4) != 0) {
        $pixeldata .= chr(0);
        $i++;
      }
    }
    
    $format = 3; // palette format
    $content =  chr($format).pack('v', $width).pack('v', $height);
    $content .= chr($colortable_num - 1).gzcompress($colortable.$pixeldata);
    
    if ($transparent_index < 0) {
      $this->code = 20; // DefineBitsLossless
    } else {
      $this->code = 36; // DefineBitsLossless2
    }
    $this->content = $content;
  }
}
