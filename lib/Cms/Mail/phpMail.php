<?php
require_once("class.phpmailer.php");

class Cms_Mail_phpMail {

	public static function send($data) {
		$mail = new PHPMailer();

		if (cfg()->is_smtp) {
			$mail->IsSMTP();    // set mailer to use SMTP
		}
		$mail->Host = cfg()->smtp_host;
		$mail->Port = cfg()->smtp_port;
		$mail->SMTPAuth = cfg()->smtp_auth;     // turn on SMTP authentication
		$mail->Username = cfg()->mail_user;  // SMTP username
		$mail->Password = cfg()->mail_password; // SMTP password

		$mail->From = $data['from_email'];
		$mail->FromName = $data['from_name'];
		$mail->AddAddress($data['to_mail']);
		$mail->SetFrom($data['from_email'], $data['from_name']);
		$mail->AddReplyTo($data['from_email'], $data['from_name']);

		//$mail->SMTPDebug = 10;
		//$mail->SMTPSecure = 'tls';

		$mail->WordWrap = cfg()->mail_world_warp; // set word wrap to 50 characters
		$mail->IsHTML(cfg()->is_html);                                  // set email format to HTML

		$mail->Subject = $data['subject'];
		$mail->Body = $data['message_html'];

		if (!$mail->Send()) {
			return $mail->ErrorInfo;
		}

		return true;
	}

}
