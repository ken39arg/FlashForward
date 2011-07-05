<?php
class Media_SWF_Tag_FrameLabel extends Media_SWF_Tag
{
  public function parse($reader)
  {
    $this->_fields = array(
      'Name' => $reader->getString(),
    );
    $this->reset($reader);
    parent::parse($reader);
  }
}
