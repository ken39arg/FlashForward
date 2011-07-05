<?php
class Media_SVG_Element
{
  protected
    $attribute_values = array(),
    $name             = null,
    $value            = null,
    $attribute        = array('id', 'class', 'filter', 'style', 'transform'),
    $node             = array();

  public function __construct($name = null, $attribute = null)
  {
    if ($name != null)
      $this->name = $name;
    
    if ($attribute != null) {
      if (!is_array($attribute)) {
        $attribute = array($attribute);
      }
      $this->attribute = array_unique(array_merge($this->attribute, $attribute));
    }
  }

  public function __get($name)
  {
    return $this->get($name);
  }

  public function __set($name, $value)
  {
    $this->set($name, $value);
  }

  public function __isset($name)
  {
    return $this->has($name);
  }

  public function __unset($name)
  {
    $this->remove($name);
  }

  public function get($name)
  {
    $name = str_replace("-", "_", $name);
    return (isset($this->attribute_values[$name])) ? $this->attribute_values[$name]: null;
  }

  public function set($name, $value)
  {
    $name = str_replace("-", "_", $name);
    $_name = str_replace("_", "-", $name);
    if (!in_array($_name, $this->attribute)) {
      $this->attribute[] = $_name;
    }
    $this->attribute_values[$name] = $value;
    return $this;
  }

  public function remove($name)
  {
    $name = str_replace("-", "_", $name);
    unset($this->value[$name]);
    return $this;
  }

  public function has($name)
  {
    $name = str_replace("-", "_", $name);
    return (isset($this->attribute_values[$name])) ? true: false;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getValue()
  {
    return $this->value;
  }

  public function setValue($value)
  {
    $this->value = $value;
    return $this;
  }

  public function addNode($node)
  {
    if ($node instanceof Media_SVG_Element)
      $this->node[] = $node;
    else 
      throw new Exception('SVG node must instanceof Media_SVG_Element');

    return $this;
  }

  public function createElement(&$xml = null)
  {
    if ($this->name == null)
      throw new Exception('SVG Element name isnot set');

    if ($xml == null)
      $xml = new SimpleXMLElement("<{$this->name}>{$this->value}</{$this->name}>");

    foreach ($this->attribute as $attr) {
      if ($this->has($attr)) {
        $v = $this->get($attr);
        $ns = null;
        switch ($attr) {
          case "filter";
            $v = (substr($v, 0, 3) == 'url') ? $v : "url(#{$v})";
            break;
          case "style";
            if (is_array($v)) {
              $v = "";
              foreach ($this->get('style') as $_n => $_v) 
                $v .= "$_n:$_v;";
            }
            break;
          case "transform";
            if (is_array($v)) {
              $v = "";
              foreach ($this->get('transform') as $_n => $_v) {
                if ($v != "") $v .= " ";
                if (is_array($_v)) {
                  $v .= strtolower($_n) . "(" . implode(",", $_v) . ")";
                } else {
                  $v .= $_v;
                }
              }
            }
            break;
          case "points";
            if (is_array($v)) {
              $v = "";
              foreach ($this->get('points') as $p) {
                if ($v != "") $v .= " ";
                if (is_array($_v)) {
                  $v .= implode(" ", $p);
                } else {
                  $v .= $p;
                }
              }
            }
            break;
          case "href":
          case "link":
          case "xlink:href":
            $attr = "xlink:href";
            $ns   = "http://www.w3.org/1999/xlink";
            break;
          case "class":
            if (is_array($v))
              $v = implode(" ", $v);
        }
        $xml->addAttribute($attr, $v, $ns);
      }
    }

    foreach ($this->node as $node) {
      if ($node instanceof Media_SVG_Element)
        $node->createElement($xml->addChild($node->getName(), htmlspecialchars($node->getValue())));
    }
  }

  public function createArray()
  {
    if ($this->name == null)
      throw new Exception('SVG Element name isnot set');

    $array = array("t" => $this->name); 
    if ($this->value) {
      $array["v"] = $this->value;
    }

    foreach ($this->attribute as $attr) {
      $v = $this->get($attr);
      if ($v) {
        $ns = null;
        switch ($attr) {
          case "filter";
            $v = (substr($v, 0, 3) == 'url') ? $v : "url(#{$v})";
            break;
        }
        $array[$attr] = $v;
      }
    }
    if (count($this->node) > 0) {
      $array["child"] = array();
    }
    foreach ($this->node as $node) {
      if ($node instanceof Media_SVG_Element)
        $array["child"][] = $node->createArray();
    }
    return $array;
  }
}
