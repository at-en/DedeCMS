<?php 
require_once(dirname(__FILE__)."/config.php");
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
$dsql = new DedeSql(false);

if(empty($pagesize)) $pagesize = 18;
if(empty($pageno)) $pageno = 1;
if(empty($dopost)) $dopost = '';
if(empty($orderby)) $orderby = 'tid';
if(empty($keyword)){
	$keyword = '';
	$addget = '';
	$addsql = '';
}else{
	$addget = '&keyword='.urlencode($keyword);
	$addsql = " where tagname like '%$keyword%' ";
}

//�����б�
if($dopost=='getlist'){
	PrintAjaxHead();
	GetTagList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}
//�����ֶ�
else if($dopost=='update')
{
	$tid = ereg_replace("[^0-9]","",$tid);
	$tagcc = ereg_replace("[^0-9]","",$tagcc);
	$cc = ereg_replace("[^0-9]","",$cc);
	$tagname = trim($tagname);
	$dsql->ExecuteNoneQuery("Update #@__tags set tagname='$tagname',tagcc='$tagcc',cc='$cc' where tid='$tid';");
	PrintAjaxHead();
	GetTagList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}
//ɾ���ֶ�
else if($dopost=='del')
{
	$tid = ereg_replace("[^0-9]","",$tid);
	$dsql->ExecuteNoneQuery("Delete From #@__tags_archives where tid='$tid'; ");
	$dsql->ExecuteNoneQuery("Delete From #@__tags_user where tid='$tid'; ");
	$dsql->ExecuteNoneQuery("Delete From #@__tags where tid='$tid'; ");
	PrintAjaxHead();
	GetTagList($dsql,$pageno,$pagesize,$orderby);
	$dsql->Close();
	exit();
}

//��һ�ν������ҳ��
if($dopost==''){
	$row = $dsql->GetOne("Select count(*) as dd From #@__tags $addsql ");
	$totalRow = $row['dd'];
	include(dirname(__FILE__)."/templets/tag_main.htm");
  $dsql->Close();
}

//����ض���Tag�б�
//---------------------------------
function GetTagList($dsql,$pageno,$pagesize,$orderby='aid'){
	global $cfg_phpurl,$addsql;
	$start = ($pageno-1) * $pagesize;
	$printhead ="<table width='99%' border='0' cellpadding='1' cellspacing='1' bgcolor='#333333' style='margin-bottom:3px'>
    <tr align='center' bgcolor='#E5F9FF' height='24'> 
      <td width='8%'><a href='#' onclick=\"ReloadPage('tid')\"><u>ID</u></a></td>
      <td width='32%'>TAG����</td>
      <td width='10%'><a href='#' onclick=\"ReloadPage('tagcc')\"><u>ʹ����</u></a></td>
      <td width='10%'><a href='#' onclick=\"ReloadPage('cc')\"><u>�����</u></a></td>
      <td width='10%'><a href='#' onclick=\"ReloadPage('arcnum')\"><u>�ĵ���</u></a></td>
      <td width='10%'>����ʱ��</td>
      <td>����</td>
    </tr>\r\n";
    echo $printhead;
    $dsql->SetQuery("Select * From #@__tags $addsql order by $orderby desc limit $start,$pagesize ");
	  $dsql->Execute();
    while($row = $dsql->GetArray()){
    $line = "
      <tr align='center' bgcolor='#FFFFFF' onMouseMove=\"javascript:this.bgColor='#FCFEDA';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\"> 
      <td height='24'>{$row['aid']}</td>
      <td><input name='tagname' type='text' id='tagname{$row['tid']}' value='{$row['tagname']}' class='ininput'></td>
      <td><input name='tagcc' type='text' id='tagcc{$row['tagcc']}' value='{$row['tagcc']}' class='ininput'></td>
      <td><input name='cc' type='text' id='cc{$row['cc']}' value='{$row['cc']}' class='ininput'></td>
      <td> {$row['arcnum']} </td>
      <td>".strftime("%y-%m-%d",$row['stime'])."</td>
      <td>
      <a href='#' onclick='UpdateNote({$row['tid']})'>����</a> | 
      <a href='#' onclick='DelNote({$row['tid']})'>ɾ��</a>
      </td>
    </tr>";
    echo $line;
   }
	 echo "</table>\r\n";
}

function PrintAjaxHead(){
	header("Pragma:no-cache\r\n");
  header("Cache-Control:no-cache\r\n");
  header("Expires:0\r\n");
	header("Content-Type: text/html; charset=gb2312");
}
?>
