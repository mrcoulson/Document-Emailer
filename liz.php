<?php

// Uncomment to see detailed error messages.
// error_reporting(E_ALL);
// ini_set('display_errors','On');

require_once 'D:\website\webmaster\swift\lib\swift_required.php'; // Include Swift Mailer

// Define result variable
$result = 0;

// Define the unique token.
$idToken = uniqid();

// Get variables.
$textFile = "liz.txt";
$strToName = $_POST["txtName"];
$strToEmail = $_POST["txtEmail"];
$strIP = $_SERVER['REMOTE_ADDR'];
$strDateTime = date("Y-n-j H:i:s", time());

// Define MaxAttempts variable
// Defaults to three
$maxAttempts = "3";

// Connect to db.
$link = mysql_connect ("localhost", "user", "pass") or die ("Error: " . mysql_error());
mysql_select_db("pdfdownload");

// How many times has this person requested a document?
$queryForCountingEntries = mysql_query("SELECT COUNT(*) FROM requests WHERE email = '" . $strToEmail . "'") or die ("Could not count entries.");
$rowEntries = mysql_fetch_row($queryForCountingEntries);
if ($rowEntries[0] > $maxAttempts)
{
	// The document has been requested too many times.  Stop.
	echo "You have requested this document " . $rowEntries[0] . " times. You are only allowed " . $maxAttempts . " requests.";
	// Close db connection.
	mysql_close ($link);
	echo "<br /><a href='liz.html'>Back</a><br />";
}
else
{
	// The document has not been requested too many times.  Continue.
	// Perform query.
	$queryBeforeSend = mysql_query("INSERT INTO requests (name, email, ip, request_datetime, token) VALUES ('" . $strToName . "', '" . $strToEmail . "', '" . $strIP . "', '" . $strDateTime . "', '" . $idToken . "')") or die ("Error updating database.");
	// Display link to go back and try again. In a production situation, this would do something more useful.
	echo "Added.<br /><a href='liz.html'>Back</a><br />";

	// Set up email.
	$strTo = "$strToName <$strToEmail>"; // this uses the submitted txtName and txtEmail from the requestor
	$strFromName = "Webmaster"; // edit this to your specified name
	$strFromEmail = "webmastr@co.frederick.va.us"; // edit this to your specified email address
	$strSubject = "super awesome pdf attached!"; // edit this to your specified subject text
	$strMessage = "Here is the file you requested."; // edit this to you specified message text
	$fileattname = "lizpdf.pdf"; // edit this to your specified filename

	// Create the transport with your SMTP settings.
	$transport = Swift_SmtpTransport::newInstance('host', 25)
		->setUsername('user')
		->setPassword('pass')
		;

	// Create the mailer using your created transport.
	$mailer = Swift_Mailer::newInstance($transport);

	// Create the message.
	$message = Swift_Message::newInstance()
		//Give the message a subject
		->setSubject($strSubject)
		// Set the From address with an associative array
		->setFrom(array($strFromEmail => $strFromName))
		// Set the To addresses with an associative array
		->setTo(array($strToEmail => $strTo))
		// Give it a body
		->setBody($strMessage)
		// Optionally add any attachments
		->attach(Swift_Attachment::fromPath($fileattname))
		;

	// Send the message.
	$result = $mailer->send($message);

	// Get the id of the record of the email just sent based on its token.
	$queryAfterSend = mysql_query("SELECT id FROM requests WHERE token = '" . $idToken . "'") or die ("Error retrieving token.");
	$rowID = mysql_fetch_row($queryAfterSend);

	// Check for success.
	if ($result == 0)
	{
		// The result variable has not changed and the email was probably not sent.
		printf("Oh, crap! Sent %d messages.\n", $result);
		// Get datetime for the query.
		$strEmailDateTime = date("Y-n-j H:i:s", time());
		// Update the record with the sent time and result (0 for not successful).
		$queryInsertConfirmationNo = "UPDATE requests SET email_datetime = '" . $strEmailDateTime . "', email_success = 0 WHERE id = ". $rowID[0] . "";
		mysql_query($queryInsertConfirmationNo) or die ("Could not insert confirmation.");
	}
	else
	{
		// The result variable has changed and the email was probably sent.
		printf("Yay! Sent %d message(s).\n", $result);
		// Get datetime for the query.
		$strEmailDateTime = date("Y-n-j H:i:s", time());
		// Update the record with the sent time and result (1 for successful).
		$queryInsertConfirmationYes = "UPDATE requests SET email_datetime = '" . $strEmailDateTime . "', email_success = 1 WHERE id = ". $rowID[0] . "";
		mysql_query($queryInsertConfirmationYes) or die ("Could not insert confirmation.");
	}

	// Close db connection because we're finished.
	mysql_close ($link);
}
	
?>
