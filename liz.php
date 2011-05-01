<?php

// Uncomment to see detailed error messages.
// error_reporting(E_ALL);
// ini_set('display_errors','On');

// Get variables.
$textFile = "liz.txt";
$strToName = $_POST["txtName"];
$strToEmail = $_POST["txtEmail"];
$strIP = $_SERVER['REMOTE_ADDR'];
$strDateTime = date("Y-n-j H:i:s", time());

// Connect to db.
$link = mysql_connect ("localhost", "pdfdownload", "pdfdownload") or die ("Error: " . mysql_error());
mysql_select_db("pdfdownload");
// Perform query.
$query = "INSERT INTO requests (name, email, ip, request_datetime) VALUES ('" . $strToName . "', '" . $strToEmail . "', '" . $strIP . "', '" . $strDateTime . "')";
mysql_query($query) or die ("Error updating database.");
// Close db connection.
mysql_close ($link) ;

// Display link.
echo "Added.<br /><a href='liz.html'>Back</a>";


// send email...

$strTo = "$strToName <$strToEmail>"; // this uses the submitted txtName and txtEmail from the requestor
$strFromName = "Webmaster"; // edit this to your specified name
$strFromEmail = "webmastr@co.frederick.va.us"; // edit this to your specified email address
$strSubject = "super awesome pdf attached!"; // edit this to your specified subject text
$strMessage = "Here is the file you requested."; // edit this to you specified message text

// echo $_SERVER['DOCUMENT_ROOT']; // uncomment this line to determine where $_SERVER['DOCUMENT_ROOT'] points to.
// edit your_path_here so that it completes the local server path to the file attachment
$fileattpath = $_SERVER['DOCUMENT_ROOT']."/webmaster/";

$fileatttype = "application/pdf"; // edit this to your specified attached file type
$fileattname = "lizpdf.pdf"; // edit this to your specified filename

// call mail_attachment function
mail_attachment($fileattname, $fileattpath, $fileatttype, $strTo, $strFromEmail, $strFromName, $strFromEmail, $strSubject, $strMessage);

function mail_attachment($filename, $path, $filetype, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
    $file = $path.$filename;
    $file_size = filesize($file);
    $handle = fopen($file, "rb");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $name = basename($file);
    $header = "From: ".$from_name." <".$from_mail.">\r\n";
    $header .= "Reply-To: ".$replyto."\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message."\r\n\r\n";
    $header .= "--".$uid."\r\n";
    $header .= "Content-Type: ". $filetype ."; name=\"".$filename."\"\r\n";
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
    $header .= $content."\r\n\r\n";
    $header .= "--".$uid."--";
    if (mail($mailto, $subject, "", $header)) {
        echo "mail send ... OK"; // or use booleans here
    } else {
        echo "mail send ... ERROR!";
    }
}


?>
