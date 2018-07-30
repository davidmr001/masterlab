<?php

namespace main\app\model\project;

use main\app\model\CacheModel;

/**
 * 项目拥有的角色 模型
 */
class ProjectRoleModel extends CacheModel
{
    public $prefix = 'project_';

    public $table = 'role';

    const   DATA_KEY = 'project_role/';

    /**
     * ProjectRoleModel constructor.
     * @param string $uid
     * @param bool $persistent
     */
    public function __construct($uid = '', $persistent = false)
    {
        parent::__construct($uid, $persistent);
        $this->uid = $uid;
    }

    /**
     * 获取某个角色信息
     * @param $id
     * @return array
     */
    public function getById($id)
    {
        return $this->getRowById($id);
    }

    /**
     * 返回所有角色信息
     * @return array
     */
    public function getsAll()
    {
        return $this->getRows('*');
    }

    /**
     * 获取某个项目的所有角色
     * @return array
     */
    public function getsByProject($projectId)
    {
        return $this->getRows('*', ['project_id' => $projectId]);
    }
}