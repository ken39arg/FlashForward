<?php
/**
 * Media_SWF 
 * 
 * IO_SWFからMedia_SWFにして用途にあわせて拡張した
 *
 * @package   Media_SWF 
 * @version   $Id$
 * @copyright Copyright (C) 2010 KAYAC Inc.
 * @author    Kensaku Araga <araga-kensaku@kayac.com> 
 * @via http://openpear.org/package/IO_SWF (@yoya)
 */
class Media_SWF // extends IO_SWF
{
  protected 
    $_headers = array(),
    $_tags    = array();

  public function parse($swfdata) 
  {
    $reader = new Media_SWF_Parser();
    $reader->input($swfdata);

    /* SWF Header */
    $this->_headers = $reader->getSWFHeader();
    
    /* SWF Tags */
    while (true) {
      $tag = $reader->getTag();
      $this->_tags[] = $tag;
      if ($tag['Code'] == 0) { // END Tag
        break;
      }
    }
    return true;
  }
  
  public function build() 
  {
    $writer = new Media_SWF_Parser();
    $writer->putSWFHeader($this->_headers);
    
    /* SWF Tags */
    foreach ($this->_tags as $tag) {
      $writer->putTag($tag);
    }
    $fileLength = $writer->getByteOffset();
    $this->_headers['FileLength'] = $fileLength;
    $writer->setFileLength($fileLength);
    return $writer->output();
  }

  public function getHeader($name)
  {
    return isset($this->_headers[$name]) ? $this->_headers[$name] : null;
  }

  public function getFirstAction()
  {
    foreach ($this->_tags as &$tag) {
      if ($tag['Code'] === 12) {
        if (!isset($tag['Object'])) {
          $tag['Object'] = new Media_SWF_Tag_DoAction($tag);
        }
        return $tag['Object'];
      }
    }
    throw new Exception('Not found Action');
  }

  public function getTagByCharacterId($characterId) 
  {
    foreach ($this->_tags as &$tag) {
      if (isset($tag['CharacterId']) && $tag['CharacterId'] === $characterId) {
        return $tag;
      }
    }
    return null;
  }

  public function setTagByCharacterId($characterId, $newTag) 
  {
    foreach ($this->_tags as $i => $tag) {
      if (isset($tag['CharacterId']) && $tag['CharacterId'] === $characterId) {
        $this->_tags[$i] = array_merge($newTag, array('CharacterId' => $characterId));
      }
    }
  }

  public function getDefineSpriteByCharacterId($characterId)
  {
    $tag = $this->getTagByCharacterId($characterId);
    if ($tag['Code'] !== 39) {
      return null; 
    }
    if (!isset($tag['Object'])) {
      $tag['Object'] = new Media_SWF_Tag_DefineSprite($tag);
    }
    return $tag['Object'];
  }

  public function getDefineShapeByCharacterId($characterId)
  {
    $tag = $this->getTagByCharacterId($characterId);
    if (!in_array($tag['Code'], array(2, 22, 32))) {
      return null; 
    }
    if (!isset($tag['Object'])) {
      $tag['Object'] = new Media_SWF_Tag_DefineShape($tag);
    }
    return $tag['Object'];
  }

}
