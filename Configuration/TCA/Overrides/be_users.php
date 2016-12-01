<?php
defined('TYPO3_MODE') or die('Access denied.');

call_user_func(function ($extension, $table) {
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('my_user_management')) {
        $groupTca = $GLOBALS['TCA'][$table]['columns']['usergroup'];

        $column = [
            'label' => $groupTca['label'],
            'config' => [
                'type' => $groupTca['config']['type'],
                'renderType' => $groupTca['config']['renderType'],
                'size' => $groupTca['config']['size'],
                'maxitems' => $groupTca['config']['maxitems'],
                'enableMultiSelectFilterTextfield' => $groupTca['config']['enableMultiSelectFilterTextfield'],
                'items' => [],
            ],
            'exclude' => $groupTca['exclude'],
        ];

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
            $table,
            [
                \Serfhos\MyUserManagementGroups\FormDataProvider\UserGroupTcaSelectItems::FAKE_TCA_COLUMN => $column
            ]
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
            $table,
            \Serfhos\MyUserManagementGroups\FormDataProvider\UserGroupTcaSelectItems::FAKE_TCA_COLUMN,
            '',
            'after:usergroup'
        );

        // Make sure usergroup is not visible anymore..
        foreach ($GLOBALS['TCA'][$table]['types'] as &$config) {
            $items = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $config['showitem']);
            $config['showitem'] = \TYPO3\CMS\Core\Utility\GeneralUtility::rmFromList('usergroup', implode(',', $items));
        }
    }
}, 'my_user_management_groups', 'be_users');
