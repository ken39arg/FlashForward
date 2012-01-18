<?php

class Media_SWF_Tag_DefineShape extends Media_SWF_Tag
{
  protected
    $type     = 'shape',
    $fillBits = 0,
    $lineBits = 0,
    $_placedCharacters = array();

  public function getElementSavedUrl()
  {
    $url = "defines/".$this->getGroupName();
    if ($this->root->saveWithCompress) {
      return $url."char.svgz";
    } else {
      return $url."char.svg";
    }
  }

  public function getChildIds()
  {
    return $this->_placedCharacters;
  }

  public function replacePlacedCharacterIds($characterIdsMap)
  {
    foreach ($this->_fields['Shapes']['FillStyles']['FillStyles'] as &$tag)
    {
      if (isset($tag['BitmapId'])) {
        foreach ($characterIdsMap as $oldCharacterId => $newCharacterId) {
          if ($tag['BitmapId'] == $oldCharacterId) {
            $tag['BitmapId'] = $newCharacterId;
            break;
          }
        }
      }
    }
  }

  public function parse($reader)
  {
    $this->_fields = array(
      'CharacterId' => $reader->getUI16LE(), // ShapeId
      'ShapeBounds' => $reader->getRect(),
      'Shapes'      => $reader->getShapeWithStyle($this->code),
    );
    $fs = $this->_fields['Shapes']['FillStyles']['FillStyles'];
    foreach ($this->_fields['Shapes']['ShapeRecords'] as $s) {
      if (isset($s['FillStyles'])) {
        $fs = array_merge($fs, $s['FillStyles']['FillStyles']);
      }
    }
    foreach ($fs as $_fs) {
      if (isset($_fs['BitmapId']) && $_fs['BitmapId'] !== 65535) {
        $this->_placedCharacters[] = $_fs['BitmapId'];
      }
    }
  }

  public function build()
  {
    $writer = new Media_SWF_Parser();
    $writer->putUI16LE($this->_fields['CharacterId']);
    $writer->putRect($this->_fields['ShapeBounds']);
    $this->putShapeWithStyle($this->_fields['Shapes'], $writer);
    return $writer->output();
  }

  public function saveAsSVG($filename = null)
  {
    $bounds = $this->getField('ShapeBounds');
    $svg_element = $this->convertSVG();

    $width  = $bounds['Xmax'] - $bounds['Xmin'];
    $height = $bounds['Ymax'] - $bounds['Ymin'];
    $x = - $bounds['Xmin'];
    $y = - $bounds['Ymin'];
    $svg = new Media_SVG($width / 20 + 10, $height / 20 + 10);
    $svg->addNode(
      Media_SVG::newElement('Group')
        ->set('transform', array('translate' => array($x / 20 + 5, $y / 20 + 5), 
                                 'scale'     => array('0.05')))
        ->addNode($svg_element)
    );
    if ($filename) {
    
      return true;
    } else {
      return $svg->toString();
    }
  }

  public function convertSVG()
  {
    $bounds = $this->getField('ShapeBounds');
    $shapes = $this->getField('Shapes');
    $x = $y = 0;

    $shapeRecords = $shapes['ShapeRecords'];

    $elements = array();
    $styleList = $this->createStyleList($shapes, $elements);

    foreach ($shapeRecords as $sr) {
      switch ($sr['Type']) {
        case 0: // End
          // ここでGo!!
          $elements = array_merge($elements, $styleList->getAllStyles());
          break;
        case 1: // SetUp
          if ($sr['StateNewStyles']) {
            $elements = array_merge($elements, $styleList->getAllStyles());
            $styleList = $this->createStyleList($sr, $elements);
          }

          if ($sr['StateFillStyle0']) 
            $styleList->setFillStyle0($sr['FillStyle0']);

          if ($sr['StateFillStyle1']) 
            $styleList->setFillStyle1($sr['FillStyle1']);

          if ($sr['StateLineStyle']) 
            $styleList->setLineStyle($sr['LineStyle']);

          $styleList->update();

          if ($sr['StateMoveTo']) {
            $x = $sr['MoveDeltaX'];
            $y = $sr['MoveDeltaY'];
          }

          $styleList->moveTo($x, $y);

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
          $styleList->lineTo($x, $y);
          break;
        case 3: // CurveTo
          $cx = $x + $sr['ControlDeltaX'];
          $cy = $y + $sr['ControlDeltaY'];
          $x += $sr['ControlDeltaX'] + $sr['AnchorDeltaX'];
          $y += $sr['ControlDeltaY'] + $sr['AnchorDeltaY'];
          $styleList->curveTo($cx, $cy, $x, $y);
          break;
      }
    }
    $elements = array_filter($elements, 'Media_SWF_Tag_DefineShape::hasSVGPath');

    if (count($elements) === 1) {
      $svg_element = array_shift($elements);
    } else {
      $svg_element = Media_SVG::newElement('Group');
      foreach ($elements as $element) {
        $svg_element->addNode($element);
      }
    }
    $svg_element->set('id', $this->getElementIdString());
    $svg_element->set('viewBox', $bounds["Xmin"] . " " . $bounds["Ymin"] . " " . $bounds["Xmax"] . " " . $bounds["Ymax"]);
    return $svg_element;
  }

  static private function hasSVGPath($element) {
    if ($element == null) {
      return false;
    }
    if ($element instanceof Media_SVG_Null) {
      return false;
    }
    if ($element instanceof Media_SVG_Path && $element->count() === 0) {
      return false;
    }
    return true;
  }

  protected function createStyleList($style, &$elements = array())
  {
    $styleList = new Media_SWF_Tag_DefineShape_StyleList();

    $fillStyles = $style['FillStyles']['FillStyles'];
    $lineStyles = $style['LineStyles']['LineStyles'];
    $spreadMode = array('pad', 'reflect', 'repeat');

    foreach ($fillStyles as $f) {
      if (isset($f['Color'])) {
        $svgFillStyle = Media_SVG::newElement('Path');
        $svgFillStyle->set('fill', "#".sprintf("%02x%02x%02x", $f['Color']['Red'], $f['Color']['Green'], $f['Color']['Blue']));
        if (isset($f['Color']['Alpha'])) {
          $svgFillStyle->set('fill-opacity', $f['Color']['Alpha'] / 255);
        }
      } elseif (isset($f['Gradient'])) {
        $gradient = $f['Gradient'];
        $gid = sprintf("g_%s_%s", $this->characterId, count($elements));

        $svgFillStyle = Media_SVG::newElement('Path');
        $svgFillStyle->set('fill', "url(#$gid)");

        $g = Media_SVG::newElement(($f['FillStyleType'] === 0x10) ? 'linearGradient' : 'radialGradient');
        $g->set('id', $gid);
        $g->set('spreadMethod', $spreadMode[$gradient['SpreadMode']]);
        // TODO GradientMatrixの対応を実装
        //$g->set('transform', Media_SWF_SVGUtill::matrixToSVGTransform($f['GradientMatrix']));
        foreach ($gradient['GradientRecords'] as $gr) {
          $g->addNode(Media_SVG::newElement('stop')
              ->set('offset', ($gr['Ratio'] / 255 * 100) . "%")
              ->set('stop-color', '#'.sprintf("%02x%02x%02x", $gr['Color']['Red'], $gr['Color']['Green'], $gr['Color']['Blue']))
              );
        }
        $elements[] = $g;
      } elseif (isset($f['BitmapId']) && $f['BitmapId'] !== 0xffff) {
        $id = $this->root->getTagByCharacterId($f['BitmapId'])->getElementIdString();
        $svgFillStyle = Media_SVG::newElement('use')
           ->set('href', "#$id")
           ->set('transform', Media_SWF_SVGUtill::matrixToSVGTransform($f['BitmapMatrix']));
      } else {
        $svgFillStyle = new Media_SVG_Null();
      }
      $styleList->addFillStyle($svgFillStyle);
    } // fillStyles

    foreach ($lineStyles as $l) {
      $svgLineStyle = Media_SVG::newElement('Path')->set('fill', 'none');
      if ($this->code !== Media_SWF_Tag::DEFINE_SHAPE4) {
        $svgLineStyle->set('stroke-linejoin', 'round')
                     ->set('stroke-linecap',  'round');
      }
      if (isset($l['Color'])) {
        $svgLineStyle->set('stroke', '#'.sprintf("%02x%02x%02x", $l['Color']['Red'], $l['Color']['Green'], $l['Color']['Blue']));
        if (isset($l['Color']['Alpha'])) {
          $svgLineStyle->set('stroke-opacity', $l['Color']['Alpha'] / 255);
        }
      }
      if (isset($l['Width'])) {
        $svgLineStyle->set('stroke-width', $l['Width']);
      }
      $styleList->addLineStyle($svgLineStyle);
    } // lineStyles

    return $styleList;
  }

  protected function putShapeWithStyle($shapeWithStyle, $writer)
  {
    $this->putFillStyleArray($shapeWithStyle['FillStyles'], $writer);
    $this->putLineStyleArray($shapeWithStyle['LineStyles'], $writer);
    $writer->putUIBits($shapeWithStyle['NumFillBits'], 4);
    $writer->putUIBits($shapeWithStyle['NumLineBits'], 4);
    $writer->putData($shapeWithStyle['ShapeRecords']);
  }

  protected function putFillStyleArray($fillStyleArray, $writer)
  {
    $writer->putUI8($fillStyleArray['FillStyleCount']);
    if (isset($fillStyleArray['FillStyleCountExtended'])) {
      $writer->putUI16LE($fillStyleArray['FillStyleCountExtended']);
    }
    foreach ($fillStyleArray['FillStyles'] as $fillStyle)
    {
      $this->putFillStyle($fillStyle, $writer);
    }
  }

  protected function putFillStyle($fillStyle, $writer)
  {
    $writer->putUI8($fillStyle['FillStyleType']);
    switch ($fillStyle['FillStyleType']) {
      case 0x00: // solid
        if ($this->code === 32) {
          $writer->putRGBA($fillStyle['Color']);
        } else {
          $writer->putRGB($fillStyle['Color']);
        }
        break;
      case 0x10: // linear gradient
      case 0x12: // radial gradient fill
      //case 0x13: // focal radial gradient  //swf 8
        $writer->putMatrix($fillStyle['GradientMatrix']);
        $this->putGradient($fillStyle['Gradient'], $writer);
        break;
      case 0x40: // repeating bitmap
      case 0x41: // clipped bitmap
      case 0x42: // non-smoothed repeating bitmap
      case 0x43: // non-smoothed clipped bitmap
        $writer->putUI16LE($fillStyle['BitmapId']);
        $writer->putMatrix($fillStyle['BitmapMatrix']);
        break;
    }
  }

  protected function putLineStyleArray($lineStyleArray, $writer)
  {
    $writer->putUI8($lineStyleArray['LineStyleCount']);
    if (isset($lineStyleArray['LineStyleCountExtended'])) {
      $writer->putUI16LE($lineStyleArray['LineStyleCountExtended']);
    }
    foreach ($lineStyleArray['LineStyles'] as $lineStyle)
    {
      $writer->putUI16LE($lineStyle['Width']);
      if ($this->code === 32) {
        $writer->putRGBA($lineStyle['Color']);
      } else {
        $writer->putRGB($lineStyle['Color']);
      }
    }
  }

  protected function putGradient($gradient, $writer)
  {
    $writer->putUIBits($gradient['SpreadMode'], 2);
    $writer->putUIBits($gradient['InterpolationMode'], 2);
    $writer->putUIBits($gradient['NumGradients'], 4);
    foreach ($gradient['GradientRecords'] as $gradientRecord) {
      $writer->putUI8($gradientRecord['Ratio']);
      if ($this->code === 32) {
        $writer->putRGBA($gradientRecord['Color']);
      } else {
        $writer->putRGB($gradientRecord['Color']);
      }
    }
  }

}

// internal 
class Media_SWF_Tag_DefineShape_StyleList 
{
  public 
    $styles     = array(),
    $fillStyle0 = 0,
    $fillStyle1 = 0,
    $lineStyle  = 0,
    $fillStyles = array(),
    $lineStyles = array();
  public function setFillStyle0($id)
  {
    $this->fillStyle0 = $id;
  }
  public function setFillStyle1($id)
  {
    $this->fillStyle1 = $id;
  }
  public function setLineStyle($id)
  {
    $this->lineStyle = $id;
  }
  public function addFillStyle($fillStyle)
  {
    $this->fillStyles[] = $fillStyle;
  }
  public function addLineStyle($lineStyle)
  {
    $this->lineStyles[] = $lineStyle;
  }
  public function getFillStyle($id)
  {
    return (isset($this->fillStyles[$id - 1])) ? $this->fillStyles[$id - 1] : null;
  }
  public function getLineStyle($id)
  {
    return (isset($this->lineStyles[$id - 1])) ? $this->lineStyles[$id - 1] : null;
  }

  public function update()
  {
    $this->styles = array();
    if ($this->fillStyle0 > 0) {
      $style =$this->getFillStyle($this->fillStyle0);
      if ($style instanceof Media_SVG_Path) $style->direction('+');
      $this->styles[] = $style;
    }
    if ($this->fillStyle1 > 0) {
      $style = $this->getFillStyle($this->fillStyle1);
      if ($style instanceof Media_SVG_Path) $style->direction('-');
      $this->styles[] = $style;
    }
    if ($this->lineStyle > 0) {
      $style = $this->getLineStyle($this->lineStyle);
      $this->styles[] = $style;
    }
    return $this;
  }

  public function moveTo($x, $y)
  {
    foreach ($this->styles as $style) {
      if ($style instanceof Media_SVG_Path)
        $style->moveTo($x, $y);
    }
  }
  public function closePath()
  {
    foreach ($this->styles as $style) {
      if ($style instanceof Media_SVG_Path)
        $style->closePath();
    }
  }
  public function lineTo($x, $y)
  {
    foreach ($this->styles as $style) {
      if ($style instanceof Media_SVG_Path)
        $style->lineTo($x, $y);
    }
  }

  public function curveTo($cx, $cy, $x, $y)
  {
    foreach ($this->styles as $style) {
      if ($style instanceof Media_SVG_Path)
        $style->curveTo($cx, $cy, $x, $y);
    }
  }

  public function getAllStyles()
  {
    return array_merge($this->fillStyles, $this->lineStyles);
  }
}
