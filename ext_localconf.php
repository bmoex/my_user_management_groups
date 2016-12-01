<?php
defined('TYPO3_MODE') or die('Access denied.');

call_user_func(function ($extension) {
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('my_user_management')) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\Serfhos\MyUserManagementGroups\FormDataProvider\UserGroupTcaSelectItems::class] = [];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\TYPO3\CMS\Backend\Form\FormDataProvider\TcaSelectItems::class]['depends'][] = \Serfhos\MyUserManagementGroups\FormDataProvider\UserGroupTcaSelectItems::class;
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$extension] = \Serfhos\MyUserManagementGroups\DataHandling\DataHandler::class;
    }
}, $_EXTKEY);
