<?php
class Media_SWF_Tag_RemoveObject2 extends Media_SWF_Tag
{
  public function isDisplayListTag()
  {
    return true;
  }

  public function parse($reader)
  {
    $this->_fields = array(
      'Depth'       => $reader->getUI16LE(),
    );
  }

  public function build()
  {
    $writer = new IO_Bit();
    $writer->putUI16LE($this->_fields['Depth']);
    return $writer->output();
  }
}
