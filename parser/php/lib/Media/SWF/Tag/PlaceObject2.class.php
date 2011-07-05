<?php

class Media_SWF_Tag_PlaceObject2 extends Media_SWF_Tag
{
  public function isDisplayListTag()
  {
    return true;
  }

  public function parse($reader)
  {
    $fields = array();
    $placeFlag = $reader->getUI8();
    $fields['PlaceFlag'] = $placeFlag;
    $fields['Depth'] = $reader->getUI16LE();
    //if ($placeFlag & 0x01) {} // PlaceFlagMove 
    if ($placeFlag & 0x02) { // PlaceFlagHasCharacter
      $fields['CharacterId'] = $reader->getUI16LE();
    }
    if ($placeFlag & 0x04) { // PlaceFlagHasMatrix 
      $fields['Matrix'] = $reader->getMatrix();
    }
      
    if ($placeFlag & 0x08) { // PlaceFlagHasColorTransform 
      $fields['ColorTransform'] = $reader->getColorTransformWithAlpha();
    }
    if ($placeFlag & 0x10) { // PlaceFlagHasRatio 
      $fields['Ratio'] = $reader->getUI16LE();
    }
    if ($placeFlag & 0x20) { // PlaceFlagHasName 
      $fields['Name'] = $reader->getString();
    }
    if ($placeFlag & 0x40) { // PlaceFlagHasClipDepth 
      $fields['ClipDepth'] = $reader->getUI16LE();
    }
    if ($placeFlag & 0x80) { // PlaceFlagHasClipActions 
      $fields['ClipActions'] = $this->getRest($reader);
    }
    $this->_fields = $fields;
  }

  public function build()
  {
    $writer = new Media_SWF_Parser();
    $writer->putUI8($this->_fields['PlaceFlag']);
    $writer->putUI16LE($this->_fields['Depth']);
    if (isset($this->_fields['CharacterId'])) {
      $writer->putUI16LE($this->_fields['CharacterId']);
    }
    if (isset($this->_fields['Matrix'])) {
      $writer->putMatrix($this->_fields['Matrix']);
    }
    if (isset($this->_fields['ColorTransform'])) {
      $writer->putColorTransformWithAlpha($this->_fields['ColorTransform']);
    }
    if (isset($this->_fields['Ratio'])) {
      $writer->putUI16LE($this->_fields['Ratio']);
    }
    if (isset($this->_fields['Name'])) {
      $writer->putString($this->_fields['Name']);
    }
    if (isset($this->_fields['ClipDepth'])) {
      $writer->putUI16LE($this->_fields['ClipDepth']);
    }
    if (isset($this->_fields['ClipActions'])) {
      $writer->putData($this->_fields['ClipActions']);
    }
    return $writer->output();
  }

}
