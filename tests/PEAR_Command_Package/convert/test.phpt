--TEST--
convert command failure
--SKIPIF--
<?php
if (!getenv('PHP_PEAR_RUNTESTS')) {
    echo 'skip';
}
?>
--FILE--
<?php
error_reporting(E_ALL);
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$savedir = getcwd();
chdir($temp_path);
copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'packagefiles' . DIRECTORY_SEPARATOR . 'validv1.xml',
    $temp_path . DIRECTORY_SEPARATOR . 'package.xml');
copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'packagefiles' . DIRECTORY_SEPARATOR . 'validv1.xml',
    $temp_path . DIRECTORY_SEPARATOR . 'validv1.xml');
touch($temp_path . DIRECTORY_SEPARATOR . 'foo.php');
mkdir($temp_path . DIRECTORY_SEPARATOR . 'sunger');
touch($temp_path . DIRECTORY_SEPARATOR . 'sunger' . DIRECTORY_SEPARATOR . 'foo.dat');
$e = $command->run('convert', array(), array());
$phpunit->assertNoErrors('1');
$phpunit->assertEquals(array (
  0 => 
  array (
    'info' => 'Wrote new version 2.0 package.xml to ".' . DIRECTORY_SEPARATOR .
        'package2.xml"',
    'cmd' => 'no command',
  ),
), $fakelog->getLog(), 'log 1');

$pkg = &new PEAR_PackageFile($config);
$pf = &$pkg->fromPackageFile($temp_path . DIRECTORY_SEPARATOR . 'package2.xml', PEAR_VALIDATE_NORMAL);
$gen = &$pf->getDefaultGenerator();
$contents = implode('', file($temp_path . DIRECTORY_SEPARATOR . 'package2.xml'));
$phpunit->assertEquals('<?xml version="1.0"?>
<package packagerversion="' . $gen->getPackagerVersion() . '" version="2.0" xmlns="http://pear.php.net/dtd/package-2.0" xmlns:tasks="http://pear.php.net/dtd/tasks-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://pear.php.net/dtd/tasks-1.0
http://pear.php.net/dtd/tasks-1.0.xsd
http://pear.php.net/dtd/package-2.0
http://pear.php.net/dtd/package-2.0.xsd">
 <name>foo</name>
 <channel>pear.php.net</channel>
 <summary>foo</summary>
 <description>foo
hi there
 
 </description>
 <lead>
  <name>person</name>
  <user>single</user>
  <email>joe@example.com</email>
  <active>yes</active>
 </lead>
 <date>' . date('Y-m-d') . '</date>
 <time>' . $pf->getTime() . '</time>
 <version>
  <release>1.2.0a1</release>
  <api>1.2.0a1</api>
 </version>
 <stability>
  <release>alpha</release>
  <api>alpha</api>
 </stability>
 <license uri="http://www.php.net/license">PHP License</license>
 <notes>here are the
multi-line
release notes
  
 </notes>
 <contents>
  <dir name="/">
   <dir name="sunger">
    <file baseinstalldir="freeb" md5sum="8332264d2e0e3c3091ebd6d8cee5d3a3" name="foo.dat" role="data" />
   </dir> <!-- //sunger -->
   <file baseinstalldir="freeb" md5sum="8332264d2e0e3c3091ebd6d8cee5d3a3" name="foo.php" role="php">
    <tasks:replace from="@pv@" to="version" type="package-info" />
   </file>
  </dir> <!-- / -->
 </contents>
 <dependencies>
  <required>
   <php>
    <min>4.3.0</min>
    <max>6.0.0</max>
   </php>
   <pearinstaller>
    <min>1.4.0a1</min>
   </pearinstaller>
   <package>
    <name>Console_Getopt</name>
    <channel>pear.php.net</channel>
    <max>1.2</max>
    <exclude>1.2</exclude>
   </package>
  </required>
  <optional>
   <extension>
    <name>xmlrpc</name>
    <min>1.0</min>
   </extension>
  </optional>
 </dependencies>
 <phprelease>
  <installconditions>
   <os>
    <name>*</name>
   </os>
  </installconditions>
  <filelist>
   <install as="merbl.php" name="foo.php" />
  </filelist>
 </phprelease>
 <changelog>
  <release>
   <version>
    <release>1.3.3</release>
    <api>1.3.3</api>
   </version>
   <stability>
    <release>stable</release>
    <api>stable</api>
   </stability>
   <date>2004-10-28</date>
   <license uri="http://www.php.net/license">PHP License</license>
   <notes>Installer:
 * fix Bug #1186 raise a notice error on PEAR::Common $_packageName
 * fix Bug #1249 display the right state when using --force option
 * fix Bug #2189 upgrade-all stops if dependancy fails
 * fix Bug #1637 The use of interface causes warnings when packaging with PEAR
 * fix Bug #1420 Parser bug for T_DOUBLE_COLON
 * fix Request #2220 pear5 build fails on dual php4/php5 system
 * fix Bug #1163  pear makerpm fails with packages that supply role=&quot;doc&quot;

Other:
 * add PEAR_Exception class for PHP5 users
 * fix critical problem in package.xml for linux in 1.3.2
 * fix staticPopCallback() in PEAR_ErrorStack
 * fix warning in PEAR_Registry for windows 98 users
   
   </notes>
  </release>
  <release>
   <version>
    <release>1.3.2</release>
    <api>1.3.2</api>
   </version>
   <stability>
    <release>stable</release>
    <api>stable</api>
   </stability>
   <date>2004-10-28</date>
   <license uri="http://www.php.net/license">PHP License</license>
   <notes>Installer:
 * fix Bug #1186 raise a notice error on PEAR::Common $_packageName
 * fix Bug #1249 display the right state when using --force option
 * fix Bug #2189 upgrade-all stops if dependancy fails
 * fix Bug #1637 The use of interface causes warnings when packaging with PEAR
 * fix Bug #1420 Parser bug for T_DOUBLE_COLON
 * fix Request #2220 pear5 build fails on dual php4/php5 system
 * fix Bug #1163  pear makerpm fails with packages that supply role=&quot;doc&quot;

Other:
 * add PEAR_Exception class for PHP5 users
 * fix critical problem in package.xml for linux in 1.3.2
 * fix staticPopCallback() in PEAR_ErrorStack
 * fix warning in PEAR_Registry for windows 98 users
   
   </notes>
  </release>
 </changelog>
</package>
', $contents, 'contents 1');
$e = $command->run('convert', array('flat' => true), array());
$phpunit->assertNoErrors('1.1');
$phpunit->assertEquals(array (
  0 => 
  array (
    0 => 1,
    1 => 'Analyzing foo.php',
  ),
  1 => 
  array (
    'info' => 'Wrote new version 2.0 package.xml to ".' . DIRECTORY_SEPARATOR .
        'package2.xml"',
    'cmd' => 'no command',
  ),
), $fakelog->getLog(), 'log 1.1');

$pkg = &new PEAR_PackageFile($config);
$pf = &$pkg->fromPackageFile($temp_path . DIRECTORY_SEPARATOR . 'package2.xml', PEAR_VALIDATE_NORMAL);
$gen = &$pf->getDefaultGenerator();
$contents = implode('', file($temp_path . DIRECTORY_SEPARATOR . 'package2.xml'));
$phpunit->assertEquals('<?xml version="1.0"?>
<package packagerversion="' . $gen->getPackagerVersion() . '" version="2.0" xmlns="http://pear.php.net/dtd/package-2.0" xmlns:tasks="http://pear.php.net/dtd/tasks-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://pear.php.net/dtd/tasks-1.0
http://pear.php.net/dtd/tasks-1.0.xsd
http://pear.php.net/dtd/package-2.0
http://pear.php.net/dtd/package-2.0.xsd">
 <name>foo</name>
 <channel>pear.php.net</channel>
 <summary>foo</summary>
 <description>foo
hi there
 
 </description>
 <lead>
  <name>person</name>
  <user>single</user>
  <email>joe@example.com</email>
  <active>yes</active>
 </lead>
 <date>' . date('Y-m-d') . '</date>
 <time>' . $pf->getTime() . '</time>
 <version>
  <release>1.2.0a1</release>
  <api>1.2.0a1</api>
 </version>
 <stability>
  <release>alpha</release>
  <api>alpha</api>
 </stability>
 <license uri="http://www.php.net/license">PHP License</license>
 <notes>here are the
multi-line
release notes
  
 </notes>
 <contents>
  <dir name="/">
   <file baseinstalldir="freeb" md5sum="8332264d2e0e3c3091ebd6d8cee5d3a3" name="sunger/foo.dat" role="data" />
   <file baseinstalldir="freeb" md5sum="8332264d2e0e3c3091ebd6d8cee5d3a3" name="foo.php" role="php">
    <tasks:replace from="@pv@" to="version" type="package-info" />
   </file>
  </dir>
 </contents>
 <dependencies>
  <required>
   <php>
    <min>4.3.0</min>
    <max>6.0.0</max>
   </php>
   <pearinstaller>
    <min>1.4.0a1</min>
   </pearinstaller>
   <package>
    <name>Console_Getopt</name>
    <channel>pear.php.net</channel>
    <max>1.2</max>
    <exclude>1.2</exclude>
   </package>
  </required>
  <optional>
   <extension>
    <name>xmlrpc</name>
    <min>1.0</min>
   </extension>
  </optional>
 </dependencies>
 <phprelease>
  <installconditions>
   <os>
    <name>*</name>
   </os>
  </installconditions>
  <filelist>
   <install as="merbl.php" name="foo.php" />
  </filelist>
 </phprelease>
 <changelog>
  <release>
   <version>
    <release>1.3.3</release>
    <api>1.3.3</api>
   </version>
   <stability>
    <release>stable</release>
    <api>stable</api>
   </stability>
   <date>2004-10-28</date>
   <license uri="http://www.php.net/license">PHP License</license>
   <notes>Installer:
 * fix Bug #1186 raise a notice error on PEAR::Common $_packageName
 * fix Bug #1249 display the right state when using --force option
 * fix Bug #2189 upgrade-all stops if dependancy fails
 * fix Bug #1637 The use of interface causes warnings when packaging with PEAR
 * fix Bug #1420 Parser bug for T_DOUBLE_COLON
 * fix Request #2220 pear5 build fails on dual php4/php5 system
 * fix Bug #1163  pear makerpm fails with packages that supply role=&quot;doc&quot;

Other:
 * add PEAR_Exception class for PHP5 users
 * fix critical problem in package.xml for linux in 1.3.2
 * fix staticPopCallback() in PEAR_ErrorStack
 * fix warning in PEAR_Registry for windows 98 users
   
   </notes>
  </release>
  <release>
   <version>
    <release>1.3.2</release>
    <api>1.3.2</api>
   </version>
   <stability>
    <release>stable</release>
    <api>stable</api>
   </stability>
   <date>2004-10-28</date>
   <license uri="http://www.php.net/license">PHP License</license>
   <notes>Installer:
 * fix Bug #1186 raise a notice error on PEAR::Common $_packageName
 * fix Bug #1249 display the right state when using --force option
 * fix Bug #2189 upgrade-all stops if dependancy fails
 * fix Bug #1637 The use of interface causes warnings when packaging with PEAR
 * fix Bug #1420 Parser bug for T_DOUBLE_COLON
 * fix Request #2220 pear5 build fails on dual php4/php5 system
 * fix Bug #1163  pear makerpm fails with packages that supply role=&quot;doc&quot;

Other:
 * add PEAR_Exception class for PHP5 users
 * fix critical problem in package.xml for linux in 1.3.2
 * fix staticPopCallback() in PEAR_ErrorStack
 * fix warning in PEAR_Registry for windows 98 users
   
   </notes>
  </release>
 </changelog>
</package>
', $contents, 'contents 2');

$phpunit->assertIsa('PEAR_PackageFile_v2', $pf, 'test 1');
$phpunit->assertEquals('foo', $pf->getPackage(), 'package test 1');

$e = $command->run('convert', array(), array($temp_path . DIRECTORY_SEPARATOR . 'validv1.xml',
    $temp_path . DIRECTORY_SEPARATOR . 'package3.xml'));
$phpunit->assertNoErrors('2');
$phpunit->assertEquals(array (
  0 => 
  array (
    'info' => 'Wrote new version 2.0 package.xml to "' . $temp_path . DIRECTORY_SEPARATOR .
        'package3.xml"',
    'cmd' => 'no command',
  ),
), $fakelog->getLog(), 'log 2');
$pf2 = &$pkg->fromPackageFile($temp_path . DIRECTORY_SEPARATOR . 'package3.xml', PEAR_VALIDATE_NORMAL);
$phpunit->assertIsa('PEAR_PackageFile_v2', $pf2, 'test 2');
$phpunit->assertEquals('foo', $pf->getPackage(), 'package test 2');
chdir($savedir);
echo 'tests done';
?>
--EXPECT--
tests done
