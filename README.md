FlashForward (FlashLite to HTML5 Animation project)
====================================

概要
------------------------------------

FlashLite1.1相当のSWFをHTML5で動くアニメーションにコンバートします. 

特にFlashが動かないiOS上で最も最適されて動くことを意識しています



ディレクトリ構成
------------------------------------

    ├── docs    -- ドキュメント
    ├── parser  -- パーサープログラムを言語別に配置します
    │   └── php
    ├── runtime -- ランタイムプログラムを言語別に配置します
    │   └── javascript
    └── sample
        ├── ff  -- デフォルトでパース後の中間コードの保存ディレクトリです
        └── swf -- サンプルSWFファイルです



サンプルの実行
------------------------------------
    
    $ sh sample/build.sh

`sample/ff`以下に`sample/swf`下のswfがパースされて展開されます

`runtime/javascript`をブラウザで確認できるようにして、runtime/javascript/index.phpにアクセスしてください

またApacheの場合は.htaccessを設置しておりますが、そうでない場合は、svg及びsvgzをsvgとして認識できるように設定してください



プログラムの構成
------------------------------------

クライアントサイドでの負荷軽減、データの分散、特にFlashの動的生成時の最適化を意識し、
中間コードの仕様に従って、パーサープログラムと、ランタイムプログラムで役割を分担しています

現在は下記の構成でそれぞれを実装しています

* パーサープログラム PHP5.3
* ランタイムプログラム JavaScript

なお、JavaScriptはSVCとcanvasで実行できますが、現在のところ、SVGが最適です. 
(2011/07/05現在、canvasにはまだ不具合も有ります)

PHPプログラムはMedia_SWF(https://github.com/ken39arg/Media_SWF)に大幅に修正を加えたものになります。
Media_SWFとの互換はなくなっている可能性が有ります



データコンバート
------------------------------------

データコンバートは下記コマンドを実行します

    $ php parser/php/bin/convert_swf.php `path to source swf` `path to output target dir`

なおサンプルのswfは、下記のコマンドで一括コンバートします

    $ sh bin/build.sh

コンバートしたSWFは下記の構成で複数のファイルに変換されます

    dir/
      - index.json
      - defines/
          - *sprite.json
          - *sprite.svgz
          - *font.svgz
          - *bitmap.jpeg
          - *bitmap.png

より詳細な中間コードの仕様は docs/SPEC.md を参考にしてください



PHPコンバートスクリプトのオプション
------------------------------------

    $ php parser/php/bin/convert_swf.php --help

Usage: $ /usr/bin/env php parser/php/bin/convert_swf.php <option> [swfname] [output directory]

Options:
  --help, -h, -?                 ヘルプを表示します
  --version, -v                  バージョンを表示します
  --compress, -c                 圧縮オプションを使います(SVGをzlib圧縮してsvgzとします)
  --compact, --minimum, -m       ファイル最小化オプションを使います(ムービークリップによらず可能な限りファイル数を最小にします)
  --swf, -f                      変換対象のSWFファイル名(パラメータでも構いません)
  --output-dir, --save-dir, -o   保存先ディレクトリ(パラメータでも構いません)

Arguments:
  swfname                        変換対象のSWFファイル名(Optionでも構いません)
  output directory               保存先ディレクトリ(Optionでも構いません)



JavaScriptのminify
------------------------------------

JavaScriptのディレクトリに移動すると下記のMakeを実行できます

    $ make all           --- minと同様
    $ make build         --- .jsをdistディレクトリに1ファイルに結合
    $ make min           --- buildした.jsをGoogle Closure Compilerでminify
    $ make install       --- minifyしたjsをbinディレクトリにバージョンに分けて配置
    $ make clean         --- dist dirをrm
    $ make recompile     --- make clean all



ボタン操作の代替策
------------------------------------

ボタンタグには対応しておりませんが、FlashLite1.1のcall()をJavaScriptから明示的に実行する事で、
ユーザーアクションに対応させる事が出来ます。


    var player = new FlashForward.Player("path/to/index.json", "screen", "svg");
    player.play();

    document.onkeydown = function (ev) {
      switch (ev.keyCode) {
      case 13:
        if (player.context.stage) {
          player.context.stage.callAction("click");
        }
        break;
      }
    };
