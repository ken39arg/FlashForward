<?php
class Media_SWF_Tag_DefineButton2 extends Media_SWF_Tag
{
  public function parse($reader)
  {
    $this->_fields['CharacterId'] = $reader->getUI16LE(); // CharacterId
    $this->reset($reader);
    parent::parse($reader);
  }
  public function getDictionaryArray()
  {
    return false;
  }
}
