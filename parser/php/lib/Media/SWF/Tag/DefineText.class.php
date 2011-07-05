<?php
// 埋め込みフォントのテキストは対応しない
class Media_SWF_Tag_DefineText extends Media_SWF_Tag
{
  protected 
    $type = 'shape',
    $hasFontCount = 0,
    $hasColorCount = 0;

  public function getElementSavedUrl()
  {
    $url = "defines/".$this->getGroupName();
    if ($this->root->saveWithCompress) {
      return $url."char.svgz";
    } else {
      return $url."char.svg";
    }
  }

  public function parse($reader)
  {
    $this->_fields['CharacterId'] = $reader->getUI16LE();
    $this->_fields['TextBounds'] = $reader->getRect();
    $this->_fields['TextMatrix'] = $reader->getMatrix();
    $this->_fields['GlyphBits'] = $reader->getUI8();
    $this->_fields['AdvanceBits'] = $reader->getUI8();

    $this->_fields['TextRecords'] = array();
    while (true) {
      $textRecord = $this->getTextRecord($reader);
      if (!$textRecord) {
        break;
      }
      $this->_fields['TextRecords'][] = $textRecord;
    }

    $this->_fields['EndOfRecordsFlag'] = 0;

    $this->reset($reader);
    parent::parse($reader);
  }

  protected function getTextRecord($reader) 
  {
    $flag = $reader->getUI8();
    if ($flag === 0) {
      return false;
    }
    $hasFont    = (bool) (($flag >> 3) & 1);
    $hasColor   = (bool) (($flag >> 2) & 1);
    $hasYOffset = (bool) (($flag >> 1) & 1);
    $hasXOffset = (bool) (($flag >> 0) & 1);

    $textRecord = array();
    if ($hasFont) {
      $textRecord['FontID'] = $reader->getUI16LE();
      ++$this->hasFontCount;
    }
    if ($hasColor) {
      $textRecord['TextColor'] = ($this->code == Media_SWF_Tag::DEFINE_TEXT2) ? 
                                 $reader->getRGBA() : $reader->getRGB();
      ++$this->hasColorCount;
    }
    if ($hasXOffset) {
      $textRecord['XOffset'] = $reader->getSI16();
    }
    if ($hasYOffset) {
      $textRecord['YOffset'] = $reader->getSI16();
    }
    if ($hasFont) {
      $textRecord['TextHeight'] = $reader->getUI16LE();
    }
    $textRecord['GlyphCount'] = $reader->getUI8();
    $textRecord['GlyphEntries'] = array();
    for ($i = 0; $i < $textRecord['GlyphCount']; ++$i) {
      $textRecord['GlyphEntries'][] = array(
        'GlyphIndex' => $reader->getUIBits($this->getField('GlyphBits')),
        'GlyphAdvance' => $reader->getSIBits($this->getField('AdvanceBits')),
      );
    }
    return $textRecord;
  }

  public function getTextRecordsWithText()
  {
    $ret = array(); 
    $font = null;
    foreach ($this->_fields['TextRecords'] as $textRecord) {
      if (isset($textRecord['FontID'])) {
        $font = $this->root->getTagByCharacterId($textRecord['FontID']);
      }
      $string = "";
      foreach ($textRecord['GlyphEntries'] as $glyph) {
        $string .= $font->getCodeString($glyph['GlyphIndex']);
      }
      $textRecord['Text'] = $string;
      $ret[] = $textRecord;
    }
    return $ret;
  }

  public function convertSVG()
  {
    $bounds = $this->getField('TextBounds');
    $matrix = $this->getField('TextMatrix');

    $text = Media_SVG::newElement('text');
    $text->set('id', $this->getElementIdString());
    $text->set('viewBox', $bounds["Xmin"] . " " . $bounds["Ymin"] . " " . $bounds["Xmax"] . " " . $bounds["Ymax"]);

    $font = $color = null;
    $font_size = $x = $y = 0;
    foreach ($this->_fields['TextRecords'] as $textRecord) {
      if (isset($textRecord['FontID'])) {
        $font = $this->root->getTagByCharacterId($textRecord['FontID']);
      }
      if (isset($textRecord['TextColor'])) {
        $color = $textRecord['TextColor'];
      }
      if (isset($textRecord['TextHeight'])) {
        $font_size = $textRecord['TextHeight'];
      }
      if (isset($textRecord['XOffset'])) {
        $x = $textRecord['XOffset'];
      }
      if (isset($textRecord['YOffset'])) {
        $y = $textRecord['YOffset'];
      }
      $string = "";
      $x_list = array();
      foreach ($textRecord['GlyphEntries'] as $glyph) {
        $string .= $font->getCodeString($glyph['GlyphIndex']);
        $x_list[] = $x;
        $x += $glyph['GlyphAdvance'];
      }
      // Tspan
      $tspan = Media_SVG::newElement('tspan');
      $tspan->set("y", $y);
      $tspan->set("x", implode(" ", $x_list));
      if ($this->hasFontCount > 1) {
        $font->setStyleToSVG($tspan);
      }
      if ($this->hasColorCount > 1) {
        $this->setColorToSVG($tspan, $color);
        $tspan->set("font-size", $font_size);
      }
      $tspan->setValue($string);
      $text->addNode($tspan);
    }
    if ($this->hasFontCount == 1) {
      $font->setStyleToSVG($text);
      $text->set("font-size", $font_size);
    }
    if ($this->hasColorCount == 1) {
      $this->setColorToSVG($text, $color);
    }
    return $text;
  }

  public function setColorToSVG($svg, $color) 
  {
    $svg->set("fill", sprintf("%02x%02x%02x", $color['Red'], $color['Green'], $color['Blue']));
    if (isset($color['Alpha'])) {
      $svg->set("opacity", $color['Alpha'] / 255);
    }
  }
}
