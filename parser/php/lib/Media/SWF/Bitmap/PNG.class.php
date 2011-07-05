<?php
class Media_SWF_Bitmap_PNG extends Media_SWF_Bitmap
{
  public function build()
  {
    $im = imagecreatefrompng($this->image_file);
    
    if ($im === false) {
      throw new Exception($this->image_file.' is not png file.');
    }
    
    $is_alpha = false;

    $width  = imagesx($im);
    $height = imagesy($im);

    $bitmap_data = array();

    for ($y = 0; $y < $height; ++$y) {
      for ($x = 0; $x < $width; ++$x) {
        $rgb = imagecolorsforindex($im, imagecolorat($im, $x, $y));
        if ($rgb['alpha'] > 0) {
          $is_alpha = true;
        }
        $alpha = (127 - $rgb['alpha']) / 127 * 255;
        if ($alpha == 255) {
          $bitmap_data[] = array(
            'Alpha' => 255,
            'Red'   => $rgb['red'],
            'Green' => $rgb['green'],
            'Blue'  => $rgb['blue'],
          );
        } else {
          $bitmap_data[] = array(
            'Alpha' => round($alpha),
            'Red'   => round($rgb['red'] * $alpha / 255),
            'Green' => round($rgb['green'] * $alpha / 255),
            'Blue'  => round($rgb['blue'] * $alpha / 255),
          );
        }
      }
    }

    $bitmap = '';
    if ($is_alpha) {
      foreach ($bitmap_data as $rgb) {
        if ($rgb['Alpha'] == 0) {
          $bitmap .= chr(0);
          $bitmap .= chr(0);
          $bitmap .= chr(0);
          $bitmap .= chr(0);
        } else {
          $bitmap .= chr($rgb['Alpha']);
          $bitmap .= chr($rgb['Red']);
          $bitmap .= chr($rgb['Green']);
          $bitmap .= chr($rgb['Blue']);
        }
      }
    } else {
      foreach ($bitmap_data as $rgb) {
        $bitmap .= chr(0);
        $bitmap .= chr($rgb['Red']);
        $bitmap .= chr($rgb['Green']);
        $bitmap .= chr($rgb['Blue']);
      }
    }

    $format = 5; // palette format
    $content =  chr($format).pack('v', $width).pack('v', $height);
    $content .= gzcompress($bitmap);
    
    if ($is_alpha) {
      $this->code = 36; // DefineBitsLossless2
    } else {
      $this->code = 20; // DefineBitsLossless
    }
    $this->content = $content;
  }
}
