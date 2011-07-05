<?php
class Media_SWF_Viewer extends Media_SWF
{
  public function getVersion()
  {
    return $this->getHeader('Version');
  }

  public function getSize()
  {
    return $this->getHeader('FileLength');
  }

  public function getFrameRate()
  {
    return $this->getHeader('FrameRate') / 0x100;
  }

  public function getFrameCount()
  {
    return $this->getHeader('FrameCount');
  }

  public function getWidth()
  {
    $rect = $this->getHeader('FrameSize');
    return ($rect['Xmax'] - $rect['Xmin']) / 20;
  }

  public function getHeight()
  {
    $rect = $this->getHeader('FrameSize');
    return ($rect['Ymax'] - $rect['Ymin']) / 20;
  }

  public function getHumanizedDefines()
  {
    $defines = array();
    foreach ($this->_tags as $tag) {
      if (isset($tag['CharacterId'])) {
        $defines[] = array(
          'TagName'     => Media_SWF_Tag::name($tag['Code']),
          'Code'        => $tag['Code'],
          'CharacterId' => $tag['CharacterId'],
          'Length'      => $tag['Length'],
        );
      }
    }
    return $defines;
  }
}
