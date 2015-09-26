# pico_dlfile
Pico Plugin:contentフォルダ配下の任意ファイルをダウンロード可能にする

## 記事に追加する値

 * Download: ダウンロードしたいファイルのファイル名。,区切りで複数個指定可能。また、::で区切ることで、ダウンロードファイルのタイトルを指定できる。

ex).
    Download: ダウンロード::file.zip, file2.zip

##  追加するTwig変数

 * download: ダウンロードするファイルのURL
  * url: ダウンロードファイルのURL
  * text: ダウンロードファイルを示すテキスト
  * external: ダウンロードファイルが外部リンクであった場合、true

##  コンフィグオプション
 * $config['dlfile']['text']:リンクテキストのデフォルト値。何も入力しなかった場合は、「Attached URL」となる。
