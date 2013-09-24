<?php
/**
 * QRCode_AllTests
 * 
 * @category  Images
 * @package   Image_QRCode
 * @author    Rich Sage <rich.sage@gmail.com> 
 * @copyright 2009 Rich Sage
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1 
 * @link      http://code.google.com/p/pearqrcode
 */

if ($fp = @fopen('PHPUnit/Autoload.php', 'r', true)) {
    require_once 'PHPUnit/Autoload.php';
} elseif ($fp = @fopen('PHPUnit/Framework.php', 'r', true)) {
    require_once 'PHPUnit/Framework.php';
} else {
    die('skip could not find PHPUnit');
}
fclose($fp);

// Keep tests from running twice when calling this file directly via PHPUnit.
$call_main = false;
if (strpos($_SERVER['argv'][0], 'phpunit') === false) {
    // Called via php, not PHPUnit.  Pass the request to PHPUnit.
    if (!defined('PHPUnit_MAIN_METHOD')) {
        define('PHPUnit_MAIN_METHOD', 'QRCode_AllTests::main');
        $call_main = true;
    }
}

require_once 'Image/QRCode.php';
require_once 'Image/QRCodeTest.php';

/**
 * All tests for Image_QRCode
 * 
 * @category  Images
 * @package   Image_QRCode
 * @author    Rich Sage <rich.sage@gmail.com> 
 * @copyright 2009 Rich Sage
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @link      http://code.google.com/p/pearqrcode
 */
class QRCode_AllTests extends PHPUnit_Framework_TestSuite
{
    public static function main()
    {
        if (!function_exists('phpunit_autoload')) {
            require_once 'PHPUnit/TextUI/TestRunner.php';
        }
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * suite 
     * 
     * @return PHPUnit_Framework_TestSuite
     */
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Image_QRCode Unit Test Suite');
        $suite->addTestSuite('Image_QRCodeTest');
        return $suite;
    }
}

if ($call_main) {
    QRCode_AllTests::main();
}
