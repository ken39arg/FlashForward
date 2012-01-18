<?php
// Export用あまり使う必要は無いかもしれないとおもう
// DefineBitsLossless(20)
// DefineBitsLossless2(20)
class Media_SWF_Tag_DefineBitsLossless extends Media_SWF_Tag
{
  protected
    $type = 'bitmap';

  public 
    $filetype = 'png';

  public function getElementSavedUrl()
  {
    return "defines/".$this->getGroupName().$this->getElementIdString().'.'.$this->filetype;
  }

  public function parse($reader)
  {
    $start = $reader->getByteOffset();
    $this->_fields = array(
      'CharacterId'  => $reader->getUI16LE(), // ShapeId
      'BitmapFormat' => $reader->getUI8(),
      'BitmapWidth'  => $reader->getUI16LE(),
      'BitmapHeight' => $reader->getUI16LE(),
    );
    if ($this->_fields['BitmapFormat'] == 3) {
      $this->_fields['BitmapColorTableSize'] = $reader->getUI8();
    }
    $this->_fields['ZlibBitmapData'] = $this->getRest($reader);
  }

  public function convertSVG()
  {
    $image_data = $this->convertImageData();
    return Media_SVG::newElement('image')
            ->set('id', $this->getElementIdString())
            ->set('width',  $this->getField('BitmapWidth'))
            ->set('height', $this->getField('BitmapHeight'))
            ->set('href', 'data:image/png;base64,'.base64_encode($image_data));
  }

  public function build()
  {
    $writer = new Media_SWF_Parser();
    $writer->putUI16LE($this->_fields['CharacterId']);
    $writer->putUI8($this->_fields['BitmapFormat']);
    $writer->putUI16LE($this->_fields['BitmapWidth']);
    $writer->putUI16LE($this->_fields['BitmapHeight']);
    if ($this->_fields['BitmapFormat'] == 3) {
      $writer->putUI8($this->_fields['BitmapColorTableSize']);
    }
    $writer->putData($this->_fields['ZlibBitmapData']);
    return $writer->output();
  }

  public function saveAsImage($filename)
  {
    $image_data = $this->convertImageData();
    file_put_contents($filename, $image_data);
  }

  public function convertImageData()
  {
    $format = (int) $this->getField('BitmapFormat');
    $width  = (int) $this->getField('BitmapWidth');
    $height = (int) $this->getField('BitmapHeight');
    $bitmapdata = gzuncompress($this->getField('ZlibBitmapData'));
    $bitmapReader = new Media_SWF_Parser();
    $bitmapReader->input($bitmapdata);

    $im = imagecreatetruecolor($width, $height);
    if ($im === false) {
      throw new Exception('Cannot Initialize new GD image stream.');
    }

    $color_palette = array();
    if ($format == 3) {
      // color palette
      $palette_size = (int) $this->getField('BitmapColorTableSize');
      if ($this->code == 20) {
        for ($i = 0; $i <= $palette_size; $i++) {
          $r = $bitmapReader->getUI8();
          $g = $bitmapReader->getUI8();
          $b = $bitmapReader->getUI8();
          $color_palette[$i] = imagecolorallocate($im, $r, $g, $b);
        }
      } else {
        // alpha
        imagealphablending($im, false);
        imagesavealpha($im, true);
        for ($i = 0; $i <= $palette_size; $i++) {
          $r = $bitmapReader->getUI8();
          $g = $bitmapReader->getUI8();
          $b = $bitmapReader->getUI8();
          $a = $bitmapReader->getUI8();
          $a = (1 - $a / 255) * 127;
          $color_palette[$i] = imagecolorallocatealpha($im, $r, $g, $b, $a);
        }
      }
      // widthの読み出しbyte数を32bitで丸める(must be rounded up to the next 32-bit word boundary)
      $padding = (($width + 3) & -4) - $width;
      for ($y = 0; $y < $height; ++$y) {
        for ($x = 0; $x < $width; ++$x) {
          $bi = $bitmapReader->getUI8();
          imagesetpixel($im, $x, $y, $color_palette[$bi]);
        }
        $bi = $bitmapReader->incrementOffset($padding, 0); // skip
      }
    } else {
      // non parette
      if ($format == 4)  {
        // PIX15
        for ($y = 0; $y < $height; ++$y) {
          for ($x = 0; $x < $width; ++$x) {
            $a = $bitmapReader->getUIBit(); // Pix15Reserved always 0
            $r = $bitmapReader->getUIBits(5);
            $g = $bitmapReader->getUIBits(5);
            $b = $bitmapReader->getUIBits(5);
            $c = imagecolorallocate($im, $r, $g, $b);
            imagesetpixel($im, $x, $y, $c);
          }
        }
      } elseif ($this->code == 20) {
        // PIX24
        for ($y = 0; $y < $height; ++$y) {
          for ($x = 0; $x < $width; ++$x) {
            $a = $bitmapReader->getUI8(); // 0 // Pix24Reserved always0
            $r = $bitmapReader->getUI8();
            $g = $bitmapReader->getUI8();
            $b = $bitmapReader->getUI8();
            $c = imagecolorallocate($im, $r, $g, $b);
            imagesetpixel($im, $x, $y, $c);
          }
        }
      } else {
        // alpha
        imagealphablending($im, false);
        imagesavealpha($im, true);
        for ($y = 0; $y < $height; ++$y) {
          for ($x = 0; $x < $width; ++$x) {
            $a = $bitmapReader->getUI8();
            $a = (1 - $a / 255) * 127;

            $r = $bitmapReader->getUI8();
            $g = $bitmapReader->getUI8();
            $b = $bitmapReader->getUI8();

            $c = imagecolorallocatealpha($im, $r, $g, $b, $a);
            imagesetpixel($im, $x, $y, $c);
          }
        }
      }
    }

    ob_start();
    imagepng($im);
    $image_data = ob_get_contents();
    ob_end_clean();

    imagedestroy($im);

    return $image_data;
  }

  public function getDictionaryArray()
  {
    return array_merge(parent::getDictionaryArray(), array(
      'width'  => $this->getField('BitmapWidth'),
      'height' => $this->getField('BitmapHeight'),
    ));
  }

}
