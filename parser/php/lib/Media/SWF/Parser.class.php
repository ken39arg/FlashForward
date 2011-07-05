<?php
require_once 'IO/Bit.php';
/**
 * Media_SWF_Parser.
 *
 * SwfFormatパーサ
 *
 * @uses IO_Bit
 * @package   Media_SWF
 * @version   $Id$
 * @copyright Copyright (C) 2010 KAYAC Inc.
 * @author    Kensaku Araga <araga-kensaku@kayac.com>
 * @see http://hkpr.info/flash/swf/index.php?Flash%20SWF%20Spec
 * @see http://www.adobe.com/devnet/swf/pdf/swf_file_format_spec_v10.pdf
 * @via http://openpear.org/package/IO_SWF (@yoya)
 */
class Media_SWF_Parser extends IO_Bit
{
  public function getLength()
  {
    return strlen($this->_data);
  }

  public function getByteOffset()
  {
    return $this->_byte_offset;
  }

  public function getDataAll() {
    $this->byteAlign();
    $data = substr($this->_data, $this->_byte_offset);
    $data_len = strlen($data);
    $this->_byte_offset += $data_len;
    return $data;
  }

  public function getFIBits($width)
  {
    return (double) ($this->getSIBits($width) / (double) (1 << 16));
  }

  public function getFLOAT()
  {
    $this->byteAlign();
    $ret = unpack('f', substr($this->_data, $this->_byte_offset, 4));
    $this->_byte_offset += 4;
    $value = $ret[1];
    if ($value < 0) {
        $value += 4294967296;
    }
    return $value;
  }

  public function getSI8() 
  {
    $this->byteAlign();
    $value = unpack('c', $this->_data{$this->_byte_offset});
    $this->_byte_offset += 1;
    return $value;
  }

  public function getSI16() 
  {
    $this->byteAlign();
    $ret = unpack('s', substr($this->_data, $this->_byte_offset, 2));
    $this->_byte_offset += 2;
    return $ret[1];
  }

  public function getSI32() 
  {
    $this->byteAlign();
    $ret = unpack('l', substr($this->_data, $this->_byte_offset, 4));
    $this->_byte_offset += 4;
    $value = $ret[1];
    if ($value < 0) { // php bugs
        $value += 4294967296;
    }
    return $value;
  }

  public function getDOUBLE()
  {
    $this->byteAlign();
    $ret = unpack('d', substr($this->_data, $this->_byte_offset, 8));
    $this->_byte_offset += 8;
    return $ret[1];
  }

  public function getString()
  {
    $string = "";
    while (($byte = $this->getUI8()) !== 0x00) {
      $string .= chr($byte);
    }
    return $string;
  }

  public function getRGB()
  {
    return array(
      'Red'   => $this->getUI8(),
      'Green' => $this->getUI8(),
      'Blue'  => $this->getUI8(),
    );
  }

  public function getRGBA()
  {
    return array(
      'Red'   => $this->getUI8(),
      'Green' => $this->getUI8(),
      'Blue'  => $this->getUI8(),
      'Alpha' => $this->getUI8(),
    );
  }

  public function getARGB()
  {
    return array(
      'Alpha' => $this->getUI8(),
      'Red'   => $this->getUI8(),
      'Green' => $this->getUI8(),
      'Blue'  => $this->getUI8(),
    );
  }

  public function getRect()
  {
    $nbits = $this->getUIBits(5);
    $rect = array(
      'Nbits' => $nbits,
      'Xmin'  => $this->getSIBits($nbits),
      'Xmax'  => $this->getSIBits($nbits),
      'Ymin'  => $this->getSIBits($nbits),
      'Ymax'  => $this->getSIBits($nbits),
    );
    $this->byteAlign();
    return $rect;
  }

  public function getMatrix()
  {
    $matrix = array();
    $matrix['HasScale'] = $this->getUIBit();
    if ($matrix['HasScale'] === 1) {
      $matrix['NScaleBits'] = $this->getUIBits(5);
      $matrix['ScaleX'] = $this->getFIBits($matrix['NScaleBits']);
      $matrix['ScaleY'] = $this->getFIBits($matrix['NScaleBits']);
    }
    $matrix['HasRotate'] = $this->getUIBit();
    if ($matrix['HasRotate'] === 1) {
      $matrix['NRotateBits'] = $this->getUIBits(5);
      $matrix['RotateSkew0'] = $this->getFIBits($matrix['NRotateBits']);
      $matrix['RotateSkew1'] = $this->getFIBits($matrix['NRotateBits']);
    }
    $matrix['NTranslateBits'] = $this->getUIBits(5);
    $matrix['TranslateX'] = $this->getSIBits($matrix['NTranslateBits']);
    $matrix['TranslateY'] = $this->getSIBits($matrix['NTranslateBits']);
    $this->byteAlign();
    return $matrix;
  }

  public function getColorTransform()
  {
    $cxform = array();
    $cxform['HasAddTerms']  = $this->getUIBit();
    $cxform['HasMultTerms'] = $this->getUIBit();

    $cxform['Nbits'] = $this->getUIBits(4);

    if ($cxform['HasMultTerms'] === 1) {
      $cxform['RedMultTerm']   = $this->getSIBits($cxform['Nbits']);
      $cxform['GreenMultTerm'] = $this->getSIBits($cxform['Nbits']);
      $cxform['BlueMultTerm']  = $this->getSIBits($cxform['Nbits']);
    }
    if ($cxform['HasAddTerms'] === 1) {
      $cxform['RedAddTerm']   = $this->getSIBits($cxform['Nbits']);
      $cxform['GreenAddTerm'] = $this->getSIBits($cxform['Nbits']);
      $cxform['BlueAddTerm']  = $this->getSIBits($cxform['Nbits']);
    }
    $this->byteAlign();

    return $cxform;
  }

  public function getColorTransformWithAlpha()
  {
    $cxform = array();
    $cxform['HasAddTerms']  = $this->getUIBit();
    $cxform['HasMultTerms'] = $this->getUIBit();

    $cxform['Nbits'] = $this->getUIBits(4);

    if ($cxform['HasMultTerms'] === 1) {
      $cxform['RedMultTerm']   = $this->getSIBits($cxform['Nbits']);
      $cxform['GreenMultTerm'] = $this->getSIBits($cxform['Nbits']);
      $cxform['BlueMultTerm']  = $this->getSIBits($cxform['Nbits']);
      $cxform['AlphaMultTerm'] = $this->getSIBits($cxform['Nbits']);
    }
    if ($cxform['HasAddTerms'] === 1) {
      $cxform['RedAddTerm']   = $this->getSIBits($cxform['Nbits']);
      $cxform['GreenAddTerm'] = $this->getSIBits($cxform['Nbits']);
      $cxform['BlueAddTerm']  = $this->getSIBits($cxform['Nbits']);
      $cxform['AlphaAddTerm'] = $this->getSIBits($cxform['Nbits']);
    }
    $this->byteAlign();

    return $cxform;
  }

  public function getSWFHeader()
  {
    return array(
      'Signature'  => $this->getData(3),
      'Version'    => $this->getUI8(),
      'FileLength' => $this->getUI32LE(),
      'FrameSize'  => $this->getRect(),
      'FrameRate'  => $this->getUI16LE(),
      'FrameCount' => $this->getUI16LE(),
    );
  }

  public function getTag()
  {
    $tag = $this->getCodeAndLength();
    switch ($tag['Code']) {
      case 6:  // DefineBits
      case 21: // DefineBitsJPEG2
      case 35: // DefineBitsJPEG3
      case 20: // DefineBitsLossless
      case 36: // DefineBitsLossless2
      case 46: // DefineMorphShape
      case 2:  // DefineShape (ShapeId)
      case 22: // DefineShape2 (ShapeId)
      case 32: // DefineShape3 (ShapeId)
      case 11: // DefineText
      case 33: // DefineText
      case 37: // DefineTextEdit
      case 39: // DefineSprite (SpriteId)
        $tag['CharacterId'] = $this->getUI16LE();
        $tag['Content'] = $this->getData($tag['Length'] - 2);
        break;
      default:
        $tag['Content'] = $this->getData($tag['Length']);
    }
    return $tag;
  }

  public function getCodeAndLength()
  {
    $tagCodeAndLength = $this->getUI16LE();
    $code = $tagCodeAndLength >> 6;
    $length = $tagCodeAndLength & 0x3f;
    $longFormat = false;
    if ($length == 0x3f) { // long format
      $length = $this->getUI32LE();
      $longFormat = true;
    }
    return array(
      'Code'       => $code,
      'Length'     => $length,
      'LongFormat' => $longFormat,
    );
  }

  public function putFIBits($value, $width)
  {
    $this->putUIBits($value * (1 << 16), $width);
  }

  public function putRGB($rgb)
  {
    $this->putUI8($rgb['Red']);
    $this->putUI8($rgb['Green']);
    $this->putUI8($rgb['Blue']);
  }

  public function putRGBA($rgb)
  {
    $this->putUI8($rgb['Red']);
    $this->putUI8($rgb['Green']);
    $this->putUI8($rgb['Blue']);
    $this->putUI8($rgb['Alpha']);
  }

  public function putARGB($rgb)
  {
    $this->putUI8($rgb['Alpha']);
    $this->putUI8($rgb['Red']);
    $this->putUI8($rgb['Green']);
    $this->putUI8($rgb['Blue']);
  }

  public function putString($string)
  {
    $this->putData($string);
    $this->putData("\x00");
  }

  public function putRect($rect)
  {
    $this->putUIBits($rect['Nbits'], 5);
    $this->putSIBits($rect['Xmin'], $rect['Nbits']);
    $this->putSIBits($rect['Xmax'], $rect['Nbits']);
    $this->putSIBits($rect['Ymin'], $rect['Nbits']);
    $this->putSIBits($rect['Ymax'], $rect['Nbits']);
    $this->byteAlign();
  }

  public function putMatrix($matrix)
  {
    $this->putUIBit($matrix['HasScale']);
    if ($matrix['HasScale'] === 1) {
      $this->putUIBits($matrix['NScaleBits'], 5);
      $this->putFIBits($matrix['ScaleX'], $matrix['NScaleBits']);
      $this->putFIBits($matrix['ScaleY'], $matrix['NScaleBits']);
    }
    $this->putUIBit($matrix['HasRotate']);
    if ($matrix['HasRotate'] === 1) {
      $this->putUIBits($matrix['NRotateBits'], 5);
      $this->putFIBits($matrix['RotateSkew0'], $matrix['NRotateBits']);
      $this->putFIBits($matrix['RotateSkew1'], $matrix['NRotateBits']);
    }
    $this->putUIBits($matrix['NTranslateBits'], 5);
    $this->putSIBits($matrix['TranslateX'], $matrix['NTranslateBits']);
    $this->putSIBits($matrix['TranslateY'], $matrix['NTranslateBits']);
    $this->byteAlign();
  }

  public function putColorTransform($cxform)
  {
    $this->putUIBit($cxform['HasAddTerms']);
    $this->putUIBit($cxform['HasMultTerms']);
    $this->putUIBits($cxform['Nbits'], 4);

    if ($cxform['HasMultTerms'] === 1) {
      $this->putSIBits($cxform['RedMultTerm'],   $cxform['Nbits']);
      $this->putSIBits($cxform['GreenMultTerm'], $cxform['Nbits']);
      $this->putSIBits($cxform['BlueMultTerm'],  $cxform['Nbits']);
    }
    if ($cxform['HasAddTerms'] === 1) {
      $this->putSIBits($cxform['RedAddTerm'],   $cxform['Nbits']);
      $this->putSIBits($cxform['GreenAddTerm'], $cxform['Nbits']);
      $this->putSIBits($cxform['BlueAddTerm'],  $cxform['Nbits']);
    }
    $this->byteAlign();
  }

  public function putColorTransformWithAlpha($cxform)
  {
    $this->putUIBit($cxform['HasAddTerms']);
    $this->putUIBit($cxform['HasMultTerms']);
    $this->putUIBits($cxform['Nbits'], 4);

    if ($cxform['HasMultTerms'] === 1) {
      $this->putSIBits($cxform['RedMultTerm'],   $cxform['Nbits']);
      $this->putSIBits($cxform['GreenMultTerm'], $cxform['Nbits']);
      $this->putSIBits($cxform['BlueMultTerm'],  $cxform['Nbits']);
      $this->putSIBits($cxform['AlphaMultTerm'], $cxform['Nbits']);
    }
    if ($cxform['HasAddTerms'] === 1) {
      $this->putSIBits($cxform['RedAddTerm'],   $cxform['Nbits']);
      $this->putSIBits($cxform['GreenAddTerm'], $cxform['Nbits']);
      $this->putSIBits($cxform['BlueAddTerm'],  $cxform['Nbits']);
      $this->putSIBits($cxform['AlphaAddTerm'], $cxform['Nbits']);
    }
    $this->byteAlign();
  }

  public function putSWFHeader($header)
  {
    $this->putData($header['Signature']);
    $this->putUI8($header['Version']);
    $this->putUI32LE($header['FileLength']);

    /* SWF Movie Header */
    $this->putRect($header['FrameSize']);
    $this->putUI16LE($header['FrameRate']);
    $this->putUI16LE($header['FrameCount']);
  }

  public function putCodeAndLength($tag)
  {
    $code = $tag['Code'];
    $length = $tag['Length'];
    if ($tag['LongFormat'] && ($length < 0x3f)) {
        $tagCodeAndLength = ($code << 6) | $length;
        $this->putUI16LE($tagCodeAndLength);
    } else {
        $tagCodeAndLength = ($code << 6) | 0x3f;
        $this->putUI16LE($tagCodeAndLength);
        $this->putUI32LE($length);
    }
  }

  public function setFileLength($fileLength)
  {
    $this->setUI32LE($fileLength, 4);
  }


  public function getShape($tagType, $endOffset)
  {
    $shape = array(
      'NumFillBits'  => $this->getUIBits(4),
      'NumLineBits'  => $this->getUIBits(4),
    );
    $shape['ShapeRecords'] = $this->getShapeRecords($tagType, $shape['NumFillBits'], $shape['NumLineBits'], $endOffset);
    return $shape;
  }

  public function getShapeWithStyle($tagType)
  {
    $shapeWithStyle = array(
      'FillStyles'   => $this->getFillStyleArray($tagType),
      'LineStyles'   => $this->getLineStyleArray($tagType),
      'NumFillBits'  => $this->getUIBits(4),
      'NumLineBits'  => $this->getUIBits(4),
    );
    $shapeWithStyle['ShapeRecords'] = $this->getShapeRecords($tagType, $shapeWithStyle['NumFillBits'], $shapeWithStyle['NumLineBits']);
    return $shapeWithStyle;
  }

  public function getFillStyleArray($tagType)
  {
    $fillStyleArray = array();
    $fillStyleArray['FillStyleCount'] = $fillStyleCount = $this->getUI8();
    if ($fillStyleCount === 0xFF) {
      $fillStyleArray['FillStyleCountExtended'] = $fillStyleCount = $this->getUI16LE();
    }
    $fillStyleArray['FillStyles'] = array();
    for ($i = 0; $i < $fillStyleCount; ++$i)
    {
      $fillStyleArray['FillStyles'][] = $this->getFillStyle($tagType);
    }
    return $fillStyleArray;
  }

  public function getFillStyle($tagType)
  {
    $fillStyle = array();
    $fillStyle['FillStyleType'] = $this->getUI8();
    switch ($fillStyle['FillStyleType']) {
      case 0x00: // solid
        $fillStyle['Color'] = ($tagType === 32) ? $this->getRGBA() : $this->getRGB();
        break;
      case 0x10: // linear gradient
      case 0x12: // radial gradient fill
      //case 0x13: // focal radial gradient  //swf 8
        $fillStyle['GradientMatrix'] = $this->getMatrix();
        $fillStyle['Gradient'] = $this->getGradient($tagType);
        break;
      case 0x40: // repeating bitmap
      case 0x41: // clipped bitmap
      case 0x42: // non-smoothed repeating bitmap
      case 0x43: // non-smoothed clipped bitmap
        $fillStyle['BitmapId'] = $this->getUI16LE();
        $fillStyle['BitmapMatrix'] = $this->getMatrix();
        break;
    }
    return $fillStyle;
  }

  public function getLineStyleArray($tagType)
  {
    $lineStyleArray = array();
    $lineStyleArray['LineStyleCount'] = $lineStyleCount = $this->getUI8();
    if ($lineStyleCount === 0xFF) {
      $lineStyleArray['LineStyleCountExtended'] = $lineStyleCount = $this->getUI16LE();
    }
    $lineStyleArray['LineStyles'] = array();
    for ($i = 0; $i < $lineStyleCount; ++$i) {
      // LineStyle2は対応しない
      $lineStyleArray['LineStyles'][] = array(
        'Width' => $this->getUI16LE(),
        'Color' => ($tagType === 32 ? $this->getRGBA() : $this->getRGB()),
      );
    }
    return $lineStyleArray;
  }

  public function getShapeRecords($tagType, $numFillBits, $numLineBits, $endOffset = 0)
  {
    $shapeRecords = array();
    if ($endOffset === 0) {
      $endOffset = $this->getLength();
    }
    while ($this->getByteOffset() <= $endOffset) {
      $shapeRecord = array(
        'Type'     => 0,
        'TypeFlag' => $this->getUIBit(),
      );
      if ($shapeRecord['TypeFlag'] == 0) {
        // Non edge record (=0)
        $shapeRecord['StateNewStyles']  = $this->getUIBit();
        $shapeRecord['StateLineStyle']  = $this->getUIBit();
        $shapeRecord['StateFillStyle1'] = $this->getUIBit();
        $shapeRecord['StateFillStyle0'] = $this->getUIBit();
        $shapeRecord['StateMoveTo']     = $this->getUIBit();
        if ($shapeRecord['StateMoveTo']     != 0
         || $shapeRecord['StateFillStyle0'] != 0
         || $shapeRecord['StateFillStyle1'] != 0
         || $shapeRecord['StateLineStyle']  != 0
         || $shapeRecord['StateNewStyles']  != 0
        ) {
          // StyleChangeRecord
          $shapeRecord['Type'] = 1;
          if ($shapeRecord['StateMoveTo']) {
            $shapeRecord['MoveBits']   = $this->getUIBits(5);
            $shapeRecord['MoveDeltaX'] = $this->getSIBits($shapeRecord['MoveBits']);
            $shapeRecord['MoveDeltaY'] = $this->getSIBits($shapeRecord['MoveBits']);
          }
          if ($shapeRecord['StateFillStyle0']) {
            $shapeRecord['FillStyle0'] = $this->getUIBits($numFillBits);
          }
          if ($shapeRecord['StateFillStyle1']) {
            $shapeRecord['FillStyle1'] = $this->getUIBits($numFillBits);
          }
          if ($shapeRecord['StateLineStyle']) {
            $shapeRecord['LineStyle'] = $this->getUIBits($numLineBits);
          }
          if ($shapeRecord['StateNewStyles']) {
            $shapeRecord['FillStyles'] = $this->getFillStyleArray($tagType);
            $shapeRecord['LineStyles'] = $this->getLineStyleArray($tagType);
            $shapeRecord['NumFillBits'] = $this->getUIBits(4);
            $shapeRecord['NumLineBits'] = $this->getUIBits(4);
            $numFillBits = $shapeRecord['NumFillBits'];
            $numLineBits = $shapeRecord['NumLineBits'];
          }
        } else {
          // EndShapeRecord
          $shapeRecord = array(
            'Type'     => 0,
            'TypeFlag' => 0,
            'EndOfShape' => 0,
          );
        }
      } else {
        // Edge record (=1)
        $shapeRecord['StraightFlag'] = $this->getUIBit();
        if ($shapeRecord['StraightFlag']) {
          // StraightEdgeRecord
          $shapeRecord['Type'] = 2;
          $shapeRecord['NumBits'] = $this->getUIBits(4);
          $shapeRecord['GeneralLineFlag'] = $this->getUIBit();
          if ($shapeRecord['GeneralLineFlag'] == 0) {
            $shapeRecord['VertLineFlag'] = $this->getUIBits(1);
          }
          if ($shapeRecord['GeneralLineFlag'] == 1 || $shapeRecord['VertLineFlag'] == 0) {
            $shapeRecord['DeltaX'] = $this->getSIBits($shapeRecord['NumBits'] + 2);
          }
          if ($shapeRecord['GeneralLineFlag'] == 1 || $shapeRecord['VertLineFlag'] == 1) {
            $shapeRecord['DeltaY'] = $this->getSIBits($shapeRecord['NumBits'] + 2);
          }
        } else {
          // CurvedEdgeRecord
          $shapeRecord['Type'] = 3;
          $shapeRecord['NumBits'] = $this->getUIBits(4);
          $shapeRecord['ControlDeltaX'] = $this->getSIBits($shapeRecord['NumBits'] + 2);
          $shapeRecord['ControlDeltaY'] = $this->getSIBits($shapeRecord['NumBits'] + 2);
          $shapeRecord['AnchorDeltaX']  = $this->getSIBits($shapeRecord['NumBits'] + 2);
          $shapeRecord['AnchorDeltaY']  = $this->getSIBits($shapeRecord['NumBits'] + 2);
        }
      }
      $shapeRecords[] = $shapeRecord;
      if ($shapeRecord['Type'] == 0) {
        break;
      }
    }
    return $shapeRecords;
  }

  public function getGradient($tagType)
  {
    $gradient = array(
      'SpreadMode' => $this->getUIBits(2),
      'InterpolationMode' => $this->getUIBits(2),
      'NumGradients' => $this->getUIBits(4),
      'GradientRecords' => array(),
    );
    for ($i = 0; $i < $gradient['NumGradients']; ++$i) {
      $gradient['GradientRecords'][] = array(
        'Ratio' => $this->getUI8(),
        'Color' => ($tagType === 32 ? $this->getRGBA() : $this->getRGB()),
      );
    }
    return $gradient;
  }

  public function getKerningRecode($fontFlagsWideCodes)
  {
    if ($fontFlagsWideCodes) {
      return array(
        'FontKerningCode1' => $this->getUI16LE(),
        'FontKerningCode2' => $this->getUI16LE(),
        'FontKerningAdjustment' => $this->getSI16(),
      );
    } else {
      return array(
        'FontKerningCode1' => $this->getUI8(),
        'FontKerningCode2' => $this->getUI8(),
        'FontKerningAdjustment' => $this->getSI16(),
      );
    }
  }
}
