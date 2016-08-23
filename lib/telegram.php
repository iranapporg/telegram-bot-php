<?php

 class Telegram {
	
		var $channel_id 	= '@';
		var $bot			= null;
		var $data			= null;
		private $token		= '';
		private $is_file	= false;
		private $file_type	= '';
		private $file_name	= '';
		
		function __construct($token) {
			
			require_once "telegram-bot-api.php";
			$this->token	= $token;
			$this->bot		= new telegram_bot($token);
		}
		
		function ReadData($object = false) {
			
			$res = $this->bot->read_post_message();
			$this->data	=	$res;
			
			$this->channel_id = $res->message->chat->id;
			
			if (isset($this->data->message->document)) {
				$this->is_file		= true;
				$this->file_type	= 'document';
				$this->file_name	= $this->data->message->document->file_name;
			}
			else if (isset($this->data->message->photo)){
				$this->is_file		= true;
				$this->file_type	= 'photo';
				$this->file_name	= $this->data->message->photo->file_name;
			}
			else if (isset($this->data->message->voice)){
				$this->is_file		= true;
				$this->file_type	= 'voice';
				$this->file_name	= $this->data->message->voice->file_name;
			}
			else if (isset($this->data->message->video)){
				$this->is_file		= true;
				$this->file_type	= 'video';
				$this->file_name	= $this->data->message->video->file_name;
			}
			else if (isset($this->data->message->file)){
				$this->is_file		= true;
				$this->file_type	= 'file';
				$this->file_name	= $this->data->message->file->file_name;
			}
			else if (isset($this->data->message->audio)){
				$this->is_file		= true;
				$this->file_type	= 'audio';
				$this->file_name	= $this->data->message->audio->file_name;
			}
			
			if ($object == false)
				return $res->message->text;
			else
				return $res;
			
		}
		
		function AddHook($url) {
			$this->bot->set_webhook($url);	
		}
		
		function GetStatus() {
			return $this->bot->status();
		}
		
		//information about chat
		
		function GetChatDate() {
			return $res->message->date;
		}
		
		function GetChatID() {
			return $this->data->message->chat->id;
		}
		
		function GetUsername() {
			return $this->data->message->chat->username;
		}
		
		function GetFirstname() {
			return $this->data->message->chat->first_name;
		}
		
		public function Location() {
			return $this->data->message->location;
		}

		public function UpdateID() {
			return $this->data->update_id;
		}
		
		///////////////////////////////////////////////////////////////
		
		function SendMessage($message,$keyboard = null) {
			
			if ($keyboard != null) {
				$key = json_encode(array('keyboard' => $keyboard,'resize_keyboard' => true,'one_time_keyboard' => true));
				$rs = $this->bot->send_message($this->channel_id , $message , null, $key);
			}
			else
				$rs = $this->bot->send_message($this->channel_id , $message , null, null);
			
			return $rs;
			
		}
		
		function SendPhoto($caption,$filepath,$keyboard = null) {
			
			if ($keyboard != null) {
				$key = json_encode(array('keyboard' => $keyboard,'resize_keyboard' => true,'one_time_keyboard' => true));
				$rs = $this->bot->send_photo($this->channel_id,new CURLFile(realpath($filepath)),$caption,null,$key);
			}
			else
				$rs = $this->bot->send_photo($this->channel_id,new CURLFile(realpath($filepath)),$caption);
				
			return $rs;
			
		}
		
		function SendVideo($filepath,$keyboard = null) {
			
			if ($keyboard != null) {
				$key = json_encode(array('keyboard' => $keyboard,'resize_keyboard' => true,'one_time_keyboard' => true));
				$rs = $this->bot->send_video($this->channel_id,new CURLFile(realpath($filepath)),null,$key);
			}
			else
				$rs = $this->bot->send_video($this->channel_id,new CURLFile(realpath($filepath)));
			return $rs;
			
		}
		
		function SendAudio($filepath,$keyboard = null) {
			
			if ($keyboard != null) {
				$key = json_encode(array('keyboard' => $keyboard,'resize_keyboard' => true,'one_time_keyboard' => true));
				$rs = $this->bot->send_audio($this->channel_id,new CURLFile(realpath($filepath)),null,$key);
			}
			else
				$rs = $this->bot->send_audio($this->channel_id,new CURLFile(realpath($filepath)));
				
			return $rs;
			
		}
		
		function SendSticker($filepath,$keyboard = null) {
			
			if ($keyboard != null) {
				$key = json_encode(array('keyboard' => $keyboard,'resize_keyboard' => true,'one_time_keyboard' => true));
				$rs = $this->bot->send_sticker($this->channel_id,new CURLFile(realpath($filepath)),null,$key);
			}
			else
				$rs = $this->bot->send_sticker($this->channel_id,new CURLFile(realpath($filepath)));
			
			return $rs;
			
		}
		
		function SendLocation($lat,$lon,$keyboard = null) {
			
			if ($keyboard != null) {
				$key = json_encode(array('keyboard' => $keyboard,'resize_keyboard' => true,'one_time_keyboard' => true));
				$rs = $this->bot->send_location($this->channel_id,$lat,$lon,null,$key);
			}
			else
				$rs = $this->bot->send_location($this->channel_id,$lat,$lon);
				
			return $rs;
			
		}
		
		function IsFile() {
			return $this->is_file;	
		}
		
		function GetFileType() {
			return $this->file_type;
		}
		
		function Download($local) {
			
			$file_id	= '';
			$local		=	$local.$this->file_name;
			
			if (isset($this->data->message->document))
				$file_id = $this->data->message->document->file_id;
			else if (isset($this->data->message->photo))
				$file_id = $this->data->message->photo->file_id;
			else if (isset($this->data->message->voice))
				$file_id = $this->data->message->voice->file_id;
			else if (isset($this->data->message->video))
				$file_id = $this->data->message->video->file_id;
			else if (isset($this->data->message->file))
				$file_id = $this->data->message->file->file_id;
			else if (isset($this->data->message->audio))
				$file_id = $this->data->message->audio->file_id;
			
			if ($file_id == '') return "error valid";

			$remote = $this->bot->get_file_path($file_id);
			
			$file_url = "https://api.telegram.org/file/bot" . $this->token . "/" . $remote->result->file_path;
			$in = fopen($file_url, "rb");
			$out = fopen($local, "wb");

			while ($chunk = fread($in, 8192)) {
				fwrite($out, $chunk, 8192);
			}
			fclose($in);
			fclose($out);
			
			return true;
			
		}
}