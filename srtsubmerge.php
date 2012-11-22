<?php
#include '../../user_check_session.php';
include '../../db.php';
include '../../basic_lib.php';

#$uploaddir = '/kunden/97925_12353/srttmp/';
$uploaddir = 'tmp/';

if(isset($_POST['timecorrection'])){
	if(isset($_FILES['tcfileupload']['name'])&&isset($_POST['tcsh'])&&isset($_POST['tcsm'])&&isset($_POST['tcss'])&&isset($_POST['tcsms'])&&isset($_POST['tcch'])&&isset($_POST['tccm'])&&isset($_POST['tccs'])&&isset($_POST['tccms'])){
		$uploadfile = $uploaddir . basename($_FILES['tcfileupload']['name']);		
#$uploadfile = $uploaddir . preg_replace('/[\W\S\D^\.]+/','_',basename($_FILES['tcfileupload']['name']));
		if (move_uploaded_file($_FILES['tcfileupload']['tmp_name'], $uploadfile)) {
			$newfile = timcorrection($uploadfile);
			$newfilename = substr($uploadfile,0,strlen($uploadfile)-4);
			$newfilename .= ".tc.srt";
			if(!$fd = fopen($newfilename, "w")) {
			echo("Error opening file.");
			}
			if(!fwrite($fd, $newfile)) {
			echo("Error writing file.");
			}
			fclose($fd);
			unlink($uploadfile);
			$theFileName = basename($newfilename);
			   header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			   header ("Content-Type: application/octet-stream");
			   header ("Content-Length: " . filesize($newfilename));
			   header ("Content-Disposition: attachment; filename=\"$theFileName\"");
			   readfile($newfilename);
				#echo $newfile;
			$flag = true;
			define("_BBC_PAGE_NAME", 'submerge_tc');
			define("_BBCLONE_DIR", "../../webstat/");
			define("COUNTER", _BBCLONE_DIR."mark_page.php");
			#if (is_readable(COUNTER)) include_once(COUNTER);
		}else {
			echo "<span class=\"onlyred\">fill all fields!</span>";
		}
	} else {
			echo "<span class=\"onlyred\">fill all fields!</span>";
	}
}else if(isset($_POST['split'])){
	if(isset($_FILES['spfileupload']['name'])&&isset($_POST['sph'])&&isset($_POST['spm'])&&isset($_POST['sps'])&&isset($_POST['spms'])){
		#echo "splitting";
		$uploadfile = $uploaddir . basename($_FILES['spfileupload']['name']);
		if (move_uploaded_file($_FILES['spfileupload']['tmp_name'], $uploadfile)) {
			$newfile = splitsrt($uploadfile);
			#echo "this:<pre>$newfile[0]</pre><br>this:<br><pre>$newfile[1]</pre>";
			$newfilename = substr($uploadfile,0,strlen($uploadfile)-4);
			if(!$fd = fopen($newfilename.".sp.cd1.srt", "w")) {
				echo("Error opening file.");
			}
			if(!fwrite($fd, $newfile[0])) {
				echo("Error writing file.");
			}
			fclose($fd);
			if(!$fd = fopen($newfilename.".sp.cd2.srt", "w")) {
				echo("Error opening file.");
			}
			if(!fwrite($fd, $newfile[1])) {
				echo("Error writing file.");
			}
			fclose($fd);
			unlink($uploadfile);
			$base = basename($newfilename);
			echo exec("tar -C $uploaddir -cjf $newfilename.tar.bz2 $base.sp.cd2.srt $base.sp.cd1.srt");
			unlink($newfilename.".sp.cd1.srt");
			unlink($newfilename.".sp.cd2.srt");
			$theFileName = basename($newfilename.".tar.bz2");
			header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header ("Content-Type: application/octet-stream");
			header ("Content-Length: " . filesize($newfilename.".tar.bz2"));
			header ("Content-Disposition: attachment; filename=\"$theFileName\"");
			readfile($newfilename.".tar.bz2");
			#echo $newfile;
			$flag = true;
			define("_BBC_PAGE_NAME", 'submerge_sp');
			define("_BBCLONE_DIR", "../../webstat/");
			define("COUNTER", _BBCLONE_DIR."mark_page.php");
			#if (is_readable(COUNTER)) include_once(COUNTER);
		}else {
			echo "<span class=\"onlyred\">fill all fields!</span>";
		}
	} else {
			echo "<span class=\"onlyred\">fill all fields!</span>";
	}

}else if(isset($_POST['submerge'])){
	
	#$uploaddir = "G:\\HTTP\\delarue\\tmp\\";
	#'
	if(isset($_FILES['fileupload']['name'])&&isset($_FILES['fileupload2']['name'])&&isset($_POST['h'])&&isset($_POST['m'])&&isset($_POST['s'])&&isset($_POST['ms'])){
		$uploadfile = $uploaddir . basename($_FILES['fileupload']['name']);
		$uploadfile2 = $uploaddir . basename($_FILES['fileupload2']['name']);
		
		if (move_uploaded_file($_FILES['fileupload']['tmp_name'], $uploadfile)&&move_uploaded_file($_FILES['fileupload2']['tmp_name'], $uploadfile2)) {
			#echo "File is valid, and was successfully uploaded.\r\n";
			if(isset($_POST['lookup'])){
				if($_POST['lookup']=="on"){
					$matches = array();
					$lines = file($uploadfile);
					foreach ($lines as $read) {
						if(preg_match("/\d+:\d+:\d+,\d+ --> (\d)+:(\d+):(\d+),(\d+)/",$read,$matches)){
							$h = $matches[1];
							$m = $matches[2];
							$s = $matches[3];
							$ms = $matches[4];
						}
					}
				}
			}else{
					$file2 = convertcd2($uploadfile,$uploadfile2);
					$file1 = file_get_contents($uploadfile);
					$newfile = $file1.$file2;
					$newfilename = substr($uploadfile2,0,strlen($uploadfile2)-4);
					$newfilename .= ".sm.srt";
					if(!$fd = fopen($newfilename, "w")) {
					echo("Error opening file.");
					}
					if(!fwrite($fd, $newfile)) {
					echo("Error writing file.");
					}
					fclose($fd);
					unlink($uploadfile);
					unlink($uploadfile2);
					$theFileName = basename($newfilename);
					   header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
					   header ("Content-Type: application/octet-stream");
					   header ("Content-Length: " . filesize($newfilename));
					   header ("Content-Disposition: attachment; filename=\"$theFileName\"");
					   readfile($newfilename);
						#echo $newfile;
					$flag = true;
					define("_BBC_PAGE_NAME", 'submerge_X');
					define("_BBCLONE_DIR", "../../webstat/");
					define("COUNTER", _BBCLONE_DIR."mark_page.php");
					#if (is_readable(COUNTER)) include_once(COUNTER);
				}
		} else {
			echo "<span class=\"onlyred\">fill all fields!</span>";
		   #echo "Possible file upload attack!\r\n";
			#echo 'Here is some more debugging info:';
			#print_r($_FILES);
		}
	} else {
			echo "<span class=\"onlyred\">fill all fields!</span>";
	}

}else{
	define("_BBC_PAGE_NAME", 'submerge');
	define("_BBCLONE_DIR", "../../webstat/");
	define("COUNTER", _BBCLONE_DIR."mark_page.php");
	#if (is_readable(COUNTER)) include_once(COUNTER);
}
#------------------------------------------------------------------------------------
if(isset($_POST['mentry'])){
	$mentry = addslashes($_POST['mentry']);
	$username = addslashes($_POST['username']);
	$mentry = strip_tags($mentry);
	$username = strip_tags($username);
 	$mentry = nl2br($mentry);
	$human = strtolower(addslashes($_POST['human']));
	if ($mentry&&($human=="yes"||$human=="ja")){
		$userid = 0;
		$sql_check = mysql_query("SELECT * FROM sub_entry WHERE entrytext='$mentry'") or die (mysql_error());
		if(mysql_num_rows($sql_check)==0){
			mysql_query("INSERT INTO sub_entry VALUES ('','$userid','$username','$mentry',NOW())") or die (mysql_error());
		}
	}
}else if(isset($_POST['updateblogentry'])){
	$updateblogentry = addslashes($_POST['updateblogentry']);
	$entryid = $_POST['entryid'];
	if ($updateblogentry){
		mysql_query("UPDATE sub_entry SET entrytext='$updateblogentry' WHERE entryid='$entryid'") or die (mysql_error());
	}
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<LINK REL="SHORTCUT ICON" HREF="favicon.ico">
<html>
<meta name="keywords" content="subtitle, subrip, merge, put together, cd1, cd2, two, cds, srt, sub, untertitel, studio, glue, one, rip, subtitle file, divx, extratitles, cat, aneinanderh&auml;ngen, zusammenf&uuml;gen, verkleben, verketten, link, delarue band,subtitle workshop,divx-digest, SubEdit">
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>
<head>
<title>.srt submerge</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="delarue.css" rel="stylesheet" type="text/css">
<script language="JavaScript">
<!--



if (document.layers && navigator.javaEnabled()) {
    netscape.security.PrivilegeManager.enablePrivilege('UniversalFileRead');
    document.myForm.myFile.value = "<?php if(isset($_POST['fileupload2'])){echo $_POST['fileupload2'];}?>";
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
//-->
</script>
</head>

<body onLoad="MM_preloadImages('pics/logo.gif')">
<table width="1000" border="0">
  <tr>
    <td><h3>.srt sub<span class="font12">(title)</span>merger</h3>
      <p>&nbsp;</p></td>
    <td valign="top"><h3>.srt sub time correction </h3></td>
  </tr>
   <tr>
     <td><form name="form1" method="post" action="srtsubmerge.php" enctype="multipart/form-data"><table width="500"  border="0" cellpadding="0" cellspacing="0">
       <tr>
         <td width="300"><div align="right"><span class="onlyred"><a href="#"><img src="pics/yellowsub.jpg" width="49" height="24" border="0">1)</a></span><br>
        cd1.srt file
            <input type="file" name="fileupload">
            <br>
        cd2.srt file
        <input type="file" name="fileupload2">
         </div></td>
         <td align="left" valign="top">&nbsp;&nbsp;&nbsp;</td>
         <td align="left" valign="top"><br>
      insert the two srt subtitle files you want to merge </td>
       </tr>
       <tr>
         <td width="300" align="right"><div align="right"><img src="pics/yellowsub.jpg" width="49" height="24" border="0">2)</div>
             <table  border="0" cellpadding="0" cellspacing="0">
               <tr>
                 <th>h</th>
                 <th>&nbsp;</th>
                 <th>m</th>
                 <th>&nbsp;</th>
                 <th>s</th>
                 <th>&nbsp;</th>
                 <th>ms</th>
               </tr>
               <tr>
                 <td><input name="h" type="text" value="<?php if(isset($h)){echo $h;}elseif(isset($_POST['h'])){echo $_POST['h'];}else{echo"1";}?>" size="3"></td>
                 <th>&nbsp;:&nbsp;</th>
                 <td><input name="m" type="text" value="<?php if(isset($m)){echo $m;}elseif(isset($_POST['m'])){echo $_POST['m'];}else{echo"03";}?>" size="3"></td>
                 <th>&nbsp;:&nbsp;</th>
                 <td><input name="s" type="text" value="<?php if(isset($s)){echo $s;}elseif(isset($_POST['s'])){echo $_POST['s'];}else{echo"27";}?>" size="3"></td>
                 <th>&nbsp;:&nbsp;</th>
                 <td><input name="ms" type="text" value="<?php if(isset($ms)){echo $ms;}elseif(isset($_POST['ms'])){echo $_POST['ms'];}else{echo"000";}?>" size="3"></td>
               </tr>
           </table></td>
         <td align="left" valign="top">&nbsp;</td>
         <td align="left" valign="top"><br>
      insert the time of the end of first part + a few seconds (look up the time of last sub in the first file
        <input name="lookup" type="checkbox" id="lookup3" value="on">
      no merging) </td>
       </tr>
       <tr>
         <td width="300" align="right" valign="top"><img src="pics/yellowsub.jpg" width="49" height="24" border="0">3)<br>
             <input name="submerge" type="submit" id="submerge" value="submerge">
         </td>
         <td align="left" valign="top">&nbsp;</td>
         <td align="left" valign="top"><br>
      try and hit the back button to change 2) if the subs aren't correct yet </td>
       </tr>
     </table>
         <br>
         <table width="500"  border="0" cellpadding="0" cellspacing="0">
           <tr>
             <td align="center"> <br>
                 <br>
                 <table  border="0">
                   <tr>
                     <td><div align="center"><a href="http://www.delarue-berlin.de"><br>
                         </a><a href="http://www.delarue-berlin.de" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('delaruelogo','','pics/logo.gif',1)"><img src="pics/logo_sw.gif" alt="delarue logo" name="delaruelogo" width="88" height="76" border="0"></a><a href="http://www.delarue-berlin.de"><br>
                     </a> </div></td>
                   </tr>
                 </table>
                 <br>
             </td>
           </tr>
         </table>
         <br>
         <table width="500" border="0" cellpadding="0" cellspacing="0">
           <tr>
             <td width="15" height="15" background="pics/tb_01.gif"></td>
             <td background="pics/tb_05.gif"></td>
             <td background="pics/tb_02.gif"></td>
           </tr>
           <tr>
             <td background="pics/tb_06.gif"></td>
             <td background="pics/tb_bg.gif"><div align="left">
                 <table width="100%" border="0" cellpadding="0" cellspacing="0">
                   <tr>
                     <td><strong>links</strong></td>
                   </tr>
                   <tr>
                     <td>
                       <table width="100%"  border="0">
                         <tr>
                           <td><a href="http://www.subbiee.com/">www.subbiee.com/</a></td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td><a href="http://javaopera.ivyro.net/substation/index.html">javaopera.ivyro.net/substation/index.html</a></td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td><a href="http://www.titles.to/">titles.to/</a></td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td><a href="http://titles.box.sk/">titles.box.sk/</a></td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td><a href="http://www.opensubtitles.org">www.opensubtitles.org</a></td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td><a href="http://www.forom.com">www.forom.com for tv series</a></td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td><a href="http://www.subtitles.cz/en/">www.subtitles.cz/en/ </a></td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td><a href="http://divxstation.com/subtitles.asp">divxstation.com/subtitles.asp</a></td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td><a href="http://www.kloofy.net">www.kloofy.net</a> English subs for Asian movies only</td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td><a href="http://artsubs.wz.cz/">artsubs.wz.cz/</a> subs for arty movies</td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td><a href="http://www.divx-digest.com/links/index.php3?category=26">www.divx-digest.com/links/index.php3?category=26</a> subs in all languages</td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td>&nbsp;</td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td><a href="http://www.akira.ru/osc/index.php">online subtitle converter </a></td>
                           <td>&nbsp;</td>
                         </tr>
                         <tr>
                           <td>&nbsp;</td>
                           <td>&nbsp;</td>
                         </tr>
                       </table>                       </td>
                   </tr>
                   <tr>
                     <td><p><strong>If you had success </strong>submit the subtitles to a subtitle page like the ones above. If you want to add your favorite subtitle page to the links above just post it and I'll add it to the list. </p>
                       <p><strong>If you did not have success</strong>:<br>
                         Open your file with a text editor, does it look something like this?</p>
                       <pre>15
00:05:48,357 --&gt; 00:05:50,183
Nothing clean. Right.</pre>                       <pre>16
00:05:50,317 --&gt; 00:05:53,401
I think this guy's a couple of cans
short of a six-pack.</pre>                       <pre>17
00:05:53,528 --&gt; 00:05:56,730
Your clothes, give them to me.
</pre>                       <p>No? Sry you do not have a .srt file. Maybe you can convert it. Go to <a href="http://www.divx-digest.com/software/index4.html">divx-digest</a> and find out how to solve your problem.</p>
                       <p>Yes it looks like that - it still does not work!<br>
                         If you just get a blank page the script timed out. You can try again later but maybe it will keep timing out. Sry you will need a different tool try to search on <a href="http://www.divx-digest.com/software/index4.html">divx-digest</a>.<br>
                         You did not get a blank page? I have no clue post it below.</p>
                       <p><strong>How can i see the .srt subtitles in my movie?<br>
                       </strong>Get <a href="http://www.divx-digest.com/software/vobsub.html">VobSub</a> or a different movie player (it's worth it)</p>
                     </td>
                   </tr>
                   <tr>
                     <td>&nbsp;</td>
                   </tr>
                   <tr>
                     <td><strong>requested-/planned-/future-features/to do list </strong><br>
ansi encoding <br>
subtitle format converter (exotic formats) <br>
framerate correction<br>
SOAP webservice ^^</td>
                   </tr>
                   <tr>
                     <td>&nbsp;</td>
                   </tr>
                 </table>
               </div>
                 <div align="justify"> </div></td>
             <td background="pics/tb_07.gif"></td>
           </tr>
           <tr>
             <td background="pics/tb_03.gif"></td>
             <td background="pics/tb_08.gif"></td>
             <td height="15" width="15" background="pics/tb_04.gif"></td>
           </tr>
         </table>
         <br>
         <table width="500" border="0" cellpadding="0" cellspacing="0">
           <tr>
             <td width="15" height="15" background="pics/tb_01.gif"></td>
             <td background="pics/tb_05.gif"></td>
             <td background="pics/tb_02.gif"></td>
           </tr>
           <tr>
             <td background="pics/tb_06.gif"></td>
             <td background="pics/tb_bg.gif"><div align="left">
                 <?php
#------------------------------------------------------------------------------
#display five blog entrys
$entrynum = 20;#configure the number of entrys to display

#don not configure
$page = 1;
$fist = 1;
$sql_getblog = mysql_query("SELECT entryid FROM sub_entry ORDER BY entryid DESC") or die (mysql_error());;
$nobe = mysql_num_rows($sql_getblog);
for($i=0;$i<$page*$entrynum;$i++){
	if($i<$nobe){
		$row = mysql_fetch_row($sql_getblog);
		if( ($i>=($page*$entrynum-$entrynum))&&($i<=($page*$entrynum)) ){
			$curentryid = $row[0];
			
			#----------------------------
			$sql_blog = mysql_query("SELECT * FROM sub_entry WHERE entryid='$curentryid'");
			$row_blog = mysql_fetch_array($sql_blog);
			foreach( $row_blog AS $key => $val ){
				$$key = stripslashes( $val );
			}
			if($userid){
				$sql_user = mysql_query("SELECT user_name FROM users WHERE userid='$userid'");
				$row_user = mysql_fetch_row($sql_user);
				$user_name = $row_user[0];
				
			}list($date, $time) = split(' ', $insert_date);
#---------------------------- 
echo"  <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">        
 <tr>
             <td><span class=\"font10\">$date</span> $time</td>
             <td align=\"right\"><span class=\"blogusername\">$user_name</span></td>
           </tr>
         </table>
       </div>
         <div align=\"justify\">
           ";
				echo"$entrytext<hr width=\"70%\" noshade>";
		}
	}
}
echo"</div></td>";
?>
               </div>
             <td background="pics/tb_07.gif"></td>
           </tr>
           <tr>
             <td background="pics/tb_03.gif"></td>
             <td background="pics/tb_08.gif"></td>
             <td height="15" width="15" background="pics/tb_04.gif"></td>
           </tr>
         </table>
         <br>
         <table width="500" border="0" cellpadding="0" cellspacing="0">
           <tr>
             <td width="15" height="15" background="pics/tb_01.gif"><a name="form"></a></td>
             <td background="pics/tb_05.gif"></td>
             <td background="pics/tb_02.gif"></td>
           </tr>
           <tr>
             <td background="pics/tb_06.gif"></td>
             <td background="pics/tb_bg.gif">
               <table width="100%" border="0">
                 <tr>
                   <td><input style="border-bottom-width:0;" type="text" name="username">
            nick</td>
                   <td align="right">&nbsp; </td>
                 </tr>
               </table>
               <br>
               <textarea name="mentry" cols="50" rows="6" id="mentry" style="border-bottom-width:0;"></textarea>
               <br>
               <input style="border-bottom-width:1;" type="submit" name="Submit2" value="all aboard">
		<br>
       are you a human? 
       <input name="human" type="text" id="human" size="5">
       <br>
             </td>
             <td background="pics/tb_07.gif"></td>
           </tr>
           <tr>
             <td background="pics/tb_03.gif"></td>
             <td background="pics/tb_08.gif"></td>
             <td height="15" width="15" background="pics/tb_04.gif"></td>
           </tr>
         </table>
     </form></td>
     <td valign="top"><form action="srtsubmerge.php" method="post" enctype="multipart/form-data" name="form2">
       <table width="500"  border="0" cellpadding="0" cellspacing="0">
         <tr>
           <td width="300"><div align="right"><img src="pics/torpedo.gif" width="100" height="13" border="0">1)<br>
        .srt file
            <input name="tcfileupload" type="file" id="tcfileupload">
           </div></td>
           <td align="left" valign="top">&nbsp;&nbsp;&nbsp;</td>
           <td align="left" valign="top"><br>
      insert the srt subtitle you want the time corrected </td>
         </tr>
         <tr>
           <td align="right"><div align="right">start<img src="pics/torpedo.gif" width="100" height="13" border="0">2)</div>
               <table  border="0" cellpadding="0" cellspacing="0">
                 <tr>
                   <th>h</th>
                   <th>&nbsp;</th>
                   <th>m</th>
                   <th>&nbsp;</th>
                   <th>s</th>
                   <th>&nbsp;</th>
                   <th>ms</th>
                 </tr>
                 <tr>
                   <td><input name="tcsh" type="text" value="<?php if(isset($tcsh)){echo $tcsh;}elseif(isset($_POST['tcsh'])){echo $_POST['tcsh'];}else{echo"00";}?>" size="3"></td>
                   <th>&nbsp;:&nbsp;</th>
                   <td><input name="tcsm" type="text" value="<?php if(isset($tcsm)){echo $tcsm;}elseif(isset($_POST['tcsm'])){echo $_POST['tcsm'];}else{echo"00";}?>" size="3"></td>
                   <th>&nbsp;:&nbsp;</th>
                   <td><input name="tcss" type="text" value="<?php if(isset($tcss)){echo $tcss;}elseif(isset($_POST['tcss'])){echo $_POST['tcss'];}else{echo"00";}?>" size="3"></td>
                   <th>&nbsp;:&nbsp;</th>
                   <td><input name="tcsms" type="text" value="<?php if(isset($tcsms)){echo $tcsms;}elseif(isset($_POST['tcsms'])){echo $_POST['tcsms'];}else{echo"000";}?>" size="3"></td>
                 </tr>
             </table></td>
           <td align="left" valign="top">&nbsp;</td>
           <td align="left" valign="top"><br>
      insert the time where the timecorrection should start </td>
         </tr>
         <tr>
           <td width="300" align="right"><div align="right">add<img src="pics/torpedo.gif" width="100" height="13" border="0">3)</div>
               <table border="0" cellspacing="0" cellpadding="5">
                 <tr>
				   <td align="left"><input name="vorzeichen" type="radio" value="1" checked>&nbsp;<strong>+<br>
                    <input name="vorzeichen" type="radio" value="0">
-</strong></td>
                   <td><table  border="0" cellpadding="0" cellspacing="0">
                     <tr>
                       <th> h</th>
                       <th>&nbsp;</th>
                       <th>m</th>
                       <th>&nbsp;</th>
                       <th>s</th>
                       <th>&nbsp;</th>
                       <th>ms</th>
                     </tr>
                     <tr>
                       <td><input name="tcch" type="text" value="<?php if(isset($tcch)){echo $tcch;}elseif(isset($_POST['tcch'])){echo $_POST['tcch'];}else{echo"0";}?>" size="3"></td>
                       <th>&nbsp;:&nbsp;</th>
                       <td><input name="tccm" type="text" value="<?php if(isset($tccm)){echo $tccm;}elseif(isset($_POST['tccm'])){echo $_POST['tccm'];}else{echo"00";}?>" size="3"></td>
                       <th>&nbsp;:&nbsp;</th>
                       <td><input name="tccs" type="text" value="<?php if(isset($tccs)){echo $tccs;}elseif(isset($_POST['tccs'])){echo $_POST['tccs'];}else{echo"27";}?>" size="3"></td>
                       <th>&nbsp;:&nbsp;</th>
                       <td><input name="tccms" type="text" value="<?php if(isset($tccms)){echo $tccms;}elseif(isset($_POST['tccms'])){echo $_POST['tccms'];}else{echo"999";}?>" size="3"></td>
                     </tr>
                   </table></td>
                 </tr>
               </table>
            </td>
           <td align="left" valign="top">&nbsp;</td>
           <td align="left" valign="top"><br>
      add/substract time to all subtitles starting at 2)</td>
         </tr>
         <tr>
           <td width="300" align="right" valign="top"><img src="pics/torpedo.gif" width="100" height="13" border="0">4)<br>
               <input name="timecorrection" type="submit" id="timecorrection" value="fire">
           </td>
           <td align="right" valign="top">&nbsp;</td>
           <td align="left" valign="top"><p><br>
            try and hit the back button to change 2) and 3) if the sub isn't correct yet </p>            </td>
         </tr>
       </table>
       <h3><br>
         .srt sub split<br>
       </h3>
       <table width="500"  border="0" cellpadding="0" cellspacing="0">
         <tr>
           <td width="300"><div align="right"><img src="pics/mooredmine_s.gif" width="20" height="24">1)<br>
        .srt file
            <input name="spfileupload" type="file" id="spfileupload">
           </div></td>
           <td align="left" valign="top">&nbsp;&nbsp;&nbsp;</td>
           <td align="left" valign="top"><br>
      insert the srt subtitle you want to split </td>
         </tr>
         <tr>
           <td align="right"><div align="right"><img src="pics/mooredmine_s.gif" width="20" height="24">2)</div>
               <table  border="0" cellpadding="0" cellspacing="0">
                 <tr>
                   <th>h</th>
                   <th>&nbsp;</th>
                   <th>m</th>
                   <th>&nbsp;</th>
                   <th>s</th>
                   <th>&nbsp;</th>
                   <th>ms</th>
                 </tr>
                 <tr>
                   <td><input name="sph" type="text" id="sph" value="<?php if(isset($tcsh)){echo $tcsh;}elseif(isset($_POST['tcsh'])){echo $_POST['tcsh'];}else{echo"00";}?>" size="3"></td>
                   <th>&nbsp;:&nbsp;</th>
                   <td><input name="spm" type="text" id="spm" value="<?php if(isset($tcsm)){echo $tcsm;}elseif(isset($_POST['tcsm'])){echo $_POST['tcsm'];}else{echo"00";}?>" size="3"></td>
                   <th>&nbsp;:&nbsp;</th>
                   <td><input name="sps" type="text" id="sps" value="<?php if(isset($tcss)){echo $tcss;}elseif(isset($_POST['tcss'])){echo $_POST['tcss'];}else{echo"00";}?>" size="3"></td>
                   <th>&nbsp;:&nbsp;</th>
                   <td><input name="spms" type="text" id="spms" value="<?php if(isset($tcsms)){echo $tcsms;}elseif(isset($_POST['tcsms'])){echo $_POST['tcsms'];}else{echo"000";}?>" size="3"></td>
                 </tr>
             </table></td>
           <td align="left" valign="top">&nbsp;</td>
           <td align="left" valign="top"><br>
      insert the time where the subtitle file should be split </td>
         </tr>
         <tr>
           <td width="300" align="right" valign="top"><img src="pics/mooredmine_s.gif" width="20" height="24">3)<br>
            <input name="split" type="submit" id="split" value="blub">           </td>
           <td align="right" valign="top">&nbsp;</td>
           <td align="left" valign="top"><p><br>
        try and hit the back button to change 2) if the subs aren't correct yet </p></td>
         </tr>
       </table>
     </form>     
<br>
<br>
<br>
<center>
<script type="text/javascript"><!--
google_ad_client = "pub-2007858250553812";
//160x600, Erstellt 09.11.07
google_ad_slot = "8988476396";
google_ad_width = 160;
google_ad_height = 600;
//--></script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
<center>
</td>
   </tr>
   
</table>
 

 <p>&nbsp;</p>
</body>
</html>

<?php
#---------------------------------------------------------
function convertcd2($input1,$input2){
	
	$h = $_POST['h'];$m = $_POST['m'];$s = $_POST['s'];	$ms = $_POST['ms'];	
	
	$lines = file($input1);
	#--------------------------------
	#set number of subtitles to the last subtitle in the first file
	$c=1;#number of subtitle
	foreach ($lines as $read) {
		#echo "::::::$read<br>";
		if(preg_match("/^(\d+)/m",$read,$matches)){
			if($c==$matches[1]){
				$c++;
			}
		}
	}$c--;
	#echo "------>$c";
	#--------------------------------

	$ct=1;
	$lines = file($input2);
	$newfile = "";
	foreach ($lines as $read) {
		if(preg_match("/(\d+):(\d+):(\d+),(\d+) --> (\d)+:(\d+):(\d+),(\d+)/",$read,$matches)){
			#my ($x1,$x2,$x3,$x4,$x5,$x6,$x7,$x8);
			#my (h  , m  ,s , ms,h  , m  ,s , ms);
			#------------------------

			$x4 = $matches[4]+$ms;
			if($x4>=1000){
				$x4 = -(1000-$x4);
				$x3 = 1;	
			}else{$x3 = 0;}
			$x3 += $matches[3]+$s;
			if($x3>=60){
				$x3 = -(60-$x3);
				$x2 = 1;	
			}else{$x2 = 0;}
			$x2 += $matches[2]+$m;
			if($x2>=60){
					$x2 = -(60-$x2);
					$x1 = 1;
			}else{$x1 = 0;}
			$x1 += $matches[1]+$h;
			
			#------------------------
			$x8 = $matches[8]+$ms;
			if($x8>=1000){
					$x8 = -(1000-$x8);
					$x7 = 1;
			}else{$x7 = 0;}
			$x7 += $matches[7]+$s;
			if($x7>=60){
					$x7 = -(60-$x7);
					$x6 = 1;
			}else{$x6 = 0;}
			$x6 += $matches[6]+$m;
			if($x6>=60){
					$x6 = -(60-$x6);
					$x5 = 1;
			}else{$x5 = 0;}
			$x5 += $matches[5]+$h;
			#echo "$x4 - $x8<br>\r\n";
			#------------------------
			#$newfile = "$1:$2:$3,$4 --> $5:$6:$7,$8\r\n";
			#$newfile .= sprintf ("%02d:%02d:%02d,%03d --> %02d:%02d:%02d,%03d\r\n",$x1,$x2,$x3,$4,$x5,$x6,$x7,$8);
			#$inarr = array("$x1","$x2","$x3","$4","$x5","$x6","$x7","$8");
			$newfile .= sprintf ("%02d:%02d:%02d,%03d --> %02d:%02d:%02d,%03d\r\n","$x1","$x2","$x3","$x4","$x5","$x6","$x7","$x8");
			#echo "$newfile\r\n\r\n";
		}else if(preg_match("/^(\d+)/m",$read,$matches)){#if line with subtile number
			if($ct==$matches[1]){#check for the right number of the sub
				$newfile .= "".$matches[1]+$c."\r\n";
				$ct++;
			}
			
		}else{#copy empty lines and text
			$newfile .= $read;
			#print "$newfile\n";
		}	
		
		#$newfile .=$read;
		#echo "--------------".$read;
	}
	return $newfile;
}


function two($x) {return (($x>9)?"":"0")+$x;}
function three($x) {return (($x>99)?"":"0")+(($x>9)?"":"0")+$x;}

function ms2time($ms) {
	if($ms<0){
		$ms *=-1;
	}
	$sec = floor($ms/1000);
	$ms = $ms % 1000;
	$ms = three($ms);
	
	$min = floor($sec/60);
	$sec = $sec % 60;
	$sec = two($sec);
	
	$hr = floor($min/60);
	$min = $min % 60;
	$min = two($min);
	
	$day = floor($hr/60);
	$hr = $hr % 60;
	$hr = two($hr);
	
	return array($hr,$min,$sec,$ms);
}

function time2ms ($h,$m,$s,$ms){
	
	return ((($h*60+$m)*60+$s)*1000+$ms);
}

function timcorrection($input){
	#echo "yo1";
	$tcsh = $_POST['tcsh'];$tcsm = $_POST['tcsm'];$tcss = $_POST['tcss'];	$tcsms = $_POST['tcsms'];	
	$h = $_POST['tcch'];$m = $_POST['tccm'];$s = $_POST['tccs'];	$ms = $_POST['tccms'];	
	$splittimeMS = time2ms($tcsh,$tcsm,$tcss,$tcsms);# (($tcsh*60+$tcsm)*60+$tcss)*1000+$tcsms;
	$movetimeMS = time2ms($h,$m,$s,$ms); #(($h*60+$m)*60+$s)*1000+$ms;
	list($hr,$min,$sec,$ms) = ms2time($movetimeMS);
	if (!$_POST['vorzeichen']){ $movetimeMS *=  -1;}
	#echo "$movetimeMS - $day,$hr,$min,$sec,$ms";echo "yo";
	$lines = file($input);
	$newfile = "";
	foreach ($lines as $read) {
		if(preg_match("/(\d+):(\d+):(\d+),(\d+) --> (\d)+:(\d+):(\d+),(\d+)/",$read,$matches)){
			#my ($x1,$x2,$x3,$x4,$x5,$x6,$x7,$x8);
			#my (h  , m  ,s , ms,h  , m  ,s , ms);
			#------------------------
			if($splittimeMS<((($matches[1]*60+$matches[2])*60+$matches[3])*1000+$matches[4])){
			#if(($tcsh <= $matches[1])&&($tcsm <= $matches[2])&&($tcss <= $matches[3])&&($tcsms <= $matches[4])){
				#echo "before: ".time2ms($matches[1],$matches[2],$matches[3],$matches[4])." after: ".(time2ms($matches[1],$matches[2],$matches[3],$matches[4])+$movetimeMS)."\n<br>";
				$sub1 = time2ms($matches[1],$matches[2],$matches[3],$matches[4])+$movetimeMS;
				$sub2 = time2ms($matches[5],$matches[6],$matches[7],$matches[8])+$movetimeMS;
				list($x1,$x2,$x3,$x4) = ms2time($sub1);
				list($x5,$x6,$x7,$x8) = ms2time($sub2);
				$newfile .= sprintf ("%02d:%02d:%02d,%03d --> %02d:%02d:%02d,%03d\r\n","$x1","$x2","$x3","$x4","$x5","$x6","$x7","$x8");
				#echo "$newfile\n\n";
			}else{
				$newfile .= $read;
			}
		}else{#copy empty lines and text
			$newfile .= $read;
			#print "$newfile\n";
		}	
		
		#$newfile .=$read;
		#echo "--------------".$read;
	}
	return $newfile;
}

function splitsrt($input){
	$h = $_POST['sph'];$m = $_POST['spm'];$s = $_POST['sps'];	$ms = $_POST['spms'];	
	
	$splittimeMS = time2ms($h,$m,$s,$ms); #(($h*60+$m)*60+$s)*1000+$ms;
	#echo "spltittime is: $splittimeMS <br>";
	$lines = file($input);
	$newfile[0] = "";
	$newfile[1] = "1\r\n";
	$spflag = 0;#flag for cd1 = 0 and cd2 = 1
	$ct = 2;# number of subtitle for cd2
	foreach ($lines as $read) {
		
		if(preg_match("/(\d+):(\d+):(\d+),(\d+) --> (\d)+:(\d+):(\d+),(\d+)/",$read,$matches)){
			#my ($x1,$x2,$x3,$x4,$x5,$x6,$x7,$x8);
			#my (h  , m  ,s , ms,h  , m  ,s , ms);
			#------------------------
			
			if($splittimeMS<((($matches[1]*60+$matches[2])*60+$matches[3])*1000+$matches[4])){
				$spflag = 1;
				
				$x4 = $matches[4]-$ms;
				if($x4<0){
					$x4 = 1000+$x4;
					$x3 = -1;	
				}else{$x3 = 0;}
				$x3 += $matches[3]-$s;
				if($x3<0){
					$x3 = 60+$x3;
					$x2 = -1;	
				}else{$x2 = 0;}
				$x2 += $matches[2]-$m;
				if($x2<0){
						$x2 = 60+$x2;
						$x1 = -1;
				}else{$x1 = 0;}
				$x1 += $matches[1]-$h;
				
				#------------------------
				$x8 = $matches[8]-$ms;
				if($x8<0){
						$x8 = 1000+$x8;
						$x7 = -1;
				}else{$x7 = 0;}
				$x7 += $matches[7]-$s;
				if($x7<0){
						$x7 = 60+$x7;
						$x6 = -1;
				}else{$x6 = 0;}
				$x6 += $matches[6]-$m;
				if($x6<0){
						$x6 = 60+$x6;
						$x5 = -1;
				}else{$x5 = 0;}
				$x5 += $matches[5]-$h;
				#echo "$x4 - $x8<br>\n";
				#------------------------
				#$newfile = "$1:$2:$3,$4 --> $5:$6:$7,$8\n";
				#$inarr = array("$x1","$x2","$x3","$4","$x5","$x6","$x7","$8");
				$newfile[$spflag] .= sprintf ("%02d:%02d:%02d,%03d --> %02d:%02d:%02d,%03d\r\n","$x1","$x2","$x3","$x4","$x5","$x6","$x7","$x8");
				#echo "-->$newfile[$spflag]\r\n<br>";
			}else{
				$newfile[$spflag] .= $read;
				#echo "check<br>\r\n";
			}
		}else if($spflag){
			if(preg_match("/^(\d+)\s*$/",$read,$matches)){#if line with subtile number
				$newfile[$spflag] .= $ct."\r\n";
				$ct++;
			}else{#copy empty lines and text
				$newfile[$spflag] .= $read;
			}	
			
		}else{#copy empty lines and text
			$newfile[$spflag] .= $read;
			#print "$newfile\r\n";
		}	
		
		#$newfile .=$read;
		#echo "--------------".$read;
	}
	#echo "this:<br>this:<br><pre>$newfile[1]</pre>";
			
	return $newfile;
}


/*				$x4 = $matches[4]+$ms;
				if($x4>=1000){
					$x4 = -(1000-$x4);
					$x3 = 1;	
				}else{$x3 = 0;}
				$x3 += $matches[3]+$s;
				if($x3>=60){
					$x3 = -(60-$x3);
					$x2 = 1;	
				}else{$x2 = 0;}
				$x2 += $matches[2]+$m;
				if($x2>=60){
						$x2 = -(60-$x2);
						$x1 = 1;
				}else{$x1 = 0;}
				$x1 += $matches[1]+$h;
				
				#------------------------
				$x8 = $matches[8]+$ms;
				if($x8>=1000){
						$x8 = -(1000-$x8);
						$x7 = 1;
				}else{$x7 = 0;}
				$x7 += $matches[7]+$s;
				if($x7>=60){
						$x7 = -(60-$x7);
						$x6 = 1;
				}else{$x6 = 0;}
				$x6 += $matches[6]+$m;
				if($x6>=60){
						$x6 = -(60-$x6);
						$x5 = 1;
				}else{$x5 = 0;}
				$x5 += $matches[5]+$h;
				#echo "$x4 - $x8<br>\n";
*/
				#------------------------
				#$newfile = "$1:$2:$3,$4 --> $5:$6:$7,$8\n";
				#$inarr = array("$x1","$x2","$x3","$4","$x5","$x6","$x7","$8");
?>
