<?php
/**
 * Pico DLFile Plugin
 * コンテンツフォルダに配置したファイルをダウンロードする
 *
 * @author TakamiChie
 * @link http://onpu-tamago.net/
 * @license http://opensource.org/licenses/MIT
 * @version 1.0
 */
class Pico_DLFile extends AbstractPicoPlugin{

  protected $enabled = false;

  private $currentpath;
  
  private $download_text;

  public function onConfigLoaded(array &$config)
  {
    $this->download_text = empty($config['dlfile']['text']) ? "Download" : $config['dlfile']['text'];
  }

  public function onRequestUrl(&$url)
  {
    $this->currenturl = $url;
  }

  public function onMetaHeaders(array &$headers)
  {
  	$headers['download'] = 'Download';
  }

  public function onSinglePageLoaded(array &$pageData)
  {
    $page_meta = $pageData['meta'];
    $base_url = $this->getBaseUrl();
    $file_url = substr($pageData["url"], strlen($base_url));
    $file_url_len = strlen($file_url);
    if($file_url_len && $file_url[$file_url_len - 1] == "/")
    {
      $file_url .= 'index';
    }
    $filematch = preg_match('/^(.+\/)[\w\.-]+?$/', $file_url, $parts);
    
    if(!empty($page_meta['download'])){
      if(is_array($page_meta['download']))
      {
        $downloads = $page_meta['download'];
      }else{
        $downloads = explode(",", $page_meta['download']);
      }
      $downloadlist = array();
      // ダウンロード要素のリストアップ
      for($i = 0; $i < count($downloads); $i++){
        $name;
        $path;
        if(is_array($downloads[$i])){
          $path = $downloads[$i]['file'];
          $name = $downloads[$i]['text'];
        }else{
          $n = explode("::", $downloads[$i], 2);
          $name = count($n) >= 2 ? $n[0] : $this->download_text;
          $path = count($n) >= 2 ? $n[1] : $downloads[$i];
        }
        $e = false;
        if (strlen($path) > 0 && $filematch) {
          if($path[0] == '.'){
            // 拡張子のみ＝コンテンツ名.拡張子に変換
            $u = $base_url . "dl/$parts[0]$path";
          }else if(!preg_match('/^(https?|ftp)/', $path)){
            // 通常のファイル＝ベースURLを付与してそのままダウンロードURLとする
            $u = $base_url . "dl/$parts[1]$path";
          }else{
            // httpまたはftpのURL。
            $u = $path;
            $e = true;
          }
          $downloadlist[] = array('url' => $u, 'text' => $name, 'external' => $e);
        }
      }
      $pageData['download'] = $downloadlist;
    }
  }
    
  public function onPageRendering(Twig_Environment &$twig, array &$twigVariables, &$templateName)
  {
    if(strpos($this->currenturl, "dl/") === 0) {
      $content_dir = $this->getConfig("content_dir");
      $path = $content_dir . substr($this->currenturl, 3);
      if (file_exists($path)) {
        // ダウンロード
        $pathinfo = pathinfo($path);
        header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
        header('Content-Type: application/x-download');
        header('Content-Transfer-Encoding: Binary');
        header(sprintf('Content-disposition: attachment; filename="%s"', $pathinfo['basename']));
        ob_clean(); // clean the output buffer
        flush();
        echo readfile($path);
        exit;
      }
    }
  }
}

?>