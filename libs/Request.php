<?php

/**
 * 
 * @author kasonyang <i@kasonyang.com>
 */

namespace Hiano\Request;

class Request {

    private $parameter, $post, $cookie , $files ,$server_vars;
    
    function __construct($parameter,$post,$files,$server_vars,$cookie) {
        $this->parameter = $parameter;
        $this->post = $post;
        $this->files = $files;
        $this->server_vars = $server_vars;
        $this->cookie = $cookie;
    }

    private static function getSubArray($arr, $keys = null) {
        $sub = NULL;
        if ($keys) {
            if(is_array($keys)){
                foreach ($keys as $v) {
                    $sub[$v] = $arr[$v];
                }
            }else{
                $sub = $arr[$keys];
            }
        } else {
            $sub = $arr;
        }
        return $sub;
    }

    /**
     * 返回请求URL的参数
     * @param string|array $names 参数名
     * @return string|array $names为字符串时返回单个结果,为数组或null时返回多个结果(数组)
     */
    function getParameter($names = null) {
        return self::getSubArray($this->parameter, $names);
    }

    /**
     * 返回表单的POST数据
     * @param string|array $names 参数名
     * @return string|array $names为字符串时返回单个结果,为数组或null时返回多个结果(数组)
     */
    function getPost($names = null) {
        return self::getSubArray($this->post, $names);
    }

    /**
     * 返回浏览器保存的Cookie
     * @param string|array $names
     * @return string|array $names为字符串时返回单个结果,为数组或null时返回多个结果(数组)
     */
    function getCookie($names = null) {
        return self::getSubArray($this->cookie, $names);
    }

    /**
     * 是否存在参数
     * @param string $name
     * @return bool
     */
    function hasParameter($name /*= null*/) {
        return isset($this->parameter[$name]);
    }

    /**
     * 是否存在指定的POST数据
     * @param string $name
     * @return bool
     */
    function hasPost($name = null) {
        if ($name){
            return isset($this->post[$name]);
        }else{
            return count($this->post) > 0;
        }
    }

    /**
     * 是否存在指定的Cookie
     * @param string $name
     * @return bool
     */
    function hasCookie($name = null) {
        if ($name){
            return isset($this->cookie[$name]);
        }else{
            return count($this->cookie) > 0;
        }
    }

    /**
     * 是否存在指定的POST或GET
     * @param string $name
     * @return boolean
     */
    function hasRequest($name){
        return $this->hasPost($name) or $this->hasParameter($name);
    }
    
    /**
     * 
     * 读取Request（POST或GET）
     * 
     * @param string|array $names
     * @return string|array
     */
    function getRequest($names=NULL){
        if($ret = $this->getPost($names)){
            return $ret;
        }else{
            return $this->getParameter($names);
        }
    }
    
    /**
     * 是否上传了文件
     * @param string $name
     * @return boolean
     */
    function hasFileList($name){
        return isset($this->files[$name]);
    }
    
    /**
     * 获得上传的文件
     * @param string $name
     * @return false|UploadedFileList|array(UploadedFileList)
     */
    function getFileList($name = NULL){
        if($name ===NULL){
            $ret = array();
            foreach($this->files as $fkey => $fval){
                $ret[$fkey] = $this->getFileList($fkey);
            }
            return $ret ?: FALSE;
        }else{
            if(!isset($this->files[$name])){
                return false;
            }
            return new UploadedFileList($this->files[$name]);
        }
    }
    
    /**
     * 是否为POST请求
     * @return bool
     */
    function isPost(){
        return $this->server_vars['REQUEST_METHOD'] == "POST";
    }
    
    /**
     * 是否为GET请求
     * @return bool
     */
    function isGet(){
        return $this->server_vars['REQUEST_METHOD'] == 'GET';
    }

}

class UploadedFileList{
    
    /**
     *
     * @var array(UploadedFile)
     */
    private $file_list;
    
    /**
     * 
     * @param array $file_info 结构同$_FILES['***']
     */
    function __construct($file_info) {
        $new_file_info = array();
        foreach($file_info as $f_key => $f_val){
            foreach((array)$f_val as $f_k => $f_v){
                $new_file_info[$f_k][$f_key] = $f_v;
            }
        }
        foreach($new_file_info as $fi){
            $this->file_list[] = new UploadedFile($fi);
        }
    }
    
    /**
     * 
     * @return int
     */
    function count(){
        return count($this->file_list);
    }
    
    /**
     * 
     * @return array(UploadedFile)
     */
    function getFiles(){
        return $this->file_list;
    }
    
}

class UploadedFile{
    
    private $file_info = array();
    
    private $error_map = array(
        UPLOAD_ERR_OK   =>  '文件上传成功',
        UPLOAD_ERR_INI_SIZE =>  '文件大小超出服务器限制',
        UPLOAD_ERR_FORM_SIZE => '文件大小超出限制',
        UPLOAD_ERR_PARTIAL  =>  '文件上传不完整',
        UPLOAD_ERR_NO_FILE  =>  '没有上传文件',
        UPLOAD_ERR_NO_TMP_DIR   =>  '服务器故障：找不到临时文件夹',
        UPLOAD_ERR_CANT_WRITE   =>  '服务器故障：无法写人文件'
    );
    
    /**
     * 
     * @param array $file_info 文件数组，包含元素(name,error,...)
     */
    function __construct($file_info) {
        $this->file_info = $file_info;
    }
    
    /**
     * 返回错误代码
     * @return int 错误代码对应php内置上传错误常数
     */
    function getErrorCode(){
        return $this->file_info['error'];
    }
    
    /**
     * 
     * @return string 错误描述
     */
    function getErrorDescription(){
        return $this->error_map[$code];
    }
    
    /**
     * 
     * @return boolean
     */
    function hasError(){
        $error = $this->file_info['error'];
        return $error !== 0;
    }
    
    /**
     * 
     * @return string
     */
    function getName(){
        return $this->file_info['name'];
    }
    
    /**
     * 
     * @return string
     */
    function getExt(){
        $path = $this->file_info['name'];
        $dotpos = strrpos($path, '.');
        if ($dotpos === FALSE) {
            return '';
        } else {
            return substr($path, $dotpos);
        }
    }
    
    /**
     * 
     * @return string
     */
    function getTemporaryPath(){
        return $this->file_info['tmp_name'];
    }
    
    /**
     * 
     * @param string $dest_path
     * @return boolean
     */
    function save($dest_path){
        return move_uploaded_file($this->getTemporaryPath(), $dest_path);
    }
    
}
