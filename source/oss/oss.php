<?php
class oss {
    public static $oss = NULL;
    protected static $bucket = "captainamerica";
    
    /**
     * 初始化
     *
     * @return ALIOSS
     */
    public function __construct() {
        if (self::$oss === NULL) {
            self::$oss = new ALIOSS();
        }
        return self::$oss;
    }
    
    /**
     * 获取bucket
     *
     * @return array();
     */
    public function get_bucket() {
        return $this->format(self::$oss->list_bucket());
    }
    
    /**
     * 创建bucket
     *
     * @param string $bucket
     *            bucket名称
     * @return array();
     */
    public function create_bucket($bucket) {
        return $this->format(self::$oss->create_bucket($bucket));
    }
    
    /**
     * 删除bucket
     *
     * @param string $bucket            
     * @return array();
     */
    public function delete_bucket($bucket) {
        return $this->format(self::$oss->delete_bucket($bucket));
    }
    
    /**
     * 设置bucket acl
     *
     * @param string $bucket            
     * @return array();
     */
    public function set_bucket_acl($bucket = "") {
        $bucket = $bucket ? $bucket : self::$bucket;
        $acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ_WRITE;
        return $this->format(self::$oss->set_bucket_acl($bucket, $acl));
    }
    
    /**
     * 获取bucket ACL
     */
    public function get_bucket_acl($bucket = "") {
        $bucket = $bucket ? $bucket : self::$bucket;
        $options = array (
            ALIOSS::OSS_CONTENT_TYPE => 'text/xml' 
        );
        return $this->format(self::$oss->get_bucket_acl($bucket, $options));
    }
    
    /**
     * 获取对象/文件列表
     *
     * @param string $bucket
     *            bucket名称 默认为 self::$bucket;
     * @return array();
     */
    public function list_object($bucket = "") {
        $bucket = $bucket ? trim($bucket) : self::$bucket;
        $options = array (
            'delimiter' => '', 'prefix' => '', 'max-keys' => 100 
        );
        $response = self::$oss->list_object($bucket, $options);
        return $this->format($response);
    }
    
    /**
     * 创建目录 可创建多级目录 /abc/abcd/abcef
     *
     * @param string $dir
     *            目录名称
     * @param string $bucket            
     * @return array();
     */
    public function create_directory($dir, $bucket = "") {
        $bucket = $bucket ? $bucket : self::$bucket;
        return $this->format(self::$oss->create_object_dir($bucket, $dir));
    }
    
    /**
     * 上传文件，通过内容
     *
     * @param string $file
     *            文件名，可按目录上传
     * @param string $bucket
     *            空间名
     * @return array();
     */
    public function upload_by_content($file, $bucket = "") {
        $bucket = $bucket ? trim($bucket) : self::$bucket;
        
        $content = 'uploadfile';
        $upload_file_options = array (
            
            'content' => $content, 
            'length' => strlen($content), 
            ALIOSS::OSS_HEADERS => array (
                'Expires' => '2012-10-01 08:00:00' 
            ) 
        );
        return $this->format(self::$oss->upload_file_by_content($bucket, $file, $upload_file_options));
    }
    
    /**
     * 通过路径上传文件
     *
     * @param string $filename
     *            上传文件名
     * @param string $filepath
     *            目录名+文件名
     * @param string $bucket
     *            空间名
     * @return array();
     */
    public function upload_by_file($filename, $filepath, $bucket = "") {
        $bucket = $bucket ? trim($bucket) : self::$bucket;
        return $this->format(self::$oss->upload_file_by_file($bucket, $filename, $filepath));
    }
    
    /**
     * 复制文件/OBJECT
     *
     * @param string $fromfile
     *            源文件名
     * @param string $tofile
     *            目标文件名
     * @param string $frombucket
     *            原bucket
     * @param string $tobucket
     *            目标bucket
     * @return array();
     */
    public function copy_object($fromfile, $tofile = "", $frombucket = "", $tobucket = "") {
        $from_bucket = $frombucket ? $frombucket : self::$bucket;
        $to_bucket = $tobucket ? $tobucket : self::$bucket;
        $to_object = $tofile ? $tofile : $fromfile . "_copy." . $this->getfix($fromfile);
        return $this->format(self::$oss->copy_object($from_bucket, $fromfile, $to_bucket, $to_object));
    }
    
    /**
     * 获取单个object / 文件 mete值
     *
     * @param string $filename
     *            文件/object
     * @param string $bucket            
     * @return array();
     */
    public function get_object_meta($filename, $bucket = "") {
        $bucket = $bucket ? trim($bucket) : self::$bucket;
        
        return $this->format(self::$oss->get_object_meta($bucket, $filename));
    }
    
    /**
     * 删除单个 object / 文件
     *
     * @param string $filename
     *            文件/object
     * @param string $bucket            
     * @return array();
     */
    public function delete_object($filename, $bucket = "") {
        $bucket = $bucket ? trim($bucket) : self::$bucket;
        return $this->format(self::$oss->delete_object($bucket, $filename));
    }
    
    /**
     * 按目录删除文件/对象
     *
     * @param array $objects
     *            对象/文件目录 必须为array
     * @param string $bucket            
     * @return array();
     */
    public function delete_objects($objects, $bucket = "") {
        if (!is_array($objects))
            return array ();
        $bucket = $bucket ? trim($bucket) : self::$bucket;
        $options = array (
            'quiet' => false 
        );
        
        return $this->format(self::$oss->delete_objects($bucket, $objects, $options));
    }
    
    /**
     * 获取单个文件/object信息
     *
     * @param string $filename            
     * @param string $bucket            
     * @return Ambigous <array();, array>
     */
    public function get_object($filename, $bucket = "") {
        if (!$filename)
            return array ();
        $bucket = $bucket ? trim($bucket) : self::$bucket;
        
        return $this->format(self::$oss->get_object($bucket, $filename));
    }
    
    /**
     * 检测文件/object 是否存在
     *
     * @param string $filename            
     * @param string $bucket            
     * @return array();
     */
    public function is_object_exist($filename, $bucket = "") {
        if (!$filename)
            return array ();
        $bucket = $bucket ? trim($bucket) : self::$bucket;
        
        return $this->format(self::$oss->is_object_exist($bucket, $filename));
    }
    
    /**
     * 通过http multipart 上传文件
     * 
     * @param string $filename
     *            上传文件名
     * @param string $filepath
     *            文件路径
     * @param string $bucket            
     * @return array();
     */
    public function upload_by_multi_part($filename, $filepath, $bucket = "") {
        if (!$filename || !$filepath)
            return array ();
        $bucket = $bucket ? trim($bucket) : self::$bucket;
        
        $options = array (
            ALIOSS::OSS_FILE_UPLOAD => $filepath, 'partSize' => 5242880 
        );
        return $this->format(self::$oss->create_mpu_object($bucket, $filename, $options));
    }
    
    /**
     * 通过http multipart 上传整个文件夹
     * 
     * @param string $dir
     *            文件夹
     * @param string $bucket            
     * @return array();
     */
    public function upload_by_dir($dir, $bucket = "") {
        if (!$dir)
            return array ();
        $bucket = $bucket ? trim($bucket) : self::$bucket;
        return self::$oss->create_mtu_object_by_dir($bucket, $dir, false);
    }
    
    /**
     * 通过multi-part上传整个目录(新版)
     * 
     * @param string $dir
     *            目录
     * @param unknown $path
     *            目标目录
     * @param string $bucket            
     * @return string
     */
    public function batch_upload_file($dir, $path, $bucket = "") {
        if (!$dir || !$path)
            return array ();
        $bucket = $bucket ? trim($bucket) : self::$bucket;
        $options = array (
            
        'bucket' => $bucket, 'object' => $path, 'directory' => $dir 
        );
        return self::$oss->batch_upload_file($options);
    }
    
    /**
     * 生成签名url,主要用户私有权限下的访问控制
     * 
     * @param string $filename
     *            文件对象/OBJECT
     * @param string $bucket            
     * @return multitype: array
     */
    public function get_sign_url($filename, $bucket = "") {
        if (!$filename)
            return array ();
        $bucket = $bucket ? trim($bucket) : self::$bucket;
        $timeout = 3600;
        
        return (array) self::$oss->get_sign_url($bucket, $filename, $timeout);
    }
    
    /**
     * 格式化输出返回值
     *
     * @param object $response            
     * @return array();
     */
    private function format($response) {
        $response = (array) $response;
        if ($response["body"]) {
            $xmlc = xml_parser_create();
            if (xml_parse($xmlc, $response["body"], true)) {
                $xml = simplexml_load_string($response["body"]);
                $xml = (array) $xml;
                $return['body'] = $xml;
            }
        }
        return $response;
    }
    
    /**
     * 获取文件后缀
     *
     * @param string $filename            
     * @return string
     */
    private function getfix($filename) {
        return end(explode('.', $filename));
    }
}
