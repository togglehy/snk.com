<?php

// +----------------------------------------------------------------------
// | VMCSHOP [V M-Commerce Shop]
// +----------------------------------------------------------------------
// | Copyright (c) vmcshop.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.vmcshop.com/licensed)
// +----------------------------------------------------------------------
// | Author: Shanghai ChenShang Software Technology Co., Ltd.
// +----------------------------------------------------------------------


class b2c_member_exp
{

    public function __construct($app)
    {
        $this->app = $app;
        $this->mdl_members = app::get('b2c')->model('members');
    }
    /**
     * 经验值变更
     */
    public function renew($member_id)
    {
        return $this->mdl_members->exp_renew($member_id) && $this->mdl_members->touch_lv($member_id);
    }

    /**
     * 经验值变更添加,更新会员等级.
     *
     * @param $member_id 会员ID
     * @param $experience 新增经验值
     * @param $msg 错误信息
     */
    public function upgrade($member_id, &$experience, &$msg = '')
    {
        $experience = abs(intval($experience));
        $member = $this->mdl_members->dump($member_id);
        if (!$member) {
            $msg = '参数错误!';
        }
        $exp = ($experience + $member['experience']);
        if ($exp < 0) {
            $exp = 0;
        }
        $experience = $member['experience'] = $exp;

        return $this->mdl_members->save($member) && $this->mdl_members->touch_lv($member_id);
    }

    /**
     * 经验值变更减少,更新会员等级.
     *
     * @param $member_id 会员ID
     * @param $experience 新增经验值
     * @param $msg 错误信息
     */
    public function downgrade($member_id,&$experience,&$msg = ''){
        $experience = abs(intval($experience));
        $member = $this->mdl_members->dump($member_id);
        if (!$member) {
            $msg = '参数错误!';
        }
        $exp = ($member['experience'] - $experience);
        if ($exp < 0) {
            $exp = 0;
        }
        $experience = $member['experience'] = $exp;

        return $this->mdl_members->save($member) && $this->mdl_members->touch_lv($member_id);
    }
}
