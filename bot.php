<?php
	
	//add this file url in below address for add webhook
	//https://api.telegram.org/bot[TOKEN_NAME]/setWebhook?url=https://domain.com/bot.php
	
	include 'lib/telegram.php';
	$bot = new Telegram('access token');
	$data = $bot->ReadData(false); // read enter text via user in telegram
	
	if ($data == '/start')
		$bot->SendMessage('show menu\r\n show photo \photo\r\n show location \location \r\nexit \exit \r\n and other command');
	else if ($data == 'photo')
		$bot->SendPhoto('caption photo','iranapp.org');
	else if ($data == 'location')
		$bot->SendPhoto('32.232323','43.343434');
	else if ($data == 'exit')
		$bot->SendMessage('\start');
	else if ($bot->IsFile() == true) { // receive file from user if file is audio
		$res = $bot->Download('files/temp.mp3');
		$bot->SendMessage("result download file is $res");
	}
	
	exit;
	
	
?>