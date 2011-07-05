<?php
class Media_SWF_Tag_DefineSprite extends Media_SWF_Tag_DisplayObjectContainer
{
  protected
    $type = 'sprite';

  public function parse($reader)
  {
    $this->_fields = array(
      'CharacterId' => $reader->getUI16LE(),
      'FrameCount'  => $reader->getUI16LE(),
    );
    parent::parse($reader);
  }

  public function build()
  {
    $writer = new Media_SWF_Parser();
    $writer->putUI16LE($this->_fields['CharacterId']);
    $writer->putUI16LE($this->_fields['FrameCount']);
    foreach ($this->_tags as $tag) {
      $tag->write($writer);
    }
    return $writer->output();
  }

  public function convertArray()
  {
    return array(
      'meta'       => $this->getMetaArray(),
      //'dict'     => $this->getDictionaryArray(),
      'ctls'       => $this->getControlsArray(),
    );
  }

  public function getMetaArray()
  {
    return array(
      'cid'  => $this->getElementIdString(),
      'fcon' => $this->getField('FrameCount'),
    );
  }

  public function getElementSavedUrl()
  {
    return "defines/".$this->getGroupName()."char.json";
  }

}
