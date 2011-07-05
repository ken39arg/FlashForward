<?php
class Media_SWF_Tag_SetBackgroundColor extends Media_SWF_Tag
{
  public function parse($reader)
  {
    $this->_fields['BackgroundColor'] = $reader->getRGB();
    $this->reset($reader);
    parent::parse($reader);
  }
}
