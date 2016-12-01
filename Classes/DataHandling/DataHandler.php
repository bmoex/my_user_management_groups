<?php
namespace Serfhos\MyUserManagementGroups\DataHandling;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUser;
use Serfhos\MyUserManagementGroups\FormDataProvider\UserGroupTcaSelectItems;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Core DataHandling: DataHandler Hook
 *
 * @package Serfhos\MyUserManagementGroups\DataHandling
 */
class DataHandler
{

    /**
     * Secure given usergroups for backend user given by the extension my_user_management
     *
     * @param array $incomingFieldArray
     * @param string $table
     * @param integer $id
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     */
    public function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler)
    {
        if ($table === BackendUser::TABLE) {
            //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($dataHandler, 'processDatamap_preProcessFieldArray');
            //exit;
            if (in_array(UserGroupTcaSelectItems::FAKE_TCA_COLUMN, array_keys($incomingFieldArray))) {
                // Get original groups from database
                $originalGroups = $dataHandler->recordInfo($table, $id, 'usergroup');
                $originalGroups = GeneralUtility::intExplode(',', $originalGroups['usergroup']);

                // Explode but keep order..
                $incomingGroups = GeneralUtility::intExplode(',', $incomingFieldArray[UserGroupTcaSelectItems::FAKE_TCA_COLUMN], true);
                unset ($incomingFieldArray[UserGroupTcaSelectItems::FAKE_TCA_COLUMN]);

                $incomingFieldArray['usergroup'] = $this->restoreActiveHiddenGroupsBeforeSave($incomingGroups, $originalGroups);
            }
        }
    }

    /**
     * Restore the active usergroups that are hidden for current backend user
     *
     * @param array $incomingGroups
     * @param array $originalGroups
     * @return string
     */
    protected function restoreActiveHiddenGroupsBeforeSave(array $incomingGroups, array $originalGroups)
    {
        $configuredGroups = BackendUserGroupPermission::configured();
        if ($this->getBackendUserAuthentication()->isAdmin() === false && !empty($configuredGroups)) {
            // Only allow configured groups in array
            $incomingGroups = array_intersect($incomingGroups, $configuredGroups);
            $groups = array_unique(array_merge($originalGroups, $incomingGroups));
        } else {
            $groups = $incomingGroups;
        }

        return implode(',', $groups);
    }

    /**
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUserAuthentication()
    {
        return $GLOBALS['BE_USER'];
    }
}
