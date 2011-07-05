<?php
set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__) . '/../lib');

require_once 'Media/SWF.class.php';
require_once 'Media/SWF/Parser.class.php';
require_once 'Media/SWF/Tag.class.php';
require_once 'Media/SWF/Tag/DisplayObjectContainer.class.php';
require_once 'Media/SWF/Tag/Stage.class.php';
require_once 'Media/SWF/Tag/DefineShape.class.php';
require_once 'Media/SWF/Tag/DefineShape2.class.php';
require_once 'Media/SWF/Tag/DefineShape3.class.php';
require_once 'Media/SWF/Tag/DefineSprite.class.php';
require_once 'Media/SWF/Tag/DoAction.class.php';
require_once 'Media/SWF/Tag/PlaceObject.class.php';
require_once 'Media/SWF/Tag/PlaceObject2.class.php';
require_once 'Media/SWF/Tag/RemoveObject.class.php';
require_once 'Media/SWF/Tag/RemoveObject2.class.php';
require_once 'Media/SWF/Tag/DefineBitsLossless.class.php';
require_once 'Media/SWF/Tag/DefineBitsLossless2.class.php';
require_once 'Media/SWF/Tag/DefineBits.class.php';
require_once 'Media/SWF/Tag/DefineBitsJPEG2.class.php';
require_once 'Media/SWF/Tag/SetBackgroundColor.class.php';
require_once 'Media/SWF/Tag/FrameLabel.class.php';
require_once 'Media/SWF/Tag/DefineFont.class.php';
require_once 'Media/SWF/Tag/DefineFont2.class.php';
require_once 'Media/SWF/Tag/DefineFontName.class.php';
require_once 'Media/SWF/Tag/DefineText.class.php';
require_once 'Media/SWF/Tag/DefineEditText.class.php';
require_once 'Media/SWF/Tag/DefineButton2.class.php';
