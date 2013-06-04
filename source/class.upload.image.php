<?php
include_once("function_class_image.php");

class upload_image extends imageInit {
    var $upload_file_name;//上传文件的名字
    var $upload_file_size;//上传文件的大小
    var $upload_max_size = 50000;//上传文件限制的大小
    var $upload__file_dir = "uploadfile/";//图片存放路径
    var $upload_tamp_dir;//上传文件的临时路径
    var $upload_file_type;//上传文件的类别
    var $upload_allow_type = array("image/jpeg","image/jpg","image/png","image/gif");//允许上传文件的类别
    var $errorstr;//错误提示
    //获取上传文件的名字
    function get_upload_name($upload_file_name){
         $type = end(explode('.',$upload_file_name));//获取图片的类型，然后重命名
         return $this->upload_file_name=$this->upload__file_dir.strtoupper(md5(date("ymdhis"))).'.'.$type;
    }
    //获取上传文件的类型
    function get_upload_type($upload_file_type){
        return $this->upload_file_type = $upload_file_type;
    }
    //获取文件的大小
    function get_upload_size($upload_file_size){
        return $this ->upload_file_size = $upload_file_size;
    }
    //获取文件的临时目录
    function get_upload_tamp($upload_tamp_dir){
        return $this->upload_tamp_dir = $upload_tamp_dir;
    }
    //检查文件存放目录
    function check_upload_dir(){
        if(!file_exists($this ->upload__file_dir)){
            @mkdir($this->upload__file_dir);
            @chmod($this->upload__file_dir, 0777);
			@fclose(@fopen($this->upload__file_dir . '/index.html', 'w'));
			@chmod($this->upload__file_dir . '/index.html', 0777);
        }
    }
    //检查上传文件的大小
    function check_upload_size(){
        if($this ->upload_file_size > $this->upload_max_size){
           $this->halt(array("status"=>0)); //big file
           exit;
         }
    }
    //检查上传文件的类型
    function check_upload_type(){
        if(!in_array($this->upload_file_type,$this->upload_allow_type)){
            $this->halt(array("status"=>-1)); //"file type error"
            exit;
        }
    }
    //移动图片
    function uplod_image() {
        if(!move_uploaded_file($this->upload_tamp_dir,$this->upload_file_name)){
            $this->halt(array("status"=>1)); //"error"
            exit;
        }else{
            $this->halt(array("status"=>2,"path"=>$this->upload_file_name)); //"succeed"
            exit();
        }

    }
    //错误提醒
    function halt($errorstr = array()){
        echo  json_encode(array("mssage"=>$errorstr));
    }
    //上传操作
    function upload(){
        $this->check_upload_type();
        $this->check_upload_size();
        $this->check_upload_dir();
        $this->uplod_image();
    }
}
