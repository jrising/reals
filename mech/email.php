<?php

/*
 * Send an email, based on a template in a file and the associated
 * meta email information
 */
function EmailTemplate($tplfile, $tos, $repl = array()) {
	$tplfile = GetEmailTemplate($tplfile);

	// load tplfile
	get_include_contents($tplfile);
	$results = $GLOBALS['gicResults'];

	// validate
	if (isset($results[4])) {
		$mods = $results[4]($repl);
		if (!empty($mods)) {
			return $mods;
		}
	}

	$from = $results[0];
	$subject = $results[1];
	$ccs = isset($results[2]) ? $results[2] : array();
	$bcs = isset($results[3]) ? $results[3] : array();

	SendEmailTemplate($from, $subject, $tplfile, $tos, $ccs, $bcs, $repl);

	return array();
}

/*
 * Send an email based on a template in a file
 */
function SendEmailTemplate($from, $subject, $tplfile, $tos,
					  $ccs = array(), $bcs = array(), $repl = array()) {
	$tplfile = GetEmailTemplate($tplfile);

	$message = TemplateReplace($tplfile, $repl);
	$subject = StringReplace($subject, $repl);

	SendEmail($from, $subject, $message, $tos, $ccs, $bcs);
}

/*
 * Fix up a email file path to point to an existing file
 */
function GetEmailTemplate($tplfile) {
	// check for location of email template
	if (!file_exists($tplfile)) {
		$newfile = rincdir("mails/$tplfile.tpl");
		if (!file_exists($newfile)) {
			EFailure(EC_COMMUNICATION, "Email template %s not found", $tplfile);
		} else {
			return $newfile;
		}
	}

	return $tplfile;
}

/*
 * Send an email with the supplied information
 */
function SendEmail($from, $subject, $message,
				   $tos, $ccs = array(), $bcs = array()) {
	$headers = "From: $from\n";
	foreach ($ccs as $ccemail) {
		$headers .= "Cc: $ccemail\n";
	}
	foreach ($bcs as $bcemail) {
		$headers .= "Bcc: $bcemail\n";
	}

	// Send message
	if (is_string($tos)) {
		$tos = array($tos);
	}
	foreach ($tos as $toemail) {
		if (!mb_send_mail($toemail, $subject, $message, $headers)) {
			EFailure(EC_COMMUNICATION, "Failed to send email to %s", $toemail);
		}
	}
}

?>