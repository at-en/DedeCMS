<?
require_once(dirname(__FILE__)."/config.php");
if(empty($job)) $job = "";

if($cfg_mb_upload=='��'){
	 $dsql->Close();
	 ShowMsg("ϵͳ��������Ա�ϴ���ͼƬ����!","-1");
	 exit();
}
//����û��ļ����·���Ƿ�Ϸ�
$activepath = str_replace("\\","/",$activepath);
$activepath = str_replace("..","",$activepath);
$activepath = ereg_replace("^/{1,}","/",$activepath);
$rootdir = $cfg_user_dir."/".$cfg_ml->M_ID;
if(strlen($activepath) < strlen($rootdir)){
	$activepath = $rootdir;
}

if($job=="newdir")
{
	$dirname = trim(ereg_replace("[\s\.\*\%\\/\?><\|\":]{1,}","",$dirname));
	if($dirname==""){
		ShowMsg("Ŀ¼���Ƿ���","-1");
		exit();
	}
	MkdirAll($cfg_basedir.$activepath."/".$dirname,777);
	CloseFtp();
	ShowMsg("�ɹ�����һ��Ŀ¼��","select_soft.php?f=$f&activepath=".urlencode($activepath."/".$dirname));
	exit();
}

if($job=="upload")
{
	CheckUserSpace($cfg_ml->M_ID);
	if(empty($uploadfile)) $uploadfile = "";
	if(!is_uploaded_file($uploadfile)){
		 ShowMsg("��û��ѡ���ϴ����ļ�!","-1");
	   exit();
	}
	if(eregi("^text",trim($uploadfile_type))){
		ShowMsg("�������ı����͸���!","-1");
		exit();
	}
	if($uploadfile_size > $cfg_mb_upload_size*1024){
	   @unlink(is_uploaded_file($uploadfile));
		 ShowMsg("���ϴ����ļ�������{$cfg_mb_upload_size}K���������ϴ���","-1");
		 exit();
	}
	if(!CheckAddonType($uploadfile_name)){
		ShowMsg("�����ϴ����ļ����ͱ���ֹ��ϵͳֻ�����ϴ�<br>".$cfg_mb_mediatype." ���͸�����","-1");
		exit();
	}
	$nowtme = mytime();
	
	//����֧���ļ�����
	$y = substr(strftime("%Y",$nowtme),2,2);
	$filename = $cfg_ml->M_ID."_".$y.strftime("%m%d%H%M%S",$nowtme);
	$fs = explode(".",$uploadfile_name);
	$filename = $filename.".".$fs[count($fs)-1];
	if(eregi("php|asp|pl|shtml|jsp|cgi",$fs[count($fs)-1])){
		exit();
	}

  $fullfilename = $cfg_basedir.$activepath."/".$filename;
  if(file_exists($fullfilename)){
  	ShowMsg("��Ŀ¼�Ѿ�����ͬ�����ļ�������ģ�","-1");
		exit();
  }
  @move_uploaded_file($uploadfile,$fullfilename);
	if($uploadfile_type == 'application/x-shockwave-flash') $mediatype=2;
	else if(eregi('audio|media|video',$uploadfile_type)) $mediatype=3;
	else $mediatype=4;
	$inquery = "
   INSERT INTO #@__uploads(title,url,mediatype,width,height,playtime,filesize,uptime,adminid,memberid) 
   VALUES ('$filename','".$activepath."/".$filename."','$mediatype','0','0','0','{$uploadfile_size}','{$nowtme}','0','".$cfg_ml->M_ID."');
  ";
  $dsql = new DedeSql(false);
  $dsql->ExecuteNoneQuery($inquery);
  $dsql->Close();
	ShowMsg("�ɹ��ϴ��ļ���","select_soft.php?comeback=".urlencode($filename)."&f=$f&activepath=".urlencode($activepath)."&d=".time());
	exit();
}

?>