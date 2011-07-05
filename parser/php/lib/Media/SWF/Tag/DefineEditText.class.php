<?php
class Media_SWF_Tag_DefineEditText extends Media_SWF_Tag
{
  protected
    $type = 'text';

  public function parse($reader)
  {
    $this->_fields['CharacterId'] = $reader->getUI16LE();
    $this->_fields['Bounds'] = $reader->getRect();

    $this->_fields['HasText'] = $reader->getUIBit();
    $this->_fields['WordWrap'] = $reader->getUIBit();
    $this->_fields['Multiline'] = $reader->getUIBit();
    $this->_fields['Password'] = $reader->getUIBit();

    $this->_fields['ReadOnly'] = $reader->getUIBit();
    $this->_fields['HasTextColor'] = $reader->getUIBit();
    $this->_fields['HasMaxLength'] = $reader->getUIBit();
    $this->_fields['HasFont'] = $reader->getUIBit();

    $this->_fields['HasFontClass'] = $reader->getUIBit();
    $this->_fields['AutoSize'] = $reader->getUIBit();
    $this->_fields['HasLayout'] = $reader->getUIBit();
    $this->_fields['NoSelect'] = $reader->getUIBit();

    $this->_fields['Border'] = $reader->getUIBit();
    $this->_fields['WasStatic'] = $reader->getUIBit();
    $this->_fields['HTML'] = $reader->getUIBit();
    $this->_fields['UseOutlines'] = $reader->getUIBit();

    if ($this->_fields['HasFont']) {
      $this->_fields['FontID'] = $reader->getUI16LE();
    }
    if ($this->_fields['HasFontClass']) {
      $this->_fields['FontClass'] = $reader->getString();
    }
    if ($this->_fields['HasFont']) {
      $this->_fields['FontHeight'] = $reader->getUI16LE();
    }
    if ($this->_fields['HasTextColor']) {
      $this->_fields['TextColor'] = $reader->getRGBA();
    }
    if ($this->_fields['HasMaxLength']) {
      $this->_fields['MaxLength'] = $reader->getUI16LE();
    }
    if ($this->_fields['HasLayout']) {
      $this->_fields['Align'] = $reader->getUI8();
      $this->_fields['LeftMargin'] = $reader->getUI16LE();
      $this->_fields['RightMargin'] = $reader->getUI16LE();
      $this->_fields['Indent'] = $reader->getUI16LE();
      $this->_fields['Leading'] = $reader->getSIBits(16);
    }
    $this->_fields['VariableName'] = $reader->getString();
    if ($this->_fields['HasText']) {
      $this->_fields['InitialText'] = $reader->getString();
    }
    $this->reset($reader);
    parent::parse($reader);
  }

  public function convertArray()
  {
    $meta = array();
    $style = array();
    $rect = $this->getField('Bounds');

    $meta['cid'] = $this->getElementIdString();
    $meta['size'] = array(
      $rect['Xmin'],
      $rect['Ymin'],
      $rect['Xmax'],
      $rect['Ymax'],
    );

    if ($this->getField('WordWrap')) {
      $style['word-wrap'] = $this->getField('WordWrap');
    }
    if ($this->getField('Multiline')) {
      $style['multiline'] = $this->getField('Multiline');
    }
    if ($this->getField('Border')) {
      $style['border'] = $this->getField('Border');
    }
    if ($this->hasField('FontHeight')) {
      $style['size'] = $this->getField('FontHeight');
    }
    if ($this->hasField('TextColor')) {
      $color = $this->getField('TextColor');
      $style['color'] = "#".sprintf("%02x%02x%02x", $color['Red'], $color['Green'], $color['Blue']);
      $style['opacity'] = $color['Alpha'] / 255;
    }
    if ($this->_fields['HasLayout']) {
      $align = array('left', 'right', 'center', 'justify');
      $style['align'] = $align[$this->getField('Align')];
      $style['left-mergin']  = $this->getField('LeftMargin');
      $style['right-mergin'] = $this->getField('RightMargin');
      $style['indent'] = $this->getField('Indent');
      $style['leading'] = $this->getField('Leading');
    }
    if ($this->hasField('FontID')) {
      $font = $this->root->getTagByCharacterId($this->getField('FontID'));
      $font->setStyleToArray($style);
    }
    $ret = array(
      'meta'  => $meta,
      'style' => $style,
    );
    if ($this->hasField('InitialText')) {
      $ret['text'] = $this->root->convertEncoding($this->getField('InitialText'));
    }
    if ($this->hasField('VariableName')) {
      $ret['variable'] = $this->getField('VariableName');
    }
    return $ret;
  }

  public function getElementSavedUrl()
  {
    return "defines/".$this->getGroupName()."char.json";
  }

}

