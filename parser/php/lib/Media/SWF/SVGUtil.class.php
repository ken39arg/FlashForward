<?php
class Media_SWF_SVGUtill
{
  public static function matrixToSVGTransform($matrix)
  {
    return array('matrix' => self::matrixToArray($matrix));
  }

  public static function matrixToArray($matrix)
  {
    return array(
      (double) ($matrix['HasScale']) ? $matrix['ScaleX'] : 1.00,
      (double) ($matrix['HasRotate']) ? $matrix['RotateSkew0'] : 0,
      (double) ($matrix['HasRotate']) ? $matrix['RotateSkew1'] : 0,
      (double) ($matrix['HasScale']) ? $matrix['ScaleY'] : 1.00,
      (double) $matrix['TranslateX'],
      (double) $matrix['TranslateY'],
    );
  }

  public static function cxformToArray($cxform)
  {
    return array(
      (double) (isset($cxform['RedMultTerm'])) ? $cxform['RedMultTerm'] / 256 : 1,
      (double) (isset($cxform['GreenMultTerm'])) ? $cxform['GreenMultTerm'] / 256 : 1,
      (double) (isset($cxform['BlueMultTerm'])) ? $cxform['BlueMultTerm'] / 256 : 1,
      (double) (isset($cxform['AlphaMultTerm'])) ? $cxform['AlphaMultTerm'] / 256 : 1, 
      (double) (isset($cxform['RedAddTerm'])) ? $cxform['RedAddTerm'] : 0, 
      (double) (isset($cxform['GreenAddTerm'])) ? $cxform['GreenAddTerm'] : 0, 
      (double) (isset($cxform['BlueAddTerm'])) ? $cxform['BlueAddTerm'] : 0, 
      (double) (isset($cxform['AlphaAddTerm'])) ? $cxform['AlphaAddTerm'] : 0,
    );
  }

}
