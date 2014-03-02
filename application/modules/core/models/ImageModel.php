<?php

require_once 'Gazel/Model.php';

class Core_Model_Image extends Gazel_Model
{
	public function resize($file, $width, $height)
	{
		if (!file_exists($file) || !is_file($file)) {
			return;
		}
		
		$info = pathinfo($file);
		$extension = $info['extension'];
		
		$old_image = $file;
		$new_image = str_replace('user/', 'cache/', $info['dirname']) . '/' . substr(basename($file), 0, strrpos(basename($file), '.')) . '-' . $width . 'x' . $height . '.' . $extension;
		
		$new_image = explode('cache/', $new_image);
		
		$cache_dir = $this->_config->cachedir;
		
		if (!file_exists($cache_dir . '/' . $new_image[1]) || (filemtime($old_image) > filemtime($cache_dir . '/' . $new_image[1]))) {
			$path = '';
			
			$directories = explode('/', dirname(str_replace('../', '', $new_image[1])));
			
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
			
				if (!file_exists($cache_dir . $path)) {
					mkdir($cache_dir . $path, 0777);
				}
			}
			
			require_once 'Gazel/Image.php';
			
			$image = new Gazel_Image($old_image);
			$image->resize($width, $height);
			$image->save($cache_dir . '/' . $new_image[1]);
		}
		
		$image_url = $this->_config->baseurl . '/data/cache/' . $new_image[1];

		return $image_url;
	}
}