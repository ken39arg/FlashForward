<?php
// Export用あまり使う必要は無いかもしれないとおもう
// DefineBits(6)
// DefineBitsJPEG2(21)
class Media_SWF_Tag_DefineBits extends Media_SWF_Tag
{
  protected
    $width  = 0, 
    $height = 0,
    $type = 'bitmap';

  public 
    $filetype;

  public function getElementSavedUrl()
  {
    return "defines/".$this->getGroupName().$this->getElementIdString().'.'.$this->filetype;
  }

  public function parse($reader)
  {
    $this->_fields = array(
      'CharacterId'  => $reader->getUI16LE(), // ShapeId
      'ImageData'    => $reader->getData($this->length - 2),
    );
    $image_data = $this->getField('ImageData');
    switch (ord($image_data{0})) {
      case 0xff: $this->filetype = 'jpg'; break; // JPEG SOI 0xff 0xD9
      case 0x89: $this->filetype = 'png'; break; // PNG 0x89 0x50 0x4E 0x47 0x0D 0x0A 0x1A 0x0A
      case 0x47: $this->filetype = 'gif'; break; // GIF89a 0x47 0x49 0x46 0x38 0x39 0x61
    }
    $i = ($this->filetype == 'jpg' && ord($image_data{2}) == 0xff && ord($image_data{3}) == 0xD8) 
       ? imagecreatefromstring(substr($image_data, 4))
       : imagecreatefromstring($image_data);
    $this->width  = imagesx($i);
    $this->height = imagesy($i);
    imagedestroy($i);
  }

  public function build()
  {
    $writer = new Media_SWF_Parser();
    $writer->putUI16LE($this->_fields['CharacterId']);
    $writer->putRect($this->_fields['ImageData']);
    return $writer->output();
  }

  public function saveAsImage($filename)
  {
    $image_data = $this->convertImageData();
    file_put_contents($filename, $image_data);
  }

  public function convertSVG()
  {
    $image_data = $this->convertImageData();
    return Media_SVG::newElement('image')
            ->set('id', $this->getElementIdString())
            ->set('width',  $this->width)
            ->set('height', $this->height)
            ->set('href', 'data:image/'.$this->filetype.';base64,'.base64_encode($image_data));
  }

  public function convertImageData()
  {
    $image_data = $this->getField('ImageData');
    if ($this->filetype == 'jpg') {
      if (ord($image_data{2}) == 0xff && ord($image_data{3}) == 0xD8) {
        // before version 8 erroneous header
        $image_data = substr($image_data, 4);
      }
    }
    return $image_data;
  }

  public function getDictionaryArray()
  {
    return array_merge(parent::getDictionaryArray(), array(
      'width'  => $this->width,
      'height' => $this->height,
    ));
  }
}
