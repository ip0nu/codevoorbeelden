<?php

   
	$smsNumbers	    = $_POST['smsTo'];
	$smsText		= $_POST['smsText'];

	if(substr($smsNumbers,-1) !== ";") { $smsNumbers .= ";"; }
	
	$numbers		= explode(';', $smsNumbers,-1 );
	$url = 'http://qr.atpi.net/tc.dll/sendsms';

	foreach ($numbers as $smsTo) {
		if($smsTo != "") {
			$xml = "<TWSRequest>
					<mobileNumber>".$smsTo."</mobileNumber>
					<textToSend><![CDATA[".$smsText."]]></textToSend>
				</TWSRequest> ";

			$stream_options = array(
				'http' => array(
				   'method'  => 'POST',
				   'header'  => "Content-type:text/xml",
				   'content' => $xml,
				)
			);

			$context  = stream_context_create($stream_options);
			$response = file_get_contents($url, null, $context);

		}     
	}
	header( "HTTP/1.1 200 OK" );



