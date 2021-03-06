Document Emailer
================

This is our NAGW project for recording those who have requested a document.  For more information on NAGW, visit [www.nagw.org](http://www.nagw.org/).

Upon completion of the form, the PDF is emailed.

Setup Notes
-----------

This application uses Swift Mailer to send emails.  You can get info and 
download the library at www.swiftmailer.org.  When setting this application up, 
you'll want to set your mail server settings and location to the Swift Mailer 
library to whatever is appropriate for your system.

You may also wish to adjust the maxAttempts variable to change the number of 
times one person can download a document.

The SQL file will set up a basic table that will work with the application.  
You'll need to set up a database and user first.

Credits
-------

Project originated by: Liz Rainey

This started code by: Jeremy Coulson

Important help to this point by: Peter Watkins

Changes
-------

### 2012-04-26:
Merged rpringle's maxAttempts setting change.

### 2011-05-16:
Added email confirmation field. Also added check to make sure the document has 
not been requested more than 3 times to prevent mail bombing. Cleaned up PHP 
a bit; removed debugging echos, etc. Incidentally, the HTML validates as HTML5.

### 2011-05-13:
Added confirmation of email sending today.  Next: "confirm email" field. 
By the way, I know the detailed error messages shouldn't be used in production.

### 2011-05-10:
Next step would probably be to add confirmation in db that message was sent.

### 2011-05-09:
Switched to using Swift Mailer (swiftmailer.org). Seems great.

### 2011-05-09:
Works fine if web server runs SMTP.
Now to figure out how to specify SMTP authentication for remote SMTP.
