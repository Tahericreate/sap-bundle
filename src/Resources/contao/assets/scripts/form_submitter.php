<?php	
	session_start();
	parse_str($_POST['formData'], $postDataArray);
	
	if($postDataArray['antiSpamKey'] == $_SESSION['antiSpamKey']){
		$salutation = $postDataArray["salutation"];
		$fullname = $postDataArray["full_name"];
		$email = $postDataArray["email"];
		$phone = $postDataArray["phone"];
		$interest = $postDataArray["interest"];
		$message = $postDataArray["message"];
		
		mysql_connect($_SESSION['db_hostname'], $_SESSION['db_username'], $_SESSION['db_password']) or die('Error Connecting to database::' . mysql_error());
		mysql_select_db($_SESSION['db_name']) or die('Error selecting database::' . mysql_error());
		
		$dbCheckSubmit = mysql_query("SELECT id FROM vi_contactform_contact WHERE antispam='" . $_SERVER['REMOTE_ADDR'] . "' AND tstamp < NOW() - INTERVAL 1 DAY") or die('Error checking repeat entry::' . mysql_error());
		if(mysql_num_rows($dbCheckSubmit) == 0){		
			mysql_query("INSERT INTO vi_contactform_contact(tstamp, salutation, full_name, email, phone, interest, message, antispam) VALUES(" . time() . ", '" . mysql_real_escape_string($salutation) . "', '" . mysql_real_escape_string($fullname) . "', '" . mysql_real_escape_string($email) . "', '" . mysql_real_escape_string($phone) . "', '" . mysql_real_escape_string($interest) . "', '" . mysql_real_escape_string($message) . "', '" . $_SERVER['REMOTE_ADDR'] . "')") or die('Error inserting contact information::' . mysql_error());
							
			// Send Email
			$receipent = $postDataArray["email"];
			$sender = "From: info@vrisini.com";
			$subject = "Vrisini Infotech - Information Received";
			$msg = "Thank you for contacting Vrisini Infotech";  
			//mail($receipent,$subject,$msg,$sender);
			
			echo '
				<p class="bold margin20 column-title">Contact information received</p>
				<p>Thank you for contacting us. One of our representatives will soon get in touch with you.</p>
				<p>Best regards,<br>Vrisini Team</p>
			';
		}
		else {
			echo '
				<p class="bold margin20 column-title">Contact already in process</p>
				<p>You have already contacted us within the last 24 hours.<br>
				Please be patient, one of our representatives will soon get in touch with you.</p>
				<p>Best regards,<br>Vrisini Team</p>
			';
		}
	}
	else{
		echo '
			<p class="bold margin20 column-title">Error! Information not submitted</p>
			<p>Your contact initiative did not succeed. If there was a genuine problem, please try one of our other contact options.</p>
			<p>Best regards,<br>Vrisini Team</p>
		'; // . '<br>POST::' . $postDataArray['antiSpamKey'] . ' SESSION::' . $_SESSION['antiSpamKey']
	}
?>
