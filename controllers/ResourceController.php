<?php
namespace Power\Controllers;
use Phalcon\Mvc\Controller;
use PA;

class ResourceController extends Controller{
    /**
     * 如果需要输出内容，$need_out_contents 会被设置为正，否则设置成 false
     * @param string $file
     * @param int $time
     * @param bool $need_out_contents
     */
    private function setResponseHeader($file, $time, &$need_out_contents=true):void{
        $extends = explode('.', $file);
        $extend  = end($extends);
        $meats = [
            'html'=>'text/html',
            'htm'=>'text/html',
            'shtml'=>'text/html',
            'css'=>'text/css',
            'xml'=>'text/xml',
            'gif'=>'image/gif',
            'jpg'=>'image/jpeg',
            'jpeg'=>'image/jpeg',
            'js'=>'application/javascript',
            'atom'=>'application/atom+xml',
            'rss'=>'application/rss+xml',
            'mml'=>'text/mathml',
            'txt'=>'text/plain',
            'jad'=>'text/vnd.sun.j2me.app-descriptor',
            'wml'=>'text/vnd.wap.wml',
            'htc'=>'text/x-component',
            'png'=>'image/png',
            'tiff'=>'image/tiff',
            'tif'=>'image/tiff',
            'wbmp'=>'image/vnd.wap.wbmp',
            'ico'=>'image/x-icon',
            'jng'=>'image/x-jng',
            'bmp'=>'image/x-ms-bmp',
            'svgz'=>'image/svg+xml',
            'svg'=>'image/svg+xml',
            'webp'=>'image/webp',
            'woff'=>'application/font-woff',
            'woff2'=>'application/font-woff',
            'ttf'=>'application/font-ttf',
            'ear'=>'application/java-archive',
            'jar'=>'application/java-archive',
            'war'=>'application/java-archive',
            'json'=>'application/json',
            'hqx'=>'application/mac-binhex40',
            'doc'=>'application/msword',
            'pdf'=>'application/pdf',
            'ai'=>'application/postscript',
            'ps'=>'application/postscript',
            'eps'=>'application/postscript',
            'rtf'=>'application/rtf',
            'm3u8'=>'application/vnd.apple.mpegurl',
            'xls'=>'application/vnd.ms-excel',
            'eot'=>'application/vnd.ms-fontobject',
            'ppt'=>'application/vnd.ms-powerpoint',
            'wmlc'=>'application/vnd.wap.wmlc',
            'kml'=>'application/vnd.google-earth.kml+xml',
            'kmz'=>'application/vnd.google-earth.kmz',
            '7z'=>'application/x-7z-compressed',
            'cco'=>'application/x-cocoa',
            'jardiff'=>'application/x-java-archive-diff',
            'jnlp'=>'application/x-java-jnlp-file',
            'run'=>'application/x-makeself',
            'pm'=>'application/x-perl',
            'pl'=>'application/x-perl',
            'pdb'=>'application/x-pilot',
            'prc'=>'application/x-pilot',
            'rar'=>'application/x-rar-compressed',
            'rpm'=>'application/x-redhat-package-manager',
            'sea'=>'application/x-sea',
            'swf'=>'application/x-shockwave-flash',
            'sit'=>'application/x-stuffit',
            'tk'=>'application/x-tcl',
            'tcl'=>'application/x-tcl',
            'crt'=>'application/x-x509-ca-cert',
            'der'=>'application/x-x509-ca-cert',
            'pem'=>'application/x-x509-ca-cert',
            'xpi'=>'application/x-xpinstall',
            'xhtml'=>'application/xhtml+xml',
            'xspf'=>'application/xspf+xml',
            'zip'=>'application/zip',
            'dll'=>'application/octet-stream',
            'bin'=>'application/octet-stream',
            'exe'=>'application/octet-stream',
            'deb'=>'application/octet-stream',
            'dmg'=>'application/octet-stream',
            'img'=>'application/octet-stream',
            'iso'=>'application/octet-stream',
            'msm'=>'application/octet-stream',
            'msi'=>'application/octet-stream',
            'msp'=>'application/octet-stream',
            'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pptx'=>'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'kar'=>'audio/midi',
            'mid'=>'audio/midi',
            'midi'=>'audio/midi',
            'mp3'=>'audio/mpeg',
            'ogg'=>'audio/ogg',
            'm4a'=>'audio/x-m4a',
            'ra'=>'audio/x-realaudio',
            '3gp'=>'video/3gpp',
            '3gpp'=>'video/3gpp',
            'ts'=>'video/mp2t',
            'mp4'=>'video/mp4',
            'mpg'=>'video/mpeg',
            'mpeg'=>'video/mpeg',
            'mov'=>'video/quicktime',
            'webm'=>'video/webm',
            'flv'=>'video/x-flv',
            'm4v'=>'video/x-m4v',
            'mng'=>'video/x-mng',
            'asf'=>'video/x-ms-asf',
            'asx'=>'video/x-ms-asf',
            'wmv'=>'video/x-ms-wmv',
            'avi'=>'video/x-msvideo',
        ];
        
        $mtime = filemtime($file);
        $md5   = md5_file($file);
        $request_headers = [];
        foreach(getallheaders() as $k=>$v) $request_headers[strtolower($k)] = $v;
        $if_modified = $request_headers['if-modified-since'] ?? null;
        $none_match  = $request_headers['if-none-match']     ?? null;
        if($if_modified){
            $if_modified = strtotime($if_modified);
        }

        $need_out_contents = true;
        $content_type = $meats[$extend] ?? (function_exists('mime_content_type') ? mime_content_type($file) : ('application/'.$extend));
        $headers = [
            'x-powered-by: '.PA::$config['site.domain.logogram'].'/'.PA::$config['site.version'],
            'Content-Type: '.$content_type
        ];
        if(PA::$config['debug']) {
            $headers[] = 'DEBUG-Source-File: ' . $file;
            $headers[] = 'DEBUG-Modified: ' . " [$if_modified vs $mtime]";
            $headers[] = 'DEBUG-ETag: ' . "[$none_match vs $md5]";
        }else{
//            $headers[] = 'Expires: '.date(DATE_RFC1123, time() + $time);
//            $headers[] = 'Cache-Control: public, max-age='.$time;
            $headers[] = 'ETag: '.$md5;
            $headers[] = 'Last-Modified: '.date(DATE_RFC1123, $mtime);
        }
        foreach($headers as $header) header($header);
        
        if(!PA::$config['debug'] && ($if_modified || $none_match)){
            if($if_modified === $mtime || $none_match === $md5){
                header('HTTP/1.0 304 Not Modified.');
                $need_out_contents = false;
            }
        }
    }
    
    /**
     *
     * 渲染资源，会被缓存
     */
    public function renderAction():void{
        $this->view->disable();
        $dist_dirs = [
            POWER_BASE_DIR . 'views',
            POWER_BASE_DIR . 'public',
        ];
        
        if(PA::$config['module_path']){ # 添加模块下的文件
            $dists = glob(PA::$config['module_path'].'*/views',GLOB_ONLYDIR );
            if($dists) {
                array_unshift($dists, null);
                $dists[0] = &$dist_dirs;
                call_user_func_array('array_push',$dists);
            }
        }else{
            $dist_dirs[] = BASE_DIR .'views';
        }
        
        //添加插件的 dist
        foreach([POWER_BASE_DIR . 'plugins/',BASE_DIR . 'plugins/'] as $plugin_path){
            $dists = glob($plugin_path.'*/views',GLOB_ONLYDIR );
            if($dists) {
                array_unshift($dists, null);
                $dists[0] = &$dist_dirs;
                call_user_func_array('array_push',$dists);
            }
        }
        
        $url = $_SERVER['REQUEST_URI'].'?';
        $file = substr($url,0,strpos($url,'?'));
        
        foreach($dist_dirs as $dir){
//            echo $dir.$file,'<br/>';
            if(file_exists($dir.$file)){
                $this->setResponseHeader($dir.$file, 604800, $need_out_contents);
                if($need_out_contents) echo file_get_contents($dir.$file);
                return;
            }
        }
        header('HTTP/1.1 404 Not Found');
    }
}
