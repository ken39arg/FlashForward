<?php
class Media_SWF_Tag_RemoveObject extends Media_SWF_Tag
{
  public function isDisplayListTag()
  {
    return true;
  }

  public function parse($content)
  {
    $this->_fields = array(
      'CharacterId' => $reader->getUI16LE(),
      'Depth'       => $reader->getUI16LE(),
    );
  }

  public function build()
  {
    $writer = new IO_Bit();
    $writer->putUI16LE($this->_fields['CharacterId']);
    $writer->putUI16LE($this->_fields['Depth']);
    return $writer->output();
  }
}
