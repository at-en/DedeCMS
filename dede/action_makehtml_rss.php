<?
require_once(dirname(__FILE__)."/config.php");
require_once(dirname(__FILE__)."/../include/inc_rss_view.php");
if(empty($tid)) $tid = 0;
if(empty($maxrecord)) $maxrecord = 50;
$dsql = new DedeSql(false);
$row = $dsql->GetOne("Select ID From #@__arctype where ID>'$tid' And ispart<>2 order by ID asc limit 0,1;");
$dsql->Close();
if(!is_array($row)){
	echo "完成所有文件更新！";
	exit();
}
else{
	$rv = new RssView($row['ID'],$maxrecord);
	$rssurl = $rv->MakeRss();
	$rv->Close();
	$tid = $row['ID'];
	ShowMsg("成功更新".$rssurl."，继续进行操作！","action_makehtml_rss.php?tid=$tid&maxrecord=$maxrecord",0,100);
  exit();
}
?>