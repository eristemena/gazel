<?php

/**
 * Class to store and retrieve the version of GAZEL.
 *
 * @category   Gazel
 * @package    Gazel_Version
 */
final class Gazel_Version
{
    /**
     * GAZEL version identification - see compareVersion()
     */
    const VERSION = '3.1.1';

    /**
     * The latest stable version GAZEL available
     *
     * @var string
     */
    protected static $_lastestVersion;

    /**
     * Compare the specified GAZEL version string $version
     * with the current Gazel_Version::VERSION of GAZEL.
     *
     * @param  string  $version  A version string (e.g. "0.7.1").
     * @return int           -1 if the $version is older,
     *                           0 if they are the same,
     *                           and +1 if $version is newer.
     *
     */
    public static function compareVersion($version)
    {
        $version = strtolower($version);
        $version = preg_replace('/(\d)pr(\d?)/', '$1a$2', $version);
        return version_compare($version, strtolower(self::VERSION));
    }

    /**
     * Fetches the version of the latest stable release
     *
     * TODO: must be implemented in the future
     *
     * @return string
     */
    /*
    public static function getLatest()
    {
        if (null === self::$_lastestVersion) {
            self::$_lastestVersion = 'not available';

            $handle = fopen('http://framework.zend.com/api/zf-version', 'r');
            if (false !== $handle) {
                self::$_lastestVersion = stream_get_contents($handle);
                fclose($handle);
            }
        }

        return self::$_lastestVersion;
    }
    */
}
