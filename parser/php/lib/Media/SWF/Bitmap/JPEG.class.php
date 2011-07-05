<?php
require_once 'IO/SWF/JPEG.php';
class Media_SWF_Bitmap_JPEG extends Media_SWF_Bitmap
{
  public function build()
  {
    $jpegdata = file_get_contents($this->image_file);
    
    $swf_jpeg = new IO_SWF_JPEG();
    $swf_jpeg->input($jpegdata);
    $jpeg_table = $swf_jpeg->getEncodingTables();
    $jpeg_image = $swf_jpeg->getImageData();

    // アルファには対応しません
    $this->code = 21;
    $this->content = $jpeg_table.$jpeg_image;
  }
}
