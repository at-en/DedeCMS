<?
require_once(dirname(__FILE__)."/config.php");
if(!isset($nid)) $nid=0;
if(empty($_COOKIE["ENV_GOBACK_URL"])) $ENV_GOBACK_URL = "co_url.php";
else $ENV_GOBACK_URL = $_COOKIE["ENV_GOBACK_URL"];
//删除节点
/*
function co_delete()
*/
if($dopost=="delete")
{
   $dsql = new DedeSql(false);
   $inQuery = "Delete From #@__courl where nid='$nid'";
   $dsql->SetSql($inQuery);
   $dsql->ExecuteNoneQuery();
   $inQuery = "Delete From #@__conote where nid='$nid'";
   $dsql->SetSql($inQuery);
   $dsql->ExecuteNoneQuery();
   $dsql->Close();
   ShowMsg("成功删除一个节点!","co_main.php");
   exit();
}
//清空采集内容
/*
function url_clear()
*/
else if($dopost=="clear")
{
	if(!isset($ids)) $ids="";
  if(empty($ids))
  {
	  $dsql = new DedeSql(false);
	  $inQuery = "Delete From #@__courl where nid='$nid'";
	  $dsql->SetSql($inQuery);
	  $dsql->ExecuteNoneQuery();
	  $dsql->Close();
	  ShowMsg("成功清空一个节点采集的内容!","co_main.php");
	  exit();
  }
  else
  {
	  $dsql = new DedeSql(false);
	  $inQuery = "Delete From #@__courl where ";
	  $idsSql = "";
	  $ids = explode("`",$ids);
	  foreach($ids as $id) $idsSql .= "or aid='$id' ";
	  $idsSql = ereg_replace("^or ","",$idsSql);
	  $dsql->SetSql($inQuery.$idsSql);
	  $dsql->ExecuteNoneQuery();
	  $dsql->Close();
	  ShowMsg("成功删除指定的网址内容!",$ENV_GOBACK_URL);
	  exit();
  }
}
//复制节点
/*
function co_copy()
*/
else if($dopost=="copy")
{
	if(empty($notename))
	{
		require_once(dirname(__FILE__)."/../include/pub_oxwindow.php");
  	$wintitle = "采集管理-复制节点";
	  $wecome_info = "<a href='co_main.php'>采集管理</a>::复制节点";
	  $win = new OxWindow();
	  $win->Init("action_co_do.php","js/blank.js","POST");
	  $win->AddHidden("dopost",$dopost);
	  $win->AddHidden("nid",$nid);
	  $win->AddTitle("请输入新节点名称：");
	  $win->AddItem("新节点名称：","<input type='text' name='notename' value='' size='30'>");
	  $winform = $win->GetWindow("ok");
	  $win->Display();
		exit();
	}
	$dsql = new DedeSql(false);
	$row = $dsql->GetOne("Select * From #@__conote where nid='$nid'");
	$inQuery = "
   INSERT INTO #@__conote(typeid,gathername,language,lasttime,savetime,noteinfo) 
   VALUES('".$row['typeid']."', '$notename', '".addslashes($row['language'])."', '0','".time()."', '".addslashes($row['noteinfo'])."');
  ";
  $dsql->SetQuery($inQuery);
  $dsql->ExecuteNoneQuery();
  $dsql->Close();
  ShowMsg("成功复制一个节点!",$ENV_GOBACK_URL);
	exit();
}
?>