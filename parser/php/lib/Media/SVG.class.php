<?php

class Media_SVG extends Media_SVG_Element
{
  public function __construct($width = null, $height = null)
  {
    parent::__construct('svg', array('width', 'height', 'viewBox'));
    if ($width  != null) $this->set('width',  $width);
    if ($height != null) $this->set('height', $height);
  }

  public function create()
  {
    $xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" />');
    $this->createElement($xml);
    return $xml;
  }

  public function save($filename, $gzip = false)
  {
    if ($gzip) {
      file_put_contents($filename, $this->toString(true));
    } else {
      $this->create()->asXML($filename);
    }
  }

  public function toString($gzip = false)
  {
    if ($gzip) {
      return gzencode($this->create()->asXML());
    }
    return $this->create()->asXML();
  }

  // Static factory
  public static function newElement($name)
  {
    $graphic_styles = array(
      'opacity',
      'fill',
      'fill-rule',
      'fill-opacity',
      'stroke',
      'stroke-width',
      'stroke-linecap',
      'stroke-linejoin',
      'stroke-miterlimit',
      'stroke-dasharray',
      'stroke-opacity',
    );
    $font_styles = array(
    );
    $name = strtolower($name);
    switch ($name) {
      case 'path':
        return new Media_SVG_Path('path', array_merge(array('d', 'transform', 'viewBox'), $graphic_styles));
      case 'svg':
        return new Media_SVG();
      case 'group':
      case 'g':
        return new Media_SVG_Element('g', array_merge(array('id', 'transform', 'viewBox'), $graphic_styles));
      case 'symbol':
        return new Media_SVG_Element('symbol', array('id', 'transform'));
      case 'title':
      case 'desc':
      case 'defs':
        return new Media_SVG_Element($name, array());
      case 'use':
      case 'image':
        return new Media_SVG_Element($name, array_merge(array('href', 'x', 'y', 'width', 'height', 'viewBox'), $graphic_styles));
      case 'a':
        return new Media_SVG_Element('a', array('href', 'target', 'title'));
      case 'circle':
        return new Media_SVG_Element('circle', array_merge(array('r', 'cx', 'cy'), $graphic_styles));
      case 'ellipse':
        return new Media_SVG_Element('ellipse', array_merge(array('rx', 'ry', 'cx', 'cy'), $graphic_styles));
      case 'rect':
        return new Media_SVG_Element('rect', array_merge(
                                     array('x', 'y', 'width', 'height', 'rx', 'ry'), 
                                     $graphic_styles));
      case 'line':
        return new Media_SVG_Element('line', array_merge(array('x1', 'y1', 'x2', 'y2'), $graphic_styles));
      case 'polyline':
        return new Media_SVG_Element('polyline', array_merge(array('points'), $graphic_styles));
      case 'polygon':
        return new Media_SVG_Element('polygon', array_merge(array('points'), $graphic_styles));
      case 'marker':
        return new Media_SVG_Element('marker', array_merge(
                                     array('id', 'markerWidth', 'markerHeight', 'refX', 'refY', 'orient', 'markerUnits'), 
                                     $graphic_styles));
      case 'lineargradient':
        return new Media_SVG_Element('linearGradient', array_merge(
                                     array('id', 'x1', 'y1', 'x2', 'y2', 'spreadMethod'), $graphic_styles));
      case 'radialgradient':
        return new Media_SVG_Element('radialGradient', array_merge(
                                     array('id', 'fx', 'fy', 'cx', 'cy', 'r', 'spreadMethod'), $graphic_styles));
      case 'stop':
        return new Media_SVG_Element('stop', array_merge(
                                     array('stop-color', 'offset'), $graphic_styles));
      case 'pattern':
        return new Media_SVG_Element('pattern', array_merge(
                                     array('id', 'x', 'y', 'width', 'height', 'patternUnits'), $graphic_styles));
      case 'clippath':
        return new Media_SVG_Element('clipPath', array_merge(array('id'), $graphic_styles));
      case 'mask':
        return new Media_SVG_Element('mask', array_merge(array('id'), $graphic_styles));
      case 'filter':
        return new Media_SVG_Element('filter', array_merge(
                                    array('id', 'x', 'y', 'width', 'height', 'filterUnits', 'primitiveUnits', 'filterRes', 'href'), 
                                    $graphic_styles));
      case 'fecolormatrix':
        return new Media_SVG_Element('feColorMatrix', array('x', 'y', 'width', 'height', 'result', 'in', 'type', 'class', 'style', 'values'));
      case 'text':
      case 'tspan':
        return new Media_SVG_Element($name, array('x', 'y', 'dx', 'dy',
                                     'font-size', 'font-family', 'font-style', 'font-weight',
                                     'text-anchor', 'fill', 'opacity'));
      case 'textpath':
        return new Media_SVG_Element('textPath', array('href', 'startOffset'));
      case 'font':
        return new Media_SVG_Element('font', array('horiz-origin-x','horiz-origin-y','horiz-adv-x'));
      case 'font-face':
        return new Media_SVG_Element('font-face', array('font-family','font-style','horiz-adv-x', 'font-weight'));
      case 'glyph':
        return new Media_SVG_Path('glyph', array('unicode', 'glyph-name', 'd', 'horiz-adv-x'));
    }
  }
}
