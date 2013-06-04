<?php
include_once("function_class_image.php");

class upload_image extends imageInit {
    var $upload_file_name;//�ϴ��ļ�������
    var $upload_file_size;//�ϴ��ļ��Ĵ�С
    var $upload_max_size = 50000;//�ϴ��ļ����ƵĴ�С
    var $upload__file_dir = "uploadfile/";//ͼƬ���·��
    var $upload_tamp_dir;//�ϴ��ļ�����ʱ·��
    var $upload_file_type;//�ϴ��ļ������
    var $upload_allow_type = array("image/jpeg","image/jpg","image/png","image/gif");//�����ϴ��ļ������
    var $errorstr;//������ʾ
    //��ȡ�ϴ��ļ�������
    function get_upload_name($upload_file_name){
         $type = end(explode('.',$upload_file_name));//��ȡͼƬ�����ͣ�Ȼ��������
         return $this->upload_file_name=$this->upload__file_dir.strtoupper(md5(date("ymdhis"))).'.'.$type;
    }
    //��ȡ�ϴ��ļ�������
    function get_upload_type($upload_file_type){
        return $this->upload_file_type = $upload_file_type;
    }
    //��ȡ�ļ��Ĵ�С
    function get_upload_size($upload_file_size){
        return $this ->upload_file_size = $upload_file_size;
    }
    //��ȡ�ļ�����ʱĿ¼
    function get_upload_tamp($upload_tamp_dir){
        return $this->upload_tamp_dir = $upload_tamp_dir;
    }
    //����ļ����Ŀ¼
    function check_upload_dir(){
        if(!file_exists($this ->upload__file_dir)){
            @mkdir($this->upload__file_dir);
            @chmod($this->upload__file_dir, 0777);
			@fclose(@fopen($this->upload__file_dir . '/index.html', 'w'));
			@chmod($this->upload__file_dir . '/index.html', 0777);
        }
    }
    //����ϴ��ļ��Ĵ�С
    function check_upload_size(){
        if($this ->upload_file_size > $this->upload_max_size){
           $this->halt(array("status"=>0)); //big file
           exit;
         }
    }
    //����ϴ��ļ�������
    function check_upload_type(){
        if(!in_array($this->upload_file_type,$this->upload_allow_type)){
            $this->halt(array("status"=>-1)); //"file type error"
            exit;
        }
    }
    //�ƶ�ͼƬ
    function uplod_image() {
        if(!move_uploaded_file($this->upload_tamp_dir,$this->upload_file_name)){
            $this->halt(array("status"=>1)); //"error"
            exit;
        }else{
            $this->halt(array("status"=>2,"path"=>$this->upload_file_name)); //"succeed"
            exit();
        }

    }
    //��������
    function halt($errorstr = array()){
        echo  json_encode(array("mssage"=>$errorstr));
    }
    //�ϴ�����
    function upload(){
        $this->check_upload_type();
        $this->check_upload_size();
        $this->check_upload_dir();
        $this->uplod_image();
    }
}
