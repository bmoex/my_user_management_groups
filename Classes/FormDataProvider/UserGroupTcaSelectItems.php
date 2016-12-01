<?php
namespace Serfhos\MyUserManagementGroups\FormDataProvider;

use KoninklijkeCollective\MyUserManagement\Domain\DataTransferObject\BackendUserGroupPermission;
use KoninklijkeCollective\MyUserManagement\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Resolve select items, set processed item list in processedTca, sanitize and resolve database field
 *
 * @package Serfhos\MyUserManagementGroups\FormDataProvider
 */
class UserGroupTcaSelectItems implements \TYPO3\CMS\Backend\Form\FormDataProviderInterface
{

    /**
     * Given name to match fake TCA
     */
    const FAKE_TCA_COLUMN = 'my_user_management_groups';

    /**
     * Add form data to result array
     *
     * @param array $result Initialized result array
     * @return array Result filled with more data
     */
    public function addData(array $result)
    {
        // Only fill data when tca is configured
        if (
            in_array(self::FAKE_TCA_COLUMN, $result['columnsToProcess'])
            && isset($result['processedTca']['columns'][self::FAKE_TCA_COLUMN])
            && empty($result['processedTca']['columns'][self::FAKE_TCA_COLUMN]['config']['items'])
        ) {
            $allowedGroups = $this->getAllowedItems();
            $activeGroups = GeneralUtility::intExplode(',', $result['databaseRow']['usergroup'], true);
            $allowedActive = array_intersect($activeGroups, array_column($allowedGroups, 'uid'));

            $result['processedTca']['columns'][self::FAKE_TCA_COLUMN]['config']['items'] = $this->mapGroupsToTcaItems($allowedGroups);
            $result['databaseRow'][self::FAKE_TCA_COLUMN] = implode(',', $allowedActive);
        }
        return $result;
    }

    /**
     * Map groups as TCA items
     *
     * @param array $groups
     * @return array
     */
    public function mapGroupsToTcaItems(array $groups)
    {
        $items = [];
        foreach ($groups as $item) {
            $items[] = [
                $item['title'],
                $item['uid'],
            ];
        }

        return $items;
    }

    /**
     * Get all allowed groups for current backend user
     *
     * @return array
     */
    protected function getAllowedItems()
    {
        $configured = BackendUserGroupPermission::configured();
        $where = '';
        if (!empty($configured)) {
            $where = 'AND uid in (' . implode(',', $configured) . ')';
        }
        $items = $this->queryGroups($where);
        return $items;
    }

    /**
     * Query backend user groups
     *
     * @param string $andWhere
     * @return array
     */
    protected function queryGroups($andWhere)
    {
        return $this->getDatabaseConnection()->exec_SELECTgetRows(
            '*',
            BackendUserGroup::TABLE,
            'hide_in_lists = 0 '
            . $andWhere
            . BackendUtility::BEenableFields(BackendUserGroup::TABLE)
            . BackendUtility::deleteClause(BackendUserGroup::TABLE),
            '',
            'title ASC'
        );
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
