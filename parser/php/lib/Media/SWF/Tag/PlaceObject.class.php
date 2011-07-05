<?php
class Media_SWF_Tag_PlaceObject extends Media_SWF_Tag
{
  public function isDisplayListTag()
  {
    return true;
  }


  public function parse($reader)
  {
    $this->_fields = array(
      'CharacterId'    => $reader->getUI16LE(),
      'Depth'          => $reader->getUI16LE(),
      'Matrix'         => $reader->getMatrix(),
    );
    if ($this->length > $reader->getByteOffset()) {
      $this->_fields['ColorTransform'] = $reader->getColorTransform();
    }
  }

  public function build()
  {
    $writer = new Media_SWF_Parser();
    $writer->putUI16LE($this->_fields['CharacterId']);
    $writer->putUI16LE($this->_fields['Depth']);
    $writer->putMatrix($this->_fields['Matrix']);
    if (isset($this->_fields['ColorTransform'])) {
      $writer->putColorTransform($this->_fields['ColorTransform']);
    }
    return $writer->output();
  }

}
