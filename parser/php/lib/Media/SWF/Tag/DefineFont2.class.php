<?php
class Media_SWF_Tag_DefineFont2 extends Media_SWF_Tag
{
  protected
    $type = "font";

  public function getElementSavedUrl()
  {
    $url = "defines/".$this->getGroupName();
    if ($this->root->saveWithCompress) {
      return $url."font.svgz";
    } else {
      return $url."font.svg";
    }
  }

  public function parse($reader)
  {
    $this->_fields['CharacterId'] = $reader->getUI16LE(); // CharacterId
    $this->_fields['FontFlagsHasLayout'] = $reader->getUIBit();
    $this->_fields['FontFlagsShiftJIS']  = $reader->getUIBit();
    $this->_fields['FontFlagsSmallText'] = $reader->getUIBit();
    $this->_fields['FontFlagsANSI'] = $reader->getUIBit();
    $this->_fields['FontFlagsWideOffsets'] = $reader->getUIBit();
    $this->_fields['FontFlagsWideCodes'] = $reader->getUIBit();
    $this->_fields['FontFlagsItalic'] = $reader->getUIBit();
    $this->_fields['FontFlagsBold'] = $reader->getUIBit();
    $this->_fields['LanguageCode'] = $reader->getUI8();
    $this->_fields['FontNameLen']  = $reader->getUI8();
    $fontName = "";
    for ($i=0;$i<$this->_fields['FontNameLen'];$i++) {
      $fontName .= chr($reader->getUI8());
    }
    $this->_fields['FontName'] = $fontName;
    $this->_fields['NumGlyphs'] = $reader->getUI16LE();
    $this->_fields['OffsetTable'] = array();

    $glyphOffset = $reader->getByteOffset();
    if ($this->_fields['FontFlagsWideOffsets']) {
      for ($i = 0; $i < $this->_fields['NumGlyphs']; ++$i) {
        $this->_fields['OffsetTable'][] = $reader->getUI32LE();
      }
      $this->_fields['CodeTableOffset'] = $reader->getUI32LE();
    } else {
      for ($i = 0; $i < $this->_fields['NumGlyphs']; ++$i) {
        $this->_fields['OffsetTable'][] = $reader->getUI16LE();
      }
      $this->_fields['CodeTableOffset'] = $reader->getUI16LE();
    }
    $this->_fields['GlyphShapeTable'] = array();
    for ($i = 0; $i < $this->_fields['NumGlyphs']; ++$i) {
      $endOffset = ($i + 1 < $this->_fields['NumGlyphs']) ? 
                    $this->_fields['OffsetTable'][$i + 1] :
                    $this->_fields['CodeTableOffset'];
      $endOffset += $glyphOffset;
      $reader->setOffset($glyphOffset + $this->_fields['OffsetTable'][$i], 0);
      $this->_fields['GlyphShapeTable'][] = $reader->getShape($this->code, $endOffset);
    }
    $reader->setOffset($glyphOffset + $this->_fields['CodeTableOffset'], 0);
    $this->_fields['CodeTable'] = array();
    if ($this->_fields['FontFlagsWideCodes']) {
      for ($i = 0; $i < $this->_fields['NumGlyphs']; ++$i) {
        $this->_fields['CodeTable'][] = $reader->getUI16LE();
      }
    } else {
      for ($i = 0; $i < $this->_fields['NumGlyphs']; ++$i) {
        $this->_fields['CodeTable'][] = $reader->getUI8();
      }
    }
    if ($this->_fields['FontFlagsHasLayout']) {
      $this->_fields['FontAscent']  = $reader->getSI16();
      $this->_fields['FontDescent'] = $reader->getSI16();
      $this->_fields['FontLeading'] = $reader->getSI16();
      $this->_fields['FontAdvanceTable'] = array();
      $this->_fields['FontBoundsTable'] = array();
      for ($i = 0; $i < $this->_fields['NumGlyphs']; ++$i) {
        $this->_fields['FontAdvanceTable'][] = $reader->getSI16();
      }
      for ($i = 0; $i < $this->_fields['NumGlyphs']; ++$i) {
        $this->_fields['FontBoundsTable'][] = $reader->getRect();
      }
      $this->_fields['KerningCount'] = 0;
      // not used throw FPv7
      //$this->_fields['KerningCount'] = $reader->getUI16LE();
      //$this->_fields['FontKerningTable'] = array();
      //for ($i = 0; $i < $this->_fields['KerningCount']; ++$i) {
      //  $this->_fields['FontKerningTable'][] = $reader->getKerningRecode($this->_fields['FontFlagsWideCodes']);
      //}
    }
    $this->reset($reader);
    parent::parse($reader);
  }

  public function getFontName()
  {
    return $this->root->convertEncoding($this->getField('FontName'));
  }
  public function isItalic()
  {
    return (bool) $this->getField('FontFlagsItalic');
  }
  public function isBold()
  {
    return (bool) $this->getField('FontFlagsBold');
  }

  public function getCodeString($index) 
  {
    if ($index < $this->getField('NumGlyphs')) {
      $code = $this->_fields['CodeTable'][$index];
      return (string) $this->root->convertEncoding(pack("H*", dechex($code)));
    }
    return null;
  }

  public function setStyleToSVG($svg) 
  {
    $svg->set("font-family", $this->getFontName());
    if ($this->isItalic()) {
      $svg->set("font-style", "italic");
    }
    if ($this->isBold()) {
      $svg->set("font-weight", "bold");
    }
  }
  public function setStyleToArray(&$style) 
  {
    $style['font'] = $this->getFontName();
    if ($this->isItalic()) {
      $style['italic'] = true;
    }
    if ($this->isBold()) {
      $style['bold'] = true;
    }
  }

  public function convertSVG()
  {
    $font = Media_SVG::newElement("font");
    $font->set('id', $this->getElementIdString());
    $font_face = Media_SVG::newElement("font-face");
    $font_face->set("font-family", $this->getFontName());
    $font->addNode($font_face);
    $glyphs = $this->getField('GlyphShapeTable');
    $bounds = $this->getField('FontBoundsTable');
    $advances = $this->getField('FontAdvanceTable');
    foreach ($glyphs as $i => $glyph) {
      $gl = Media_SVG::newElement('glyph');
      $gl->set("unicode", $this->getCodeString($i));
      $gl->direction("+");
      $minX = $minY = $maxX = $maxY = 0;
      foreach ($glyph['ShapeRecords'] as $sr) {
        switch ($sr['Type']) {
          case 0: // End
            break;
          case 1: // SetUp
            if ($sr['StateMoveTo']) {
              $x = $sr['MoveDeltaX'];
              $y = $sr['MoveDeltaY'];
            }
            $gl->moveTo($x, -$y);
            break;
          case 2: // LineTo
            if ($sr['GeneralLineFlag']) {
              $x += $sr['DeltaX'];
              $y += $sr['DeltaY'];
            } elseif ($sr['VertLineFlag']) {
              $y += $sr['DeltaY'];
            } else {
              $x += $sr['DeltaX'];
            }
            $gl->lineTo($x, -$y);
            break;
          case 3: // CurveTo
            $cx = $x + $sr['ControlDeltaX'];
            $cy = $y + $sr['ControlDeltaY'];
            $x += $sr['ControlDeltaX'] + $sr['AnchorDeltaX'];
            $y += $sr['ControlDeltaY'] + $sr['AnchorDeltaY'];
            $gl->curveTo($cx, -$cy, $x, -$y);
            break;
        }
        $minX = min($x, $minX);
        $minY = min($y, $minY);
        $maxX = max($x, $maxX);
        $maxY = max($y, $maxY);
      }
      if ($advances) {
        $gl->set("horiz-adv-x", $advances[$i]);
      } else {
        $gl->set("horiz-adv-x", $maxX);
      }
      $font->addNode($gl);
    }
    return $font;
  }
}
