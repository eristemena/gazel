<?php

class Gazel_File_Info
{
	protected $filename;
	protected $ext;
	protected $mime;
	protected $type;
	
	public function __construct($filepath)
	{
		$this->filename=basename($filepath);
	}
	
	public function getMime()
	{
		return $this->mime;
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function getExt()
	{
		return $this->ext;
	}
	
	public function getInfo()
	{
		$ext=strtolower(substr($this->filename, strrpos($this->filename, '.') + 1));
		
		switch ( $ext )
		{
			case 'zip':
				$mime = 'application/zip';
				$type = 'file';
				break;
			
			case 'pdf':
				$mime = 'application/pdf';
				$type = 'file';
				break;
			
			case 'jpg':
			case 'jpeg':
				$mime = 'image/jpeg';
				$type = 'image';
				break;
			
			case 'mp3':
				$mime = 'audio/mpeg3';
				$type = 'audio';
				break;
			
			case 'wav':
				$mime = 'audio/wav';
				$type = 'audio';
				break;
			
			case 'flv':
				$mime = 'video/x-flv';
				$type = 'video';
				break;
			
			case 'avi':
				$mime = 'video/avi';
				$type = 'video';
				break;
			
			case 'doc':
			case 'docx':
				$mime = 'application/msword';
				$type = 'file';
				break;
			
			case 'ppt':
				$mime = 'application/mspowerpoint';
				$type = 'file';
				break;
			
			case 'pdf':
				$mime = 'application/pdf';
				$type = 'file';
				break;
			
			default:
				$mime = 'application/force-download';
				$type = 'file';
				break;
		}
		
		$info=array(
			'name'	=> $this->filename,
			'ext'		=> $ext,
			'type'	=> $type,
			'mime'	=> $mime
		);
		
		return $info;
	}
}