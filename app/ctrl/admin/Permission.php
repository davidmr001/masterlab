<?php

namespace main\app\ctrl\admin;

use main\app\ctrl\BaseAdminCtrl;
use main\app\model\permission\PermissionModel;
use main\app\model\permission\PermissionDefaultRoleModel;
use main\app\model\permission\PermissionDefaultRoleRelationModel;

/**
 * 系统角色权限控制器
 */
class Permission extends BaseAdminCtrl
{

    public function role()
    {
        $data = [];
        $data['title'] = 'System';
        $data['nav_links_active'] = 'system';
        $data['sub_nav_active'] = 'security';
        $data['left_nav_active'] = 'permission';
        $this->render('gitlab/admin/permission_role.php', $data);
    }

    /**
     * 返回所有角色列表
     * @throws \ReflectionException
     */
    public function roleFetch()
    {
        $model = new PermissionDefaultRoleModel();
        $roles = $model->getsAll();

        unset($model);

        $data = [];
        $data['roles'] = $roles;
        $this->ajaxSuccess('ok', $data);
    }

    /**
     * 获取单个角色信息
     * @param $id
     * @throws \ReflectionException
     */
    public function roleGet($id)
    {
        $id = (int)$id;
        $model = new PermissionDefaultRoleModel();
        $group = $model->getById($id);

        unset($model);
        $this->ajaxSuccess('ok', (object)$group);
    }

    /**
     * 角色编辑
     * @param $roleId
     * @param $permissionIds
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function roleEdit($roleId, $permissionIds)
    {
        if (empty($roleId) || empty($permissionIds)) {
            $this->ajaxFailed(' param_is_empty ', [], 600);
        }

        $permissionRoleRelation = new PermissionDefaultRoleRelationModel();

        $rows = $permissionRoleRelation->getPermIdsByRoleId($roleId);

        if (!empty($rows)) {
            $permissionRoleRelation->deleteByRoleId($roleId);
        }

        $permIdsList = explode(',', $permissionIds);
        $permIdsList = array_filter($permIdsList);

        if (!is_array($permIdsList)) {
            $this->ajaxFailed(' param_is_empty ', [], 600);
        }

        foreach ($permIdsList as $v) {
            $permissionRoleRelation->add($roleId, $v);
        }

        unset($permissionRoleRelation);
        $this->ajaxSuccess('ok', []);
    }

    /**
     * 获取角色树形关系
     * @param $roleId
     */
    public function roleTree($roleId)
    {
        $permissionModel = new PermissionModel();
        $permissionRoleRelationModel = new PermissionDefaultRoleRelationModel();

        $parentList = $permissionModel->getParent();
        $childrenList = $permissionModel->getChildren();
        $permIdList = $permissionRoleRelationModel->getPermIdsByRoleId($roleId);

        unset($permissionModel);
        unset($permissionRoleRelationModel);

        //组装数据
        $data = [];
        $i = 0;
        foreach ($parentList as $p) {
            $data[$i]['id'] = $p['id'];
            $data[$i]['text'] = $p['name'];
            $data[$i]['state'] = in_array($p['id'], $permIdList) ? ['selected' => true] : ['selected' => false];

            $data[$i]['children'] = [];
            $j = 0;
            foreach ($childrenList as $k => $c) {
                if ($c['parent_id'] == $p['id']) {
                    $data[$i]['children'][$j]['id'] = $k;
                    $data[$i]['children'][$j]['text'] = $c['name'];
                    $data[$i]['children'][$j]['state'] = in_array($k, $permIdList) ? ['selected' => true] : ['selected' => false];
                    $j++;
                }
            }
            $i++;
        }
        @header('Content-Type:application/json');
        echo json_encode($data);
        exit;
    }
}
