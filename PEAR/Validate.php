<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 5                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Gregory Beaver <cellog@php.net>                             |
// +----------------------------------------------------------------------+
//
// $Id$
define('PEAR_VALIDATE_INSTALLING', 1);
define('PEAR_VALIDATE_NORMAL', 3);
define('PEAR_VALIDATE_PACKAGING', 7);
class PEAR_Validate
{
    var $packageregex = _PEAR_COMMON_PACKAGE_NAME_PREG;
    /**
     * @var PEAR_PackageFile
     */
    var $_packagexml;
    /**
     * @var int one of the PEAR_VALIDATE_* constants
     */
    var $_state = PEAR_VALIDATE_NORMAL;
    /** 
     * Valid release states
     * @var array
     * @access private
     */
    var $_validStates = array('alpha','beta','stable','snapshot','devel');
    /**
     * Format: ('error' => array('field' => name, 'reason' => reason), 'warning' => same)
     * @var array
     * @access private
     */
    var $_failures = array('error' => array(), 'warning' => array());

    function getChannelPackageDownloadRegex()
    {
        return '(' . _PEAR_CHANNELS_NAME_PREG . ')::' . $this->getPackageDownloadRegex();
    }

    function getPackageDownloadRegex()
    {
        return '(' . $this->packageregex . ')(-([.0-9a-zA-Z]+))?';
    }

    function validPackageName($name)
    {
        return (bool)preg_match('/^' . $this->packageregex . '$/', $name);
    }

    function setPackageFile(&$pf)
    {
        $this->_packagexml = &$pf;
    }

    function _addFailure($field, $reason)
    {
        $this->_failures['error'] = array('field' => $field, 'reason' => $reason);
    }

    function _addWarning($field, $reason)
    {
        $this->_failures['warning'] = array('field' => $field, 'reason' => $reason);
    }

    /**
     * @param int one of the PEAR_VALIDATE_* constants
     */
    function validate($state = null)
    {
        if (!isset($this->_packagexml)) {
            return false;
        }
        if ($state !== null) {
            $this->_state = $state;
        }
        $this->validatePackageName();
        $this->validateVersion();
        $this->validateMaintainers();
        $this->validateDate();
        if ($this->_packagexml->getPackagexmlVersion() == '1.0') {
            $this->validateState();
        } elseif ($this->_packagexml->getPackagexmlVersion() == '2.0') {
            $this->validateTime();
            $this->validateStability();
        }
    }

    function validatePackageName()
    {
        if ($this->_state == PEAR_VALIDATE_PACKAGING ||
              $this->_state == PEAR_VALIDATE_NORMAL) {
            if ($this->_packagexml->getExtends()) {
                $vlen = strlen($version = $this->_packagexml->getVersion() . '');
                $name = $this->_packagexml->getPackage();
                if ($name{strlen($name) - $vlen} != $version) {
                    $this->_addFailure('name', "package $name extends package " .
                        $this->_packagexml->getExtends() . ' and so the name must ' .
                        'have a postfix equal to the major version like "' . $name .
                        $version . '"');
                    return false;
                }
            }
        }
        if (!$this->validPackageName($this->_packagexml->getPackage())) {
            $this->_addFailure('name', 'package name ' .
                $this->_packagexml->getPackage() . ' is invalid');
            return false;
        }
    }

    function validateVersion()
    {
        if ($this->_state != PEAR_VALIDATE_PACKAGING) {
            return true;
        }
        $version = $this->_packagexml->getVersion();
        $versioncomponents = explode('.', $version);
        if (count($versioncomponents) != 3) {
            $this->_addFailure('version',
                'Must have 3 decimals (x.y.z) in a version number');
            return false;
        }
        $name = $this->_packagexml->getPackage();
        // version must be based upon state
        switch ($this->_packagexml->getState()) {
            case 'snapshot' :
            case 'devel' :
                if ($versioncomponents[0] == '0') {
                    return true;
                }
                return false;
            break;
            case 'alpha' :
            case 'beta' :
            // check for a package that extends a package,
            // like Foo and Foo2
            if ($this->_packagexml->getExtends()) {
                if ($versioncomponents[0] == '1') {
                    if ($versioncomponents[2]{0} == '0') {
                        if (strlen($versioncomponents[2]) > 1) {
                            // version 1.*.0RC1 or 1.*.0beta24 etc.
                            return true;
                        } else {
                            // version 1.*.0
                            $this->_addFailure('version',
                                'version 1.' . $versioncomponents[1] .
                                    '.0 cannot be alpha or beta');
                            return false;
                        }
                    } else {
                        $this->_addFailure('version',
                            'bugfix versions (1.3.x where x > 0) cannot be alpha or beta');
                        return false;
                    }
                } else {
                    $this->_addFailure('version',
                        'major versions greater than 1 are not allowed for packages ' .
                        'not containing an identical postfix');
                }
            } else {
                $vlen = strlen($versioncomponents[0] . '');
                if ($name{strlen($name) - $vlen} != $versioncomponents[0]) {
                    $this->_addFailure('version', 'first version number "' .
                        $versioncomponents[0] . '" must match the postfix of ' .
                        'package name "' . $name . '" (' .
                        $name{strlen($name) - $vlen} . ')');
                    return false;
                }
                if ($versioncomponents[2]{0} == '0') {
                    if (strlen($versioncomponents[2]) > 1) {
                        // version 2.*.0RC1 etc.
                        return true;
                    } else {
                        // version 2.*.0
                        $this->_addFailure('version', 'version ' .
                            $versioncomponents[0] . '.0.0 cannot be alpha or beta');
                        return false;
                    }
                }
            }
            case 'stable' :
                if ($versioncomponents[0] == '0') {
                    $this->_addFailure('version', 'versions less than 1.0 cannot ' .
                    'be stable');
                    return false;
                }
                if (!is_numeric($versioncomponents[2])) {
                    if (preg_match('/\n+(rc|a|alpha||b|beta|)\n*/i',
                          $versioncomponents[2])) {
                        $this->_addFailure('version', 'RC/beta/alpha cannot be stable');
                        return false;
                    }
                }
                return true;
            break;
            default :
                return false;
            break;
        }
    }

    function validateMaintainers()
    {
        // maintainers can only be truly validated server-side for most channels
        // but allow this customization for those who wish it
        return true;
    }

    function validateDate()
    {
        // packager automatically sets date, so only validate if
        // pear validate is called
        if ($this->_state = PEAR_VALIDATE_NORMAL) {
            if (!preg_match('/\n\n\n\n\-\n\n\-\n\n/',
                  $this->_packagexml->getDate())) {
                $this->_addFailure('date', 'invalid release date "' .
                    $this->_packagexml->getDate());
                return false;
            }
            if (strtotime($this->_packagexml->getDate()) == -1) {
                $this->_addFailure('date', 'invalid release date "' .
                    $this->_packagexml->getDate());
                return false;
            }
        }
        return true;
    }

    function validateTime()
    {
        if (!$this->_packagexml->getTime()) {
            // default of no time value set
            return true;
        }
        // packager automatically sets time, so only validate if
        // pear validate is called
        if ($this->_state = PEAR_VALIDATE_NORMAL) {
            if (!preg_match('/\n\n:\n\n:\n\n/',
                  $this->_packagexml->getDate())) {
                $this->_addFailure('time', 'invalid release time "' .
                    $this->_packagexml->getDate());
                return false;
            }
            if (strtotime($this->_packagexml->getDate()) == -1) {
                $this->_addFailure('time', 'invalid release time "' .
                    $this->_packagexml->getDate());
                return false;
            }
        }
        return true;
    }

    function validateState()
    {
        if (!in_array($this->_packagexml->getState(), $this->_validStates)) {
            $this->_addFailure('state', 'release state "' .
                $this->_packagexml->getState() . '" is not valid, must be one of: ' .
                implode(', ', $this->_validStates));
            return false;
        }
        return true;
    }

    function validateStability()
    {
        $ret = true;
        $packagestability = $this->_packagexml->getState();
        $apistability = $this->_packagexml->getState('api');
        if (!in_array($packagestability, $this->_validStates)) {
            $this->_addFailure('state', 'package stability "' .
                $this->_packagexml->getState() . '" is not valid, must be one of: ' .
                implode(', ', $this->_validStates));
            $ret = false;
        }
        if (!in_array($apistability, $this->_validStates)) {
            $this->_addFailure('state', 'API stability "' .
                $this->_packagexml->getState() . '" is not valid, must be one of: ' .
                implode(', ', $this->_validStates));
            $ret = false;
        }
        return $ret;
    }
}
?>