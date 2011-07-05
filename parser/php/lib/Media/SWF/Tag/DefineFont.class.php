<?php
class Media_SWF_Tag_DefineFont extends Media_SWF_Tag
{
  public function parse($reader)
  {
    $this->_fields = array(
      'FontID' => $reader->getUI16LE(),
    );
    $this->reset($reader);
    parent::parse($reader);
  }
}
