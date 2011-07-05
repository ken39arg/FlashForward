FlashForward 中間コード仕様 (Draft version 0.1)
===============

FlashForwardはSWFファイルをWebKitブラウザ上で動かすためのツールで、
HTML5によるクライアントサイドランタイムとサーバー上で中間コンパイルするためのライブラリの2つで構成されます。

SWFはサーバー上で1度(JSON, SVG, PNG, JPEG)のいずれかに変換されます。

FlashForwardは日本のフィーチャーフォン向けに制作されたflashlite1.1コンテンツをiPhone上で再利用し、
クロスプラットフォームコンテンツの制作を容易にすることを目的にしています。


特徴
---------------

* サーバーサイドとクライアントサイドで処理の分担をするため、
  JavaScript単体で実現されたランタイムよりもクライアントのCPUリソースを抑えることができます。
* 中間コードはキャッシュ可能な単位で分割できるため、
  トラフィックを効率化します(全体のデータサイズはSWFに遠く及びません)
* モバイルにおけるswfコンテンツの再利用を目的にしているため、
  iPhone/iPodTouch/iPad用mobile safari以外はサポートしませんがその分高速な動作を実現します。


制限事項
--------------

目的に最適化するために下記の制約があります。

* SWFバージョン (swf-ver4)

* FlashLite1.1相当の機能に限定する

* 下記の機能はサポートしない

  * モーフィング(シェイプトゥイーン)
  * グラデーション(正確に再現する事は出来ません)
  * サウンド
  * 着色(canvas版では対応しています)
  * 加算アルファブレンド(canvas版では対応しています)
  * ボタン(次期バージョンで対応予定)

* ActionScriptの制約

  Flashの仕様を埋めるため、いくつかの関数は特殊な使い方が必要です

  * 全角を含むsubstring

    // 下記の文字数判定コードを入れて長さのカウントに使用しま
    a = "あ"
    len = (length(a) == 1) ? 1 : 2;

    // 使用例
    a = substring(str, 1, len);


中間コード仕様
---------------

SWFは下記のファイルに変換されます.

* ベクターデータのシェイプはSVGに変換されます.
* ビットマップデータは、JPEGまたはPNGに変換されます.
* DisplayObjectContainer(Sprite)はJSONに変換されます
* サイズについて1pxあたり20として扱います。つまり240pxは4800と表されます。

### Top level

トップレベルのインデックスはswfに対して必ず一対一で存在する必要があります

#### Format

* JSON

#### Spec

    {
      "meta":  // META Data part
      {
        "version":     4.0, // <= 4.0
        "fcon": 100, // root のフレーム数
        "fps":         12,  // frame rate
        "bgcolor":     "#ffffff", // 背景色
        "size":             // 表示領域のサイズ
          [ 
            0,        // x minimum position
            0,        // y minimum position
            4800,     // x maximum position
            6000,     // y maximum position
          ],
      },
      "dict": // charactor idと対応するdefine定義のディクショナリ
      [
        { // putturn bitmap  //Bitmapは1オブジェクトに付き1ファイル
          "cid": "cid_1",
          "type": "bitmap",
          "url": "/path/to/bitmap.jpg",
          "width": 100,      // Bitmapのみwidthとheightパラメータ
          "height": 100,
        },
        { // svg path // SVGは複数のオブジェクトを１つ以上のSVGファイルにまとめて内包 `/svg/defs[0]/*[@id=cid]`
          "cid": "cid_2",
          "type": "shape",
          "url": "/path/to/shapes.svg",
        },
        { // json path // JSONは複数のオブジェクトを１つ以上のJSONにまとめて内包 json.{cid}
          "cid": "cid_3",  
          "type": "sprite",
          "url": "/path/to/sprite.json",
        },
        ...
      ],
      "ctls":  // top level data mapping
      [
        (ctlsはSpriteと同様です)
      ]
    }


### Bitmap

`DefineBitLossless`タグはPNGファイルに変換されます.
`DefineBits`、`DefineBitsJPEG`タグは`JPEG`ファイルに変換されます.

Bitマップは画像ファイルに変換されますが、よりわかりやすいようにSVGのタグを通します

* example

    <image xlink:href="/path/to/bitmap.jpg" width="240" height="150"/>


### Shape

`Shape`タグはSVGに変換され、`<defs>`タグに内包された、`<path>`と`<g>`などのタグで表現されます
`Shape`は必ず一意な`id`属性をもちます。また、1つのSVGファイルに複数の`Shape`が内包されます

#### Format

* SVG

#### SVG convert Spec


    <svg xmlns="http://www.w3.org/2000/svg"
      xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1">
      <defs>
        <!-- Bitmap example -->
        <image id="cid_1" xlink:href="/path/to/bitmap.jpg" width="240" height="150"/>
        <!-- tiny Shape -->
        <path id="cid_2" d="M 5 5 L 5 10 10 10 10 5 Z" fill="#ff0000"/>
        <!-- groupd Shape -->
        <g id="cid_3">
          <path d="M 10 10 L 15 20" fill="none" stroke="#ff0000"/>
          <path d="M 15 20 L 30 30 15 30 Z" fill="#0000ff"/>
        </g>
        <!-- using bitmap Shape -->
        <use id="cid_4" xlink:href="#cid_1" transform="transrate(30, 30)" />
      </defs>
    </svg>


### Text

textは動的テキスト及び、静的テキストを扱いますが、フォントの埋め込みには対応しません

#### Format

* JSON 

#### SPEC 

    {
      "meta":
      {
        "cid":         "cid_9"  // unique charactor id
        "size":          // 表示領域のサイズ
          [ 
            0,        // x minimum position
            0,        // y minimum position
            4800,     // x maximum position
            6000,     // y maximum position
          ],
      },
      "style":
      {
        "word-wrap": true,
        "multiline": true,
        "border":    true,
        "size":      220,
        "color":     "#ff0000",
        "opacity":   1.0,
        "align":     "left",
        "left-mergin":  20,
        "right-mergin": 20,
        "indent":       20,
        "leading":      0,
        "font":        "_ゴシック",
        "italic":      true,
        "bold":        true,
      },
      "text": "初期テキスト",
      "variable": "変数名", // 変数名が設定されている場合は変数が変わると動的に内容が変わります
    }

### Sprite(MovieClip)

Spriteは1フレームごとの位置を表すJSONに変換されます。
SWFではフレーム単位で差分のみをもっていますが、全データをもちます


#### Format

* JSON

#### SPEC

    {
      "meta":  // META Data part
      {
        "cid":         "cid_9"  // unique charactor id
        "fcon": 100,     // top level root frame count
      },
      "ctls":  // top level data mapping
      [
        {                                      // シーン毎に1オブジェクト
          'label': 'hoge',  // not required
          'd': [
            {
              "dp":      "1",               // shape depth
              "cid":        "cid_1",           // charactor id in dictionary
              "mtx":     [1,0,0,1,10,10],   // matrix transform
              "name":       "hoge",            // インスタンス名がついている場合
              "cx":     [                  // color transform
                1, // multiply red
                1, // multiply green
                1, // multiply blue
                1, // multiply alpha
                0, // offset red
                0, // offset green
                0, // offset blue
                0  // offset alpha
              ],
              "cdp": 10,                // クリップレイヤーの場合及ぼす範囲まで
            },
            ...
          ],
          "rm": [1, 3, 5, ...],           // このシーンでディスプレイ上からなくなったオベジェクトのdepth
          "act":[                           // スクリプト部
            {
              "1": "set(hoge, 1)",
              "2": "start()",
              "3": "stop()",
              "4": "gotoFrame(concat(label,get(num)), 1)",
            },
            ...
          ],
        },
        ...
      ]
    }



ActionScript
------------

ActionScriptはパースの必要はありませんがスタックマシンを実装する必要があります。

### フォーマット

    {
      (offset番号) : [
        (ActionCode),
        [arguments]
      ], ...
    }

argumentsには、ActionRecordHeader以降に続くフィールドが入ります

スタックマシンの仕様は、SWFフォーマットに準拠します

参考資料
----------

* [Adobe SWF File Format Specification](http://www.adobe.com/devnet/swf.html)

  SWFファイルフォーマット

* [SVG RFC](http://www.w3.org/TR/SVG11/index.html)

  W3C SVG仕様

* [Adobe Wallaby](http://labs.adobe.com/technologies/wallaby/)

  fla convert to html5 Flash CS5 plugin.

* [gordon](https://github.com/tobeytailor/gordon/)

  SWFをHTML5に変換しますがSpriteには対応していません

* [Smokescreen](http://smokescreen.us/)

  SWFをJSだけでHTML5に変換します

