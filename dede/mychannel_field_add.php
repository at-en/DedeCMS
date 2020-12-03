<?php 
require_once(dirname(__FILE__)."/config.php");
if(empty($action)) $action = '';
if($action=='save'){
	//ģ����Ϣ
  $dsql = new DedeSql(false);
  $mysql_version = $dsql->GetVersion();
  $mysql_versions = explode(".",trim($mysql_version));
  $mysql_version = $mysql_versions[0].".".$mysql_versions[1];
  $row = $dsql->GetOne("Select fieldset,addtable,issystem From #@__channeltype where ID='$ID'");
  $fieldset = $row['fieldset'];
  require_once(dirname(__FILE__)."/../include/pub_dedetag.php");
  $dtp = new DedeTagParse();
  $dtp->SetNameSpace("field","<",">");
  $dtp->LoadSource($fieldset);
  //���ӵ��ֶ�
  if($row['issystem']==1){
		 $dsql->Close();
		 ShowMsg("�Բ������ڲ鿴����ϵͳģ�ͣ����������ֶΣ�","-1");
		 exit();
	}
	$trueTable = $row['addtable'];
  //������ݿ��Ƿ���ڸ��ӱ������������½�һ��
  if(!$dsql->IsTable($trueTable)){
     $tabsql = "CREATE TABLE IF NOT EXISTS  `$trueTable`(
	           `aid` int(11) NOT NULL default '0',
             `typeid` int(11) NOT NULL default '0',
     ";
	   if($mysql_version < 4.1){
        $tabsql .= "    PRIMARY KEY  (`aid`), KEY `".$trueTable."_index` (`typeid`)\r\n) TYPE=MyISAM; ";
     }else{
        $tabsql .= "    PRIMARY KEY  (`aid`), KEY `".$trueTable."_index` (`typeid`)\r\n) ENGINE=MyISAM DEFAULT CHARSET=".$cfg_db_language."; ";
     }
     $dsql->ExecuteNoneQuery($tabsql);
  }
  //��⸽�ӱ��ﺬ�е��ֶ�
	$fields = Array();
	$rs = mysql_query("show fields from $trueTable",$dsql->linkID);
	while($row = mysql_fetch_array($rs)){
		$fields[$row['Field']] = $row['Type'];
	}
	//�޸��ֶ�������Ϣ
	$dfvalue = $vdefault;
	$isnull = ($isnull==1 ? "true" : "false");
	$mxlen = $maxlength;
	//��ⱻ�޸ĵ��ֶ����ͣ����������ݱ�
	if($dtype=="int"||$dtype=="datetime"){
    	if($dfvalue=="" || ereg("[^0-9-]",$dfvalue)){ $dfvalue = 0; }
    	$tabsql = " `$fieldname` int(11) NOT NULL default '$dfvalue';";
    	$buideType = "int(11)";
  }else if($dtype=="float"){
      if($dfvalue=="" || ereg("[^0-9\.-]",$dfvalue)){ $dfvalue = 0; }
      $tabsql = " `$fieldname` float NOT NULL default '$dfvalue';";
    	$buideType = "float";
  }else if($dtype=="img"||$dtype=="media"||$dtype=="addon"){
    	if($mxlen=="") $mxlen = 200;
    	if($mxlen > 255) $mxlen = 50;
    	$tabsql = " `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue';";
    	$buideType = "varchar($mxlen)";
  }else if($dtype=="multitext"||$dtype=="htmltext"){
    	$tabsql = " `$fieldname` mediumtext;";
    	$buideType = "mediumtext";
  }else if($dtype=="textdata"){
    	$tabsql = " `$fieldname` varchar(100) NOT NULL default '';";
    	$buideType = "varchar(100)";
  }else{
    	if($mxlen=="") $mxlen = 50;
    	if($mxlen > 255) $mxlen = 250;
    	$tabsql = " `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue';";
    	$buideType = "varchar($mxlen)";
  }
  //�����������ͣ����滻Ϊ������
  if(is_array($dtp->CTags)){
	  $ok = false;
	  foreach($dtp->CTags as $tagid=>$ctag){
		  if($ctag->GetName()==$fieldname){
        $dfvalue = $ctag->GetAtt('default');
        $isnull = $ctag->GetAtt('isnull');
        $dtype = $ctag->GetAtt('type');
        $mxlen = $ctag->GetAtt('maxlength');
        if(isset($fields[$fieldname]) && $fields[$fieldname]!=$buideType){
     	    $tabsql = "ALTER TABLE `$trueTable` CHANGE `$fieldname` ".$tabsql;
        }else if(!isset($fields[$fieldname])){
     	    $tabsql = "ALTER TABLE `$trueTable` ADD ".$tabsql;
        }else{
     	    $tabsql = "";
        }
        if($tabsql!=""){
     	    $dsql->ExecuteNoneQuery($tabsql);
        }
        $ok = true;
        $dtp->Assign($tagid,stripslashes($fieldstring));
		  }
	  }
	  if(!$ok){
	  	$dsql->ExecuteNoneQuery(" ALTER TABLE `$trueTable` ADD  $tabsql ");
	  	$oksetting = addslashes($fieldset)."\r\n".$fieldstring;
	  }else{
	  	$oksetting = addslashes($dtp->GetResultNP());
	  }
	}else{
		$dsql->ExecuteNoneQuery(" ALTER TABLE `$trueTable` ADD  $tabsql ");
		$oksetting = addslashes($fieldset)."\r\n".$fieldstring;
	}
	$dsql->ExecuteNoneQuery("Update #@__channeltype set fieldset='$oksetting' where ID='$ID' ");
	$dsql->Close();
	ShowMsg("�ɹ�����һ���ֶΣ�","mychannel_field.php?ID={$ID}&dopost=edit");
	exit();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>�����ֶ�</title>
<style type="text/css">
<!--
body {
	background-image: url(img/allbg.gif);
}
-->
</style>
<link href="base.css" rel="stylesheet" type="text/css">
<script language="javascript">
var notAllow = " aid ID typeid typeid2 sortrank iscommend ismake channel arcrank click money title shorttitle color writer source litpic pubdate senddate arcatt adminID memberID description keywords templet lastpost postnum redirecturl mtype userip ";
function GetFields()
{
	var fieldname = document.form1.fieldname.value;
	var itemname = document.form1.itemname.value;
	var dtype = document.form1.dtype.value;
	var isnull = document.form1.isnull.value;
	var vdefault = document.form1.vdefault.value;
	var maxlength = document.form1.maxlength.value;
	var vfunction = document.form1.vfunction.value;
	var vinnertext = document.form1.vinnertext.value;
	if(document.form1.spage[0].checked) var spage = document.form1.spage[0].value;
	else var spage = document.form1.spage[1].value;
	if(isnull==0) var sisnull="false";
	else var sisnull="true";
	if(notAllow.indexOf(" "+fieldname+" ") >-1 ) 
	{
		alert("�ֶ����Ʋ��Ϸ������������ǲ������ģ�\n"+notAllow);
		return false;
	}
	if((dtype=="text"||dtype=="radio"||dtype=="select") && maxlength=="")
	{
		alert("��ѡ������ı���select��radio���ͣ�������������ַ����ȣ�");
		return false;
	}
	if((dtype=="radio"||dtype=="select") && vinnertext=="")
	{
		alert("��ѡ���select��radio���ͣ������ڱ���HTML������ѡ�����Ŀ���ö���[,]�ֿ�����");
		return false;
	}
	if(itemname=="")
	{
		alert("������ʾ���Ʋ���Ϊ�գ�");
		return false;
	}
	if(spage=="no") spage = "";
	revalue =  "<field:"+fieldname+" itemname=\""+itemname+"\" type=\""+dtype+"\"";
	revalue += " isnull=\""+sisnull+"\" default=\""+vdefault+"\" function=\""+vfunction+"\"";
	revalue += " maxlength=\""+maxlength+"\" page=\""+spage+"\">\r\n"+vinnertext+"</field:"+fieldname+">\r\n";
	document.form1.fieldstring.value = revalue;
	document.form1.submit();
}
</script>
</head>
<body topmargin="8">
<table width="98%"  border="0" align="center" cellpadding="2" cellspacing="1" bgcolor="#98CAEF">
  <form name="form1" action="mychannel_field_add.php" method="post">
  <input type='hidden' name='action' value='save'>
  <input type='hidden' name='ID' value='<?php echo $ID?>'>
	<input type='hidden' name='fieldstring' value=''>
    <tr> 
      <td height="20" colspan="2" background="img/tbg.gif"> 
        <table width="98%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="30%"><b>&nbsp;<a href="mychannel_main.php"></a>�������ֶΣ�</b> 
            </td>
            <td align="right">
			<input type="button" name="ss1" value="��ǰģ����Ϣ" style="width:90px;margin-right:6px" onClick="location='mychannel_edit.php?ID=<?php echo $ID?>&dopost=edit';" class='nbt'>
              <input type="button" name="ss12" value="Ƶ��ģ�͹���" style="width:90px;margin-right:6px" onClick="location='mychannel_main.php';" class='nbt'> 
            </td>
          </tr>
        </table> </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">������ʾ���֣�</td>
      <td bgcolor="#FFFFFF">
      	<input name="itemname" type="text" id="itemname" style="width:35%">
        *����������ʱ��ʾ�������֣�
        </td>
    </tr>
    <tr> 
      <td width="28%" align="center" bgcolor="#FFFFFF">�ֶ����ƣ�</td>
      <td width="72%" bgcolor="#FFFFFF" style="table-layout:fixed;word-break:break-all"> 
        <input name="fieldname" type="text" id="fieldname" style="width:35%"> 
      </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">�������ͣ�</td>
      <td bgcolor="#FFFFFF">
      	<select name="dtype" id="type" style="width:250">
          <option value="text">�����ı�</option>
          <option value="multitext">�����ı�</option>
          <option value="htmltext">HTML�ı�</option>
          <option value="int">��������</option>
          <option value="float">С������</option>
          <option value="datetime">ʱ������</option>
          <option value="img">ͼƬ</option>
          <option value="media">��ý���ļ�</option>
          <option value="addon">��������</option>
          <option value="textdata">�ı��������</option>
          <option value="select">ʹ��option������</option>
          <option value="radio">ʹ��radioѡ�</option>
        </select> </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">�Ƿ��ҳ��</td>
      <td bgcolor="#FFFFFF">
      	<input name="isnull" type="hidden" value="1">
        <input name="spage" type="radio" class="np" value="split">
         ��
         &nbsp; 
        <input name="spage" type="radio" class="np" value="no" checked>
         ��
        </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">Ĭ��ֵ��</td>
      <td bgcolor="#FFFFFF"> 
      	<input name="vdefault" type="text" id="vdefault"> 
      </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">��󳤶ȣ�</td>
      <td bgcolor="#FFFFFF">
      	<input name="maxlength" type="text" id="maxlength">
        (�ı����ݱ�����д������255Ϊtext����)
        </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF">����������</td>
      <td bgcolor="#FFFFFF">
      	<input name="vfunction" type="text" id="vfunction" style="width:35%">
        (��ѡ����'@me'��ʾ��ǰ��Ŀֵ����)
       </td>
    </tr>
    <tr>
      <td align="center" bgcolor="#FFFFFF">�Զ������HTML��</td>
      <td bgcolor="#FFFFFF">
      	���Զ������HTMLʱ������������Ϊ���ֶ����ơ���value='@value'��<br>
      	�����������������Ϊselect��radioʱ���˴���д��ѡ�����Ŀ���á�,���ֿ����硰��,Ů,��������
      </td>
    </tr>
    <tr> 
      <td align="center" bgcolor="#FFFFFF"><br>
      </td>
      <td bgcolor="#FFFFFF">
      	<textarea name="vinnertext" cols="45" rows="5" id="vinnertext"></textarea> 
      </td>
    </tr>
    <tr> 
      <td height="28" colspan="2" bgcolor="#E8F8FF">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td width="26%" height="45">&nbsp;</td>
            <td width="20%"><img src="img/button_ok.gif" width="60" height="22" border="0" style="cursor:hand" onClick="GetFields()"></td>
            <td width="54%"><img src="img/button_reset.gif" width="60" height="22" border="0" style="cursor:hand" onClick="form1.reset()"></td>
          </tr>
        </table></td>
    </tr>
  </form>
</table>
</body>
</html>