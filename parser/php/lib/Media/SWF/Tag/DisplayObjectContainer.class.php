<?php
class Media_SWF_Tag_DisplayObjectContainer extends Media_SWF_Tag
{
  protected
    $name       = "",
    $_tags      = array(),
    $childIds   = array(),
    $childNames = array();

  public function getName()
  {
    return $this->name;
  }

  public function getSpriteNames()
  {
    return $this->childNames;
  }

  public function getChildIds()
  {
    return $this->childIds;
  }

  public function hasChildByName($name)
  {
    return (isset($this->childNames[$name])) ? true: false;
  }

  public function getCharacterIdByName($name)
  {
    return (isset($this->childNames[$name])) ? $this->childNames[$name]: null;
  }

  public function getChildByName($name)
  {
    return (isset($this->childNames[$name])) 
            ? $this->root->getTagByCharacterId($this->childNames[$name])
            : null;
  }

  public function getChildren()
  {
    $ret = array();
    foreach ($this->childIds as $cid) {
      $ret[] = $this->root->getTagByCharacterId($cid);
    }
    return $ret;
  }

  public function resolveChiledSpriteName($name)
  {
    $object = $this;
    foreach (explode('/', trim($name, '/')) as $_name) {
      $object = $object->getChildByName($_name);
      if (is_null($object)) {
        return false;
      }
    }
    return $object->getCharacterId();
  }

  public function parse($reader)
  {
    while (true) {
      $cl = $reader->getCodeAndLength();
      $class = "Media_SWF_Tag_".Media_SWF_Tag::name($cl['Code']);
      if (!class_exists($class)) {
        $class = 'Media_SWF_Tag';
      }
      $tag = new $class($cl['Code'], $cl['Length'], $cl['LongFormat'], $reader, $this->root);
      $this->_tags[] = $tag;

      if ($cl['Code'] == 0) { // END Tag
        break;
      }

      if (!$tag->isDisplayListTag()) {
        continue;
      }
      if (!$tag->hasField('CharacterId')) {
        continue;
      }
      $characterId = $tag->getField('CharacterId');
      $object = $this->root->getTagByCharacterId($characterId);
      if (!in_array($characterId, $this->childIds)) {
        $this->childIds[] = $characterId;
      }
      if ($object->firstParentId === false && $this->characterId) {
        $object->firstParentId = $this->characterId;
      }
      if ($tag->hasField('Name')) {
        $name = $tag->getField('Name');
        if (isset($this->childNames[$name]) && $this->childNames[$name] != $characterId) {
          throw new Exception('Duplicate name "'.$name.'"');
        }
        if ($object->name === "") {
          $object->name = $name;
        }
        $this->childNames[$name] = $characterId;
      }
    }
  }

  public function build()
  {
    $writer = new Media_SWF_Parser();
    foreach ($this->_tags as $tag) {
      $tag->write($writer);
    }
    return $writer->output();
  }

  public function convertSVG()
  {
    $id = $this->getElementIdString();
    $svg = Media_SVG::newElement('Group');
    $svg->set('id', $id);
    $frame = 0;
    $display_stack = array();
    foreach ($this->_tags as $tag) {
      switch ($tag->getCode()) {
        case Media_SWF_Tag::PLACE_OBJECT:
        case Media_SWF_Tag::PLACE_OBJECT2:
        case Media_SWF_Tag::PLACE_OBJECT3:
          $depth       = $tag->getField('Depth');
          $characterId = $tag->getField('CharacterId');
          if ($characterId) {
            $id = $this->root->getTagByCharacterId($characterId)->getElementIdString();
            $node = Media_SVG::newElement('use');
            $node->set('href', "url(#$id)");
          } else {
            $node = $display_stack[$depth];
          }
          if ($tag->hasField('Matrix')) {
            $node->set('transform', Media_SWF_SVGUtill::matrixToSVGTransform($tag->getField('Matrix')));
          }
          if ($tag->hasField('ColorTransform')) {
            $cxform = $tag->getField('ColorTransform');
            if (isset($cxform['AlphaMultTerm'])) {
              $node->set('opacity', ($cxform['AlphaMultTerm'] / 256));
            }
          }
          $display_stack[$depth] = $node;
          break;
        case Media_SWF_Tag::REMOVE_OBJECT:
        case Media_SWF_Tag::REMOVE_OBJECT2:
          // SVGでは何もしない
          $depth = $tag->getField('Depth');
          unset($display_stack[$depth]);
          break;
        case Media_SWF_Tag::SHOW_FRAME:
          $frame++;
          ksort($display_stack);
          $f = Media_SVG::newElement('Group')->set('id', $id.'_f_'.$frame);
          foreach ($display_stack as $depth => $node) {
            $f->addNode(clone $node);
          }
          $svg->addNode($f);
          break 2; // SVGでは1フレームのみ
      }
    }
    return $svg;
  }

  public function getControlsArray()
  {
    $ret = array();
    $frame = 0;
    $frame_name = null;
    $display_stack = array();
    $actions_stack = array();
    $remove_stack  = array();
    foreach ($this->_tags as $tag) {
      switch ($tag->getCode()) {
        case Media_SWF_Tag::PLACE_OBJECT:
        case Media_SWF_Tag::PLACE_OBJECT2:
        case Media_SWF_Tag::PLACE_OBJECT3:
          $depth       = $tag->getField('Depth');
          $characterId = $tag->getField('CharacterId');
          $display     = $display_stack[$depth];
          if ($characterId) {
            $id = $this->root->getTagByCharacterId($characterId)->getElementIdString();
            if ($display) {
              $display['cid'] = $id;
              //$display['replace'] = true;
            } else {
              $display = array(
                'dp' => $depth,
                'cid'   => $id,
                //'new'   => true,
              );
            }
          } else {
            //if ($display['replace']) {
            //  unset($display['replace']);
            //}
          }
          if ($tag->hasField('Name')) {
            $display['name'] = $tag->getField('Name');
          }
          if ($tag->hasField('Matrix')) {
            $matrix = Media_SWF_SVGUtill::matrixToArray($tag->getField('Matrix'));
            if ($matrix != array(1,0,0,1,0,0)) {
              $display['mtx'] = Media_SWF_SVGUtill::matrixToArray($tag->getField('Matrix'));
            }
          }
          if ($tag->hasField('ColorTransform')) {
            $cxform = $tag->getField('ColorTransform');
            $display['cx'] = Media_SWF_SVGUtill::cxformToArray($tag->getField('ColorTransform'));
          }
          if ($tag->hasField('ClipDepth')) {
            $display['cdp'] = $tag->getField('ClipDepth');
          }
          $display_stack[$depth] = $display;
          break;
        case Media_SWF_Tag::REMOVE_OBJECT:
        case Media_SWF_Tag::REMOVE_OBJECT2:
          // SVGでは何もしない
          $depth = $tag->getField('Depth');
          $remove_stack[] = $depth;
          unset($display_stack[$depth]);
          break;
        case Media_SWF_Tag::DO_ACTION:
          $actions_stack[] = $tag->convertArray();
          break;
        case Media_SWF_Tag::FRAME_LABEL:
          $frame_name = $tag->getField('Name');
          break;
        case Media_SWF_Tag::SHOW_FRAME:
          ++$frame;
          ksort($display_stack);
          $d = array();
          foreach ($display_stack as $i => $display) {
            $d[] = $display;
            //if ($display_stack[$i]['new']) unset($display_stack[$i]['new']);
            //if ($display_stack[$i]['replace']) unset($display_stack[$i]['replace']);
          }
          $r = array( 
            'd' => $d,
          );
          if (count($remove_stack) > 0) {
            $r['rm'] = $remove_stack;
          }
          if ($frame_name) {
            $r['label'] = $frame_name;
          }
          if (count($actions_stack) > 0) {
            $r['act'] = $actions_stack;
          }
          $ret[] = $r;
          $remove_stack = array();
          $actions_stack = array();
          $frame_name   = null;
          break;
        case Media_SWF_Tag::SET_BACKGROUND_COLOR:
          break;
        case Media_SWF_Tag::END:
          break;
        case Media_SWF_Tag::FILE_ATTRIBUTES:
        case Media_SWF_Tag::METADATA:
          break;
        default:
          if (!$tag->isDefinitionTag())
            var_dump($tag->getTagName());
      }
    }
    return $ret;
  }

  public function replacePlacedCharacterIds($characterIdsMap)
  {
    foreach ($this->_tags as &$tag)
    {
      if ($tag->isDisplayListTag() && $tag->hasField('CharacterId')) {
        foreach ($characterIdsMap as $oldCharacterId => $newCharacterId) {
          if ($tag->getField('CharacterId') === $oldCharacterId) {
            $tag->setField('CharacterId', $newCharacterId);
            break;
          }
        }
      }
    }
  }

}
