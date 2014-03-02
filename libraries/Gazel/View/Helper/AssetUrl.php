<?php

require_once "Gazel/View/Helper/Abstract.php";

/**
 * Helper for making easy links and getting urls that depend on the routes and router
 *
 */
class Gazel_View_Helper_AssetUrl extends Gazel_View_Helper_Abstract
{
    /**
     * @params string $moduleName module name
     * @params string $path relative path from /{moduleName}/views/assets
     */
    public function assetUrl($path, $moduleName = null)
    {
        if( !$moduleName )
        {
            $moduleName = $this->_getRequest()->getParam('module');
        }

        $fullPath = $this->_getConfig()->applicationdir.'/modules/'.$moduleName.'/views/assets/'.$path;

        if( !file_exists($fullPath) ){
            return null;
        }

        $cacheAssetDir = $this->_getConfig()->cachedir.'/assets/'.$moduleName.'/'.dirname($path);
        if( !file_exists($cacheAssetDir) )
        {
            mkdir($cacheAssetDir, 0755, true);
        }

        $cacheFullPath = $cacheAssetDir.'/'.basename($path);
        $cacheUrl = $this->_getConfig()->baseurl.'/data/cache/assets/'.$moduleName.'/'.$path;
        //echo $cacheFullPath;exit;

        $content = file_get_contents($fullPath);
        file_put_contents($cacheFullPath, $content);

        return $cacheUrl;

        //echo $this->_getConfig()->cachedir;exit;
        //echo $fullPath;exit;
        //return $this->_getConfig()->baseurl.'/application/modules/'.
    }
}
