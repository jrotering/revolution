<?php
/**
 * Gets a list of users in a usergroup
 *
 * @param boolean $combo (optional) If true, will append a (anonymous) row
 * @param integer $start (optional) The record to start at. Defaults to 0.
 * @param integer $limit (optional) The number of records to limit to. Defaults
 * to 10.
 * @param string $sort (optional) The column to sort by. Defaults to name.
 * @param string $dir (optional) The direction of the sort. Defaults to ASC.
 *
 * @package modx
 * @subpackage processors.security.group
 */
class modUserGroupUserGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'modUser';
    public $defaultSortField = 'username';
    public $permission = 'access_permissions';
    public $languageTopics = array('user');

    public function initialize() {
        $initialized = parent::initialize();
        $this->setDefaultProperties(array(
            'usergroup' => false,
            'username' => '',
        ));
        return $initialized;
    }

    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $c->innerJoin('modUserGroupMember','UserGroupMembers');
        $c->innerJoin('modUserGroup','UserGroup','UserGroupMembers.user_group = UserGroup.id');
        $c->leftJoin('modUserGroupRole','UserGroupRole','UserGroupMembers.role = UserGroupRole.id');

        $userGroup = $this->getProperty('usergroup',0);
        $c->where(array(
            'UserGroupMembers.user_group' => $userGroup,
        ));

        $username = $this->getProperty('username','');
        if (!empty($username)) {
            $c->where(array(
                'modUser.username:LIKE' => '%'.$username.'%',
            ));
        }
        return $c;
    }

    public function prepareQueryAfterCount(xPDOQuery $c) {
        $c->select($this->modx->getSelectColumns('modUser','modUser'));
        $c->select(array(
            'usergroup' => 'UserGroup.id',
            'usergroup_name' => 'UserGroup.name',
            'role' => 'UserGroupRole.id',
            'role_name' => 'UserGroupRole.name',
            'authority' => 'UserGroupRole.authority',
        ));
        $c->sortby('authority','ASC');
        return $c;
    }
}
return 'modUserGroupUserGetListProcessor';