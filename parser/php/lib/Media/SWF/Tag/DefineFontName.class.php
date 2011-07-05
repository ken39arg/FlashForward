<?php
class Media_SWF_Tag_DefineFontName extends Media_SWF_Tag
{
  protected
    $type     = 'font';

  public function parse($reader)
  {
    $this->_fields = array(
      'CharacterId'   => $reader->getUI16LE(), //FontId
      'FontName'      => $reader->getString(),
      'FontCopyright' => $reader->getString(),
    );
    $this->reset($reader);
    parent::parse($reader);
  }

  public function getDictionaryArray()
  {
    return false;
  }

  public function getFontName()
  {
    //return $this->root->convertEncoding($this->getField('FontName'));
    return $this->getField('FontName');
  }

  public function getFontCopyright()
  {
    //return $this->root->convertEncoding($this->getField('FontCopyright'));
    return $this->getField('FontCopyright');
  }
}
