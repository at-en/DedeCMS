<?
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
if(empty($dopost)) $dopost = "";
////////////////////////////////////////
if($dopost=="ok")
{
  require_once(dirname(__FILE__)."/../include/inc_arcspec_view.php");
  $sp = new SpecView();
  $sp->MakeHtml();
  $sp->Close();
  exit();
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312">
<title>����ר���б�HTML</title>
<link href="base.css" rel="stylesheet" type="text/css">
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" cellpadding="3" cellspacing="1" bgcolor="#666666" align="center">
  <form name="form1" action="makehtml_spec.php" method="get" target='stafrm'>
  <input type="hidden" name="dopost" value="ok">
    <tr> 
      <td height="20" background='img/tbg.gif'> <table width="98%" border="0" cellpadding="0" cellspacing="0">
          <tr> 
            <td width="30%" height="18"><strong>����ר���б�HTML��</strong></td>
            <td width="70%" align="right">&nbsp; </td>
          </tr>
        </table></td>
    </tr>
    <tr> 
      <td height="20" valign="top" bgcolor="#FFFFFF">˵����Ĭ�ϵ�����£�ϵͳ�ö�̬���� 
        <?=$cfg_cmspath.$cfg_special."/index.php"?>
        ����ȡר���б�������HTML��ϵͳ���Զ�ʶ��<strong>̬</strong>�ļ���</td>
    </tr>
    <tr> 
      <td height="20" bgcolor="#FAFAF1" align="center"> <input name="b112" type="button" class="np2" value="��ʼ����HTML" onClick="document.form1.submit();" style="width:100"> 
      </td>
    </tr>
  </form>
  <tr bgcolor="#E6F3CD"> 
    <td height="20"> <table width="100%">
        <tr> 
          <td width="74%">����״̬�� </td>
          <td width="26%" align="right"> <script language='javascript'>
            	function ResizeDiv(obj,ty)
            	{
            		if(ty=="+") document.all[obj].style.pixelHeight += 50;
            		else if(document.all[obj].style.pixelHeight>80) document.all[obj].style.pixelHeight = document.all[obj].style.pixelHeight - 50;
            	}
            	</script>
            [<a href='#' onClick="ResizeDiv('mdv','+');">����</a>] [<a href='#' onClick="ResizeDiv('mdv','-');">��С</a>] 
          </td>
        </tr>
      </table></td>
  </tr>
  <tr bgcolor="#FFFFFF"> 
    <td id="mtd"> <div id='mdv' style='width:100%;height:100;'> 
        <iframe name="stafrm" frameborder="0" id="stafrm" width="100%" height="100%"></iframe>
      </div>
      <script language="JavaScript">
	  document.all.mdv.style.pixelHeight = screen.height - 360;
	  </script> </td>
  </tr>
</table>
</body>
</html>