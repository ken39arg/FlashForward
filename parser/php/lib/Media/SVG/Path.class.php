<?php

// 三次ベジェ曲線は対応せず
class Media_SVG_Path extends Media_SVG_Element
{ 
  public static
    $moveTo           = 'M',
    $closePath        = 'Z',
    $lineTo           = 'L',
    $curveTo          = 'Q';

  protected
    $direction = "+",
    $current = null,
    $pathDatas = array();

  public function __construct($name = null, $attribute = null)
  {
    parent::__construct($name, $attribute);
    $this->pathDatas = array(
      "+" => new Media_SVG_Path_Data("+"),
      "-" => new Media_SVG_Path_Data("-"),
    );
  }

  public function count()
  {
    return count($this->pathDatas["+"]->data) + count($this->pathDatas["-"]->data);
  }

  public function direction($v)
  {
    if ($v == false || $v == '-') {
      $this->direction = "-";
    } else {
      $this->direction = "+";
    }
    return $this;
  }

  public function getTo($x, $y)
  {
    $toX = ($this->direction == "+") ? $x : $this->pathDatas[$this->direction]->currentX;
    $toY = ($this->direction == "+") ? $y : $this->pathDatas[$this->direction]->currentY;
    $this->pathDatas[$this->direction]->currentX = $x;
    $this->pathDatas[$this->direction]->currentY = $y;
    return array($toX, $toY);
  }

  public function moveTo($x, $y)
  {
    list($toX, $toY) = $this->getTo($x, $y);
    if ($this->direction == "+" || count($this->pathDatas[$this->direction]->data) > 0) {
      $this->addPoint(array(self::$moveTo, $toX, $toY));
    }
    return $this;
  }

  public function closePath()
  {
    return $this->addPoint(array(self::$closePath));
  }

  public function lineTo($x, $y)
  {
    list($toX, $toY) = $this->getTo($x, $y);
    return $this->addPoint(array(self::$lineTo, $toX, $toY));
  }

  public function curveTo($cx, $cy, $x, $y)
  {
    list($toX, $toY) = $this->getTo($x, $y);
    return $this->addPoint(array(self::$curveTo, $cx, $cy, $toX, $toY));
  }

  public function createElement(&$xml = null)
  {
    $data = $this->createData();
    $this->set('d', implode(" ", $data));
    parent::createElement($xml);
  }

  public function createArray()
  {
    $data = $this->createData();
    $this->set("d", $data);
    return parent::createArray();
  }

  protected function createData()
  {
    $data = array();
    $command = "";
    $pathDatas = array_merge($this->pathDatas["+"]->getData(), $this->pathDatas["-"]->getData());
    foreach ($pathDatas as $path_data) {
      $_command = array_shift($path_data);
      if (count($data) === 0 ) {
        $command = $_command;
        $data[] = $command;
      } elseif (array_slice($data, -2) === $path_data) {
        // 同一地点は無視
        continue;
      } elseif ($_command !== $command) {
        // コマンド変更
        $command = $_command;
        $data[] = $command;
      } elseif ($command == self::$moveTo) {
        // moveToが2回続いたら前のMoveToをカット
        array_pop($data);
        array_pop($data);
      }
      $data = array_merge($data, $path_data);
    }
    return $data;
  }

  protected function addPoint($point)
  {
    $this->pathDatas[$this->direction]->add($point);
    return $this;
  }
}

class Media_SVG_Path_Data
{
  public
    $direction = true,
    $currentX = 0,
    $currentY = 0,
    $data = array();

  public function __construct($direction) 
  {
    $this->direction = ($direction == "-") ? false: true;
  }

  public function add($data)
  {
    $this->data[] = $data;
  }

  public function getData()
  {
    if (count($this->data) == 0) {
      return array();
    }
    if ($this->direction) {
      return $this->data;
    } else {
      return array_merge(array(array("M", $this->currentX, $this->currentY)), array_reverse($this->data));
    }
  }
}
