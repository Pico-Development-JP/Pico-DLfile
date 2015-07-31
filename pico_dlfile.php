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
class Pico_DLFile{

  private $base_url;
  
  private $currentpath;
  
  private $content_dir;
  
  private $download_text;

  public function config_loaded(&$settings) {
    $this->base_url = $settings['base_url'];
    $this->content_dir = $settings['content_dir'];
    $this->download_text = empty($settings['dlfile']['text']) ? "Download" : $settings['dlfile']['text'];
  }

  public function request_url(&$url)
  {
    $this->currenturl = $url;
  }
    
  public function before_read_file_meta(&$headers)
  {
  	$headers['download'] = 'Download';
  }
	
	public function get_page_data(&$data, $page_meta)
	{
    $file_url = substr($data["url"], strlen($this->base_url));
    if($file_url[strlen($file_url) - 1] == "/") $file_url .= 'index';
    $filematch = preg_match('/^(.+\/)[\w\.-]+?$/', $file_url, $parts);
    
    if(!empty($page_meta['download'])){
      $downloads = explode(",", $page_meta['download']);
      $downloadlist = array();
      // ダウンロード要素のリストアップ
      for($i = 0; $i < count($downloads); $i++){
        $n = explode("::", $downloads[$i], 2);
        $name = count($n) >= 2 ? $n[0] : $this->download_text;
        $path = count($n) >= 2 ? $n[1] : $downloads[$i];
        $e = false;
        if (strlen($path) > 0 && $filematch) {
          if($path[0] == '.'){
            // 拡張子のみ＝コンテンツ名.拡張子に変換
            $u = $this->base_url . "/" . $this->content_dir . "$parts[0]$path" . '?download';
          }else if(!preg_match('/^(https?|ftp)/', $path)){
            // 通常のファイル＝ベースURLを付与してそのままダウンロードURLとする
            $u = $this->base_url . "/" . $this->content_dir . "$parts[1]$path" . '?download';
          }else{
            // httpまたはftpのURL。
            $u = $path;
            $e = true;
          }
          $downloadlist[] = array('url' => $u, 'text' => $name, 'external' => $e);
        }
      }
      $data['download'] = $downloadlist;
    }
  }
    
  public function before_render(&$twig_vars, &$twig) {
    // Pico内部でクエリ文字列が切り取られてしまうため、生のリクエストURIを使用
    $urlpart = parse_url($_SERVER['REQUEST_URI']);
    
		$path = ROOT_DIR . $this->content_dir . $urlpart['path'];
    if ($urlpart['query'] == "download" && file_exists($path)) {
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
    $twig_vars['download_url'] = $urlpart['path'];
  }
}

?>