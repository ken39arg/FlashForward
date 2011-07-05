<?php

class Media_SWF_Tag_DoAction extends Media_SWF_Tag
{
  const END                    = 0x00;
  const NEXT_FRAME             = 0x04;
  const PREVIOUS_FRAME         = 0x05;
  const PLAY                   = 0x06;
  const STOP                   = 0x07;
  const TOGGLE_QUALITY         = 0x08;
  const STOP_SOUND             = 0x09;
  const ADD                    = 0x0A;
  const SUBTRACT               = 0x0B;
  const MULTIPLY               = 0x0C;
  const DIVIDE                 = 0x0D;
  const EQUAL                  = 0x0E;
  const LESS                   = 0x0F;
  const LOGICAL_AND            = 0x10;
  const LOGICAL_OR             = 0x11;
  const LOGICAL_NOT            = 0x12;
  const STRING_EQUAL           = 0x13;
  const STRING_LENGTH          = 0x14;
  const STRING_EXTRACT         = 0x15;
  const POP                    = 0x17;
  const TO_INTEGER             = 0x18;
  const GET_VARIABLE           = 0x1C;
  const SET_VARIABLE           = 0x1D;
  const SET_TARGET2            = 0x20;
  const STRING_ADD             = 0x21;
  const GET_PROPERTY           = 0x22;
  const SET_PROPERTY           = 0x23;
  const CLONE_SPRITE           = 0x24;
  const REMOVE_SPRITE          = 0x25;
  const TRACE                  = 0x26;
  const START_DRAG             = 0x27;
  const STOP_DRAG              = 0x28;
  const STRING_LESS            = 0x29;
  const _THROW                 = 0x2A;
  const CAST_OBJECT            = 0x2B;
  const _IMPLEMENTS            = 0x2C;
  const FSCOMMAND2             = 0x2D;
  const RANDOM                 = 0x30;
  const MB_STRING_LENGTH       = 0x31;
  const CHAR_TO_ASCII          = 0x32;
  const ASCII_TO_CHAR          = 0x33;
  const GET_TIME               = 0x34;
  const MB_STRING_EXTRACT      = 0x35;
  const MB_CHAR_TO_ASCII       = 0x36;
  const MB_ASCII_TO_CHAR       = 0x37;
  const DELETE                 = 0x3A;
  const DELETE_ALL             = 0x3B;
  const SET_LOCAL_VARIABLE     = 0x3C;
  const CALL_FUNCTION          = 0x3D;
  const _RETURN                = 0x3E;
  const MODULO                 = 0x3F;
  const _NEW                   = 0x40;
  const DECLARE_LOCAL_VARIABLE = 0x41;
  const DECLARE_ARRAY          = 0x42;
  const DECLARE_OBJECT         = 0x43;
  const TYPE_OF                = 0x44;
  const GET_TARGER             = 0x45;
  const ENUMERATE              = 0x46;
  const ADD2                   = 0x47;
  const LESS_THAN2             = 0x48;
  const EQUAL2                 = 0x49;
  const NUMBER                 = 0x4A;
  const STRING                 = 0x4B;
  const DUPLICATE              = 0x4C;
  const SWAP                   = 0x4D;
  const GET_MEMBER             = 0x4E;
  const SET_MEMBER             = 0x4F;
  const INCREMENT              = 0x50;
  const DECREMENT              = 0x51;
  const CALL_METHOD            = 0x52;
  const NEW_METHOD             = 0x53;
  const INSTANCE_OF            = 0x54;
  const ENUMERATE_OBJECT       = 0x55;
  const _AND                   = 0x60;
  const _OR                    = 0x61;
  const _XOR                   = 0x62;
  const SHIFT_LEFT             = 0x63;
  const SHIFT_RIGHT            = 0x64;
  const SHIFT_RIGHT_UNSIGNED   = 0x65;
  const STRICT_EQUAL           = 0x66;
  const GREATER_THAN           = 0x67;
  const STRING_GREATER_THAN    = 0x68;
  const _EXTENDS               = 0x69;
  const GOTO_FRAME             = 0x81;
  const GET_URL                = 0x83;
  const STORE_REGISTER         = 0x87;
  const DECLARE_DICTIONARY     = 0x88;
  const STRICT_MODE            = 0x89;
  const WAIT_FOR_FRAME         = 0x8A;
  const SET_TARGET             = 0x8B;
  const GOTO_LABEL             = 0x8C;
  const WAIT_FOR_FRAME2        = 0x8D;
  const DECLARE_FUNCTION2      = 0x8E;
  const _TRY                   = 0x8F;
  const WITH                   = 0x94;
  const PUSH                   = 0x96;
  const JUMP                   = 0x99;
  const GET_URL2               = 0x9A;
  const DECLARE_FUNCTION       = 0x9B;
  const _IF                    = 0x9D;
  const CALL                   = 0x9E;
  const GOTO_FRAME2            = 0x9F;


  public static $names = array(
      0x00 => 'End',
      0x04 => 'NextFrame',
      0x05 => 'PreviousFrame',
      0x06 => 'Play',
      0x07 => 'Stop',
      0x08 => 'ToggleQuality',
      0x09 => 'StopSound',
      0x0A => 'Add',
      0x0B => 'Subtract',
      0x0C => 'Multiply',
      0x0D => 'Divide',
      0x0E => 'Equal',
      0x0F => 'Less',
      0x10 => 'LogicalAnd',
      0x11 => 'LogicalOr ',
      0x12 => 'LogicalNot',
      0x13 => 'StringEqual',
      0x14 => 'StringLength',
      0x15 => 'StringExtract',
      0x17 => 'Pop',
      0x18 => 'ToInteger',
      0x1C => 'GetVariable',
      0x1D => 'SetVariable',
      0x20 => 'SetTarget',
      0x21 => 'StringAdd',
      0x22 => 'GetProperty',
      0x23 => 'SetProperty',
      0x24 => 'CloneSprite',
      0x25 => 'RemoveSprite',
      0x26 => 'Trace',
      0x27 => 'StartDrag',
      0x28 => 'StopDrag',
      0x29 => 'StringLess',
      0x2A => 'Throw',
      0x2B => 'CastObject',
      0x2C => 'implements',
      0x2D => 'FSCommand2',
      0x30 => 'Random',
      0x31 => 'MBStringLength',
      0x32 => 'CharToAscii',
      0x33 => 'AsciiToChar',
      0x34 => 'GetTime',
      0x35 => 'MBStringExtract',
      0x36 => 'MBCharToAscii',
      0x37 => 'MBAsciiToChar',
      0x3A => 'Delete',
      0x3B => 'DeleteAll',
      0x3C => 'SetLocalVariable',
      0x3D => 'CallFunction',
      0x3E => 'Return',
      0x3F => 'Modulo',
      0x40 => 'New',
      0x41 => 'DeclareLocalVariable',
      0x42 => 'DeclareArray',
      0x43 => 'DeclareObject',
      0x44 => 'TypeOf',
      0x45 => 'GetTarger',
      0x46 => 'Enumerate',
      0x47 => 'Add',
      0x48 => 'LessThan(typed)',
      0x49 => 'Equal(typed)',
      0x4A => 'Number',
      0x4B => 'String',
      0x4C => 'Duplicate',
      0x4D => 'Swap',
      0x4E => 'GetMember',
      0x4F => 'SetMember',
      0x50 => 'Increment',
      0x51 => 'Decrement',
      0x52 => 'CallMethod',
      0x53 => 'NewMethod',
      0x54 => 'InstanceOf',
      0x55 => 'EnumerateObject',
      0x60 => 'And',
      0x61 => 'Or',
      0x62 => 'XOr',
      0x63 => 'ShiftLeft',
      0x64 => 'ShiftRight',
      0x65 => 'ShiftRightUnsigned',
      0x66 => 'StrictEqual',
      0x67 => 'GreaterThan(typed)',
      0x68 => 'StringGreaterThan(typed)',
      0x69 => 'Extends',
      0x81 => 'GotoFrame',
      0x83 => 'GetURL',
      0x87 => 'StoreRegister',
      0x88 => 'DeclareDictionary',
      0x89 => 'StrictMode',
      0x8A => 'WaitForFrame',
      0x8B => 'SetTarget',
      0x8C => 'GotoLabel',
      0x8D => 'WaitForFrame(dynamic)',
      0x8E => 'DeclareFunction (with 256 registers)',
      0x8F => 'Try',
      0x94 => 'With',
      0x96 => 'Push',
      0x99 => 'Jump',
      0x9A => 'GetURL2',
      0x9B => 'DeclareFunction',
      0x9D => 'If',
      0x9E => 'Call',
      0x9F => 'GotoFrame2',
  );

  public static function name($code)
  {
    return isset(self::$names[$code]) ? self::$names[$code] : "";
  }

  public static $propertyIndex = array(
    0  => '_X',
    1  => '_Y',
    2  => '_xscale',
    3  => '_yscale',
    4  => '_currentframe',
    5  => '_totalframes',
    6  => '_alpha',
    7  => '_visible',
    8  => '_width',
    9  => '_height',
    10 => '_rotation',
    11 => '_target',
    12 => '_framesloaded',
    13 => '_name',
    14 => '_droptarget',
    15 => '_url',
    16 => '_highquality',
    17 => '_focusrect',
    18 => '_soundbuftime',
    19 => '_quality',
    20 => '_xmouse',
    21 => '_ymouse',
  );

  protected
    $_actions = array(),
    $_values  = array();

  public function hasField($field)
  {
    return $field === 'Actions' ? true : false;
  }

  public function getField($field)
  {
    return $field === 'Actions' ? $this->_actions : null;
  }

  public function parse($reader)
  {
    $valname = null;
    while (true) {
      $action_code = $reader->getUI8();
      $length = ($action_code & 0x80) ? $reader->getUI16LE() : 0;

      $contents = ($length > 0) ? $reader->getData($length) : null;

      switch ($action_code)
      {
        case 0x96: //PushData
          if ($valname != null) {
            if (!isset($this->_values[$valname])) {
              $this->_values[$valname] = array('Index' => count($this->_actions), 'Content' => mb_convert_encoding($contents, 'utf-8', 'sjis-win'));
            }
            $valname = null;
          } else {
            $valname = trim($contents);
          }
          break;
        //case 0x1D: // SetVariable
        //case 0x17: // Pop
        default:
          $valname = null;
          break;
      }
      $this->_actions[] = array('ActionCode' => $action_code, 'Length' => $length, 'Content' => $contents);

      if ($action_code == 0) { // END Tag
        break;
      }
    }
    $reader = null;
  }

  public function build()
  {
    $writer = new IO_Bit();

    foreach ($this->_actions as $index => $d)
    {
      $writer->putUI8($d['ActionCode']);
      if ($d['Length'] == 0) continue;

      $writer->putUI16LE($d['Length']);
      $writer->putData($d['Content']);
    }

    return $writer->output();
  }

  public function replaceValue($name, $value)
  {
    if (isset($this->_values[$name]))
    {
      $this->_values[$name]['Content'] = $value;
      $index = $this->_values[$name]['Index'];

      $data = "\x00" . mb_convert_encoding($value, 'sjis-win', 'utf-8') . "\x00";
      $len = strlen($data);

      $this->_actions[$index]['Length']  = $len;
      $this->_actions[$index]['Content'] = $data;
      return true;
    }
    return false;
  }

  public function getActionQueue()
  {
    $actions = new _ActionQueue();
    $point_offset = 0;
    foreach ($this->_actions as $act) {
      $r = null;
      // offsetの更新
      ++$point_offset;
      if ($act['ActionCode'] & 0x80 ) {
        $point_offset += 2;
        $point_offset += $act['Length'];
      }
      switch ($act['ActionCode']) {
        case self::PUSH:
          $r = new Media_SWF_Parser();
          $r->input($act['Content']);
          $type = $r->getUI8();
          switch ($type) {
            case 0:
              $data = $this->root->convertEncoding($r->getString());
              break;
            case 1:
              $data = $r->getFLOAT();
              break;
            case 4:
            case 5:
            case 8:
              $data = $r->getUI8();
              break;
            case 6:
              $data = $r->getDOUBLE();
              break;
            case 7:
              $data = $r->getUI32LE();
              break;
            case 9:
              $data = $r->getUI16LE();
              break;
            default:
              $data = null;
          }
          if ($type === 0 && !(strlen($data) > 1 && substr($data, 0, 1) == 0) && is_numeric($data)) {
            $data = intval($data);
          }
          $actions->add($act['ActionCode'], array($data), $point_offset);
          break;
        case self::_IF:
        case self::JUMP:
          $r = new Media_SWF_Parser();
          $r->input($act['Content']);
          $offset = $r->getSI16();
          $actions->add($act['ActionCode'], array($point_offset + $offset), $point_offset);
          break;
        case self::GET_URL2:
          $r = new Media_SWF_Parser();
          $r->input($act['Content']);
          $m = $r->getUIBits(2);
          $r->getUIBits(4);
          $lt_flag = $r->getUIBit();
          $vr_flag = $r->getUIBit();
          $method = ($m == 1) ? 'GET' : (($m == 2) ? 'POST' : 'NONE');
          $actions->add($act['ActionCode'], array($method, $lt_flag, $vr_flag), $point_offset);
          break;
        case self::GOTO_FRAME2:
          $r = new Media_SWF_Parser();
          $r->input($act['Content']);
          $r->getUIBits(6);
          $sceneBiasFlag = (boolean)$r->getUIBit();
          $playFlag      = (int)$r->getUIBit();
          if ($sceneBiasFlag) {
            $sceneBias = $r->getUI16LE();
          }
          $actions->add($act['ActionCode'], array($playFlag), $point_offset);
          break;
        case self::GOTO_FRAME:
          $r = new Media_SWF_Parser();
          $r->input($act['Content']);
          $frame = $r->getUI16LE();
          $actions->add($act['ActionCode'], array($frame, 0), $point_offset);
          break;
        case self::GOTO_LABEL:
          $r = new Media_SWF_Parser();
          $r->input($act['Content']);
          $frame = $r->getString();
          $actions->add($act['ActionCode'], array($frame, 0), $point_offset);
          break;
        case self::SET_TARGET:
          $r = new Media_SWF_Parser();
          $r->input($act['Content']);
          $target = $r->getString();
          $actions->add($act['ActionCode'], array($target), $point_offset);
          break;
        default:
          $actions->add($act['ActionCode'], array(), $point_offset);
          break;
      }
    }
    return $actions;
  }

  public function convertArray()
  {
    return $this->getActionQueue()->convertArray();
  }

}

class _ActionQueue
{
  protected
    $offset = 0,
    $queue = array();

  public function add($command, $arguments, $next_offset)
  {
    $this->queue[$this->offset] = array($command, $arguments);
    $this->offset = $next_offset;
  }

  public function convertArray()
  {
    $dump = $this->queue;
    //$dump[$this->offset] = array(Media_SWF_Tag_DoAction::END);
    return $dump;
  }
}
