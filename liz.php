<?php

// Uncomment to see detailed error messages.
// error_reporting(E_ALL);
// ini_set('display_errors','On');

require_once '/home/jcoulson/jcoulson.com/document-emailer/swift/lib/swift_required.php'; // Include Swift Mailer

// Get variables.
$textFile = "liz.txt";
$strToName = $_POST["txtName"];
$strToEmail = $_POST["txtEmail"];
$strIP = $_SERVER['REMOTE_ADDR'];
$strDateTime = date("Y-n-j H:i:s", time());

// Connect to db.
$link = mysql_connect ("localhost", "user", "pass") or die ("Error: " . mysql_error());
mysql_select_db("pdfdownload");
// Perform query.
$query = "INSERT INTO requests (name, email, ip, request_datetime) VALUES ('" . $strToName . "', '" . $strToEmail . "', '" . $strIP . "', '" . $strDateTime . "')";
mysql_query($query) or die ("Error updating database.");
// Close db connection.
mysql_close ($link);

// Display link.
echo "Added.<br /><a href='liz.html'>Back</a>";


// Send email.

$strTo = "$strToName <$strToEmail>"; // this uses the submitted txtName and txtEmail from the requestor
$strFromName = "Webmaster"; // edit this to your specified name
$strFromEmail = "webmastr@co.frederick.va.us"; // edit this to your specified email address
$strSubject = "super awesome pdf attached!"; // edit this to your specified subject text
$strMessage = "Here is the file you requested."; // edit this to you specified message text
$fileattname = "lizpdf.pdf"; // edit this to your specified filename

//Create the transport with your SMTP settings (Gmail here as example)
$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
  ->setUsername('user@gmail.com')
  ->setPassword('pass')
  ;

//Create the mailer using your created transport
$mailer = Swift_Mailer::newInstance($transport);

//Create the message
$message = Swift_Message::newInstance()
	//Give the message a subject
	->setSubject($strSubject)
	//Set the From address with an associative array
  	->setFrom(array($strFromEmail => $strFromName))
	//Set the To addresses with an associative array
  	->setTo(array($strToEmail => $strTo))
	//Give it a body
 	->setBody($strMessage)
	//Optionally add any attachments
  	->attach(Swift_Attachment::fromPath($fileattname))
	;

//Send the message
$result = $mailer->send($message);

?>
