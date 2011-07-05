<?php
class Media_SWF_Bitmap
{
  protected 
    $code,
    $content,
    $image_file;

  public function __construct($image_file = null)
  {
    $this->setImageFile($image_file);
  }

  public function setImageFile($image_file)
  {
    $this->image_file = $image_file;
  }

  public function build() {}

  public function getTag($characterId)
  {
    return array('Code'        => $this->code, 
                 'Length'      => strlen($this->content) + 2, 
                 'CharacterId' => $characterId, 
                 'Content'     => $this->content);
  }

  public function getContent()
  {
    return $this->content;
  }

  public function getCode()
  {
    return $this->code;
  }
}
