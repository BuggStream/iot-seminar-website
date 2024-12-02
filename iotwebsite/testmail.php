<html>
  <head>
    <title>Untitled</title>
  </head>
  <body>
	<?php
	require '../vendor/autoload.php'; // Include PHPMailer

	$mail = new PHPMailer\PHPMailer\PHPMailer();
	$mail->isSMTP();
	$mail->Host = 'smtp.freesmtpservers.com'; // Your SMTP server
	//$mail->SMTPAuth = true;
	//$mail->SMTPSecure = 'tls'; // Use 'tls' or 'ssl'
	$mail->Port = 25; // Use 587 for 'tls' or 465 for 'ssl'

	$mail->setFrom('admin@slechtvalk.tudelft.nl', 'Kanker Test');
	$mail->addAddress('lrnz.albani@gmail.com');
	$mail->Subject = 'Test from mail sender';
	$mail->Body = 'This is a test email from Auto Mailer.';

	if ($mail->send()) {
		echo 'Email sent successfully';
	} else {
		echo 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
	}
	?>
	  Mail Sent.
  </body>
</html>
