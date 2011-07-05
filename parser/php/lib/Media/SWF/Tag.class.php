<?php

class Media_SWF_Tag
{
  const END                               = 0;
  const SHOW_FRAME                        = 1;
  const DEFINE_SHAPE                      = 2;
  const FREE_CHARACTER                    = 3;
  const PLACE_OBJECT                      = 4;
  const REMOVE_OBJECT                     = 5;
  const DEFINE_BITS                       = 6;
  const DEFINE_BUTTON                     = 7;
  const JPEGTABLES                        = 8;
  const SET_BACKGROUND_COLOR              = 9;
  const DEFINE_FONT                       = 10;
  const DEFINE_TEXT                       = 11;
  const DO_ACTION                         = 12;
  const DEFINE_FONT_INFO                  = 13;
  const DEFINE_SOUND                      = 14;
  const START_SOUND                       = 15;
  const STOP_SOUND                        = 16;
  const DEFINE_BUTTON_SOUND               = 17;
  const SOUND_STREAM_HEAD                 = 18;
  const SOUND_STREAM_BLOCK                = 19;
  const DEFINE_BITS_LOSSLESS              = 20;
  const DEFINE_BITS_JPEG2                 = 21;
  const DEFINE_SHAPE2                     = 22;
  const DEFINE_BUTTON_CXFORM              = 23;
  const PROTECT                           = 24;
  const PATHS_ARE_POSTSCRIPT              = 25;
  const PLACE_OBJECT2                     = 26;
  const REMOVE_OBJECT2                    = 28;
  const SYNC_FRAME                        = 29;
  const FREE_ALL                          = 31;
  const DEFINE_SHAPE3                     = 32;
  const DEFINE_TEXT2                      = 33;
  const DEFINE_BUTTON2                    = 34;
  const DEFINE_BITS_JPEG3                 = 35;
  const DEFINE_BITS_LOSSLESS2             = 36;
  const DEFINE_EDIT_TEXT                  = 37;
  const DEFINE_VIDEO                      = 38;
  const DEFINE_SPRITE                     = 39;
  const NAME_CHARACTER                    = 40;
  const PRODUCT_INFO                      = 41;
  const DEFINE_TEXT_FORMAT                = 42;
  const FRAME_LABEL                       = 43;
  const SOUND_STREAM_HEAD2                = 45;
  const DEFINE_MORPH_SHAPE                = 46;
  const GENERATE_FRAME                    = 47;
  const DEFINE_FONT2                      = 48;
  const GENERATOR_COMMAND                 = 49;
  const DEFINE_COMMAND_OBJECT             = 50;
  const CHARACTER_SET                     = 51;
  const EXTERNAL_FONT                     = 52;
  const EXPORT_ASSETS                     = 56;
  const IMPORT_ASSETS                     = 57;
  const ENABLE_DEBUGGER                   = 58;
  const DO_INIT_ACTION                    = 59;
  const DEFINE_VIDEO_STREAM               = 60;
  const VIDEO_FRAME                       = 61;
  const DEFINE_FONT_INFO2                 = 62;
  const DEBUG_ID                          = 63;
  const ENABLE_DEBUGGER2                  = 64;
  const SCRIPT_LIMITS                     = 65;
  const SET_TAB_INDEX                     = 66;
  const FILE_ATTRIBUTES                   = 69;
  const PLACE_OBJECT3                     = 70;
  const IMPORT_ASSETS2                    = 71;
  const DEFINE_FONT_ALIGN_ZONES           = 73;
  const CSMTEXT_SETTINGS                  = 74;
  const DEFINE_FONT3                      = 75;
  const SYMBOL_CLASS                      = 76;
  const METADATA                          = 77;
  const DEFINE_SCALING_GRID               = 78;
  const DO_ABC                            = 82;
  const DEFINE_SHAPE4                     = 83;
  const DEFINE_MORPH_SHAPE2               = 84;
  const DEFINE_SCENE_AND_FRAME_LABEL_DATA = 86;
  const DEFINE_BINARY_DATA                = 87;
  const DEFINE_FONT_NAME                  = 88;
  const START_SOUND2                      = 89;
  const DEFINE_BITS_JPEG4                 = 90;
  const DEFINE_FONT4                      = 91;


  public static $names = array(
      0  => 'End',
      1  => 'ShowFrame',
      2  => 'DefineShape',
      3  => 'FreeCharacter',
      4  => 'PlaceObject',
      5  => 'RemoveObject',
      6  => 'DefineBits',
      7  => 'DefineButton',
      8  => 'JPEGTables',
      9  => 'SetBackgroundColor',
      10 => 'DefineFont',
      11 => 'DefineText',
      12 => 'DoAction',
      13 => 'DefineFontInfo',
      14 => 'DefineSound',
      15 => 'StartSound',
      16 => 'StopSound',
      17 => 'DefineButtonSound',
      18 => 'SoundStreamHead',
      19 => 'SoundStreamBlock',
      20 => 'DefineBitsLossless',
      21 => 'DefineBitsJPEG2',
      22 => 'DefineShape2',
      23 => 'DefineButtonCxform',
      24 => 'Protect',
      25 => 'PathsArePostscript',
      26 => 'PlaceObject2',
      28 => 'RemoveObject2',
      29 => 'SyncFrame',
      31 => 'FreeAll',
      32 => 'DefineShape3',
      33 => 'DefineText2',
      34 => 'DefineButton2',
      35 => 'DefineBitsJPEG3',
      36 => 'DefineBitsLossless2',
      37 => 'DefineEditText',
      38 => 'DefineVideo',
      39 => 'DefineSprite',
      40 => 'NameCharacter',
      41 => 'ProductInfo',
      42 => 'DefineTextFormat',
      43 => 'FrameLabel',
      45 => 'SoundStreamHead2',
      46 => 'DefineMorphShape',
      47 => 'GenerateFrame',
      48 => 'DefineFont2',
      49 => 'GeneratorCommand',
      50 => 'DefineCommandObject',
      51 => 'CharacterSet',
      52 => 'ExternalFont',
      56 => 'ExportAssets',
      57 => 'ImportAssets',
      58 => 'EnableDebugger',
      59 => 'DoInitAction',
      60 => 'DefineVideoStream',
      61 => 'VideoFrame',
      62 => 'DefineFontInfo2',
      63 => 'DebugID',
      64 => 'EnableDebugger2',
      65 => 'ScriptLimits',
      66 => 'SetTabIndex',
      69 => 'FileAttributes',
      70 => 'PlaceObject3',
      71 => 'ImportAssets2',
      73 => 'DefineFontAlignZones',
      74 => 'CSMTextSettings',
      75 => 'DefineFont3',
      76 => 'SymbolClass',
      77 => 'Metadata',
      78 => 'DefineScalingGrid',
      82 => 'DoABC',
      83 => 'DefineShape4',
      84 => 'DefineMorphShape2',
      86 => 'DefineSceneAndFrameLabelData',
      87 => 'DefineBinaryData',
      88 => 'DefineFontName',
      89 => 'StartSound2',
      90 => 'DefineBitsJPEG4',
      91 => 'DefineFont4',
  );
  
  public static function name($code)
  {
    return isset(self::$names[$code]) ? self::$names[$code] : "";
  }

  public
    $firstParentId = false;
  
  protected
    $root,
    $content,
    $code,
    $length,
    $longFormat,
    $offset,
    $characterId,
    $type,
    $_fields;

  public function __construct($code, $length, $longFormat, $reader, $root)
  {
    $this->code   = $code;
    $this->length = $length;
    $this->longFormat = $longFormat;
    $this->root = $root;

    $this->offset = $reader->getByteOffset();
    $this->parse($reader);

    if (!$this->isDisplayListTag() && $this->hasField('CharacterId')) {
      $this->characterId = $this->getField('CharacterId');
    }
  }

  public function isDisplayListTag()
  {
    return false;
  }

  public function isDefinitionTag()
  {
    if ($this->isDisplayListTag()) {
      return false;
    } 
    return $this->hasField('CharacterId');
  }

  public function getFirstParentTag()
  {
    return ($this->firstParentId) ? $this->root->getTagByCharacterId($this->firstParentId): $this->root;
  }

  public function getGroupName()
  {
    if ($this->root->useCompactSaveMode) {
      return $this->root->getGroupName();
    }
    $parent = $this->getFirstParentTag();
    if (empty($this->name)) {
      return $parent->getGroupName();
    }
    return $parent->getGroupName().$this->name.".";
  }

  public function getCharacterId()
  {
    return $this->characterId;
  }

  public function getElementIdString()
  {
    return 'cid_'.$this->getCharacterId();
  }

  public function getElementType()
  {
    return $this->type ? $this->type : "unknown";
  }

  public function getElementSavedUrl()
  {
    return false;
  }

  public function getDictionaryArray()
  {
    return array(
      'cid' => $this->getElementIdString(),
      'type'=> $this->getElementType(),
      'url' => $this->getElementSavedUrl(),
    );
  }

  public function getCode()
  {
    return $this->code;
  }

  public function getTagName()
  {
    return self::name($this->code);
  }

  public function getFields()
  {
    return $this->_fields;
  }

  public function hasField($field)
  {
    return empty($this->_fields[$field]) ? false : true;
  }

  public function getField($field)
  {
    return isset($this->_fields[$field]) ? $this->_fields[$field] : null;
  }

  public function setField($field, $value)
  {
    $this->_fields[$field] = $value;
  }

  public function parseContent($content)
  {
    $reader = new Media_SWF_Parser();      
    $reader->input($content);
    $this->parse($reader);
  }

  public function parse($reader)
  {
    $this->content = $reader->getData($this->length);
  }

  public function reset($reader)
  {
    $reader->byteAlign();
    list($byte_offset, $bit_offset) = $reader->getOffset();
    $reader->setOffset($this->offset, $bit_offset);
  }

  public function getRest($reader)
  {
    return $reader->getData($this->length - $reader->getByteOffset() + $this->offset);
  }

  public function write($write)
  {
    $content = $this->build();
    $this->length = strlen($content);

    $this->writeCodeAndLength($writer);
    $writer->putData($content);
  }

  public function writeCodeAndLength($writer)
  {
    $writer->putCodeAndLength(array(
      'Code'       => $this->code,
      'Length'     => $this->length,
      'LongFormat' => $this->longFormat,
    ));
  }

  public function build()
  {
    return $this->content;
  }

  public function dump($indent)
  {
    return array();
  }
}
