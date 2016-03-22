# pico_dlfile
Pico Plugin:contentフォルダ配下の任意ファイルをダウンロード可能にする

## 記事に追加する値

 * Download: ダウンロードしたいファイルのファイル名。,区切りで複数個指定可能。また、::で区切ることで、ダウンロードファイルのタイトルを指定できる。

ex).
    Download: ダウンロード::file.zip, file2.zip

### Pico1.0から使えるようになった記述
Pico1.0からメタデータがYAML形式に変更されたため、以下のような記述も可能です。

```yaml
  URL: 
    - リンクテキスト::リンクURL
    - リンクテキスト::リンクURL
```

または

```yaml
  URL: 
    - 
      text: リンクテキスト
      file: リンクURL
    - 
      text: リンクテキスト
      file: リンクURL
```

##  追加するTwig変数

 * download: ダウンロードするファイルのURL
  * url: ダウンロードファイルのURL
  * text: ダウンロードファイルを示すテキスト
  * external: ダウンロードファイルが外部リンクであった場合、true

##  コンフィグオプション
 * $config['dlfile']['text']:リンクテキストのデフォルト値。何も入力しなかった場合は、「Attached URL」となる。
