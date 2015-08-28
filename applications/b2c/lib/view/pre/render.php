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


class b2c_view_pre_render
{
    public function pre_display(&$content)
    {
        $content = $this->member_avatar($content);
    }//End Function

    private function member_avatar($content)
    {
        $blocks = preg_split('/%AVATAR_([0-9a-z]*)_S_([a-z0-9\:]*)_AVATAR%/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        $c = count($blocks);
        $block_lib = array();
        $block = array();
        for ($i = 0;$i < $c;$i++) {
            switch ($i % 3) {
                case 1:
                    $mid = $blocks[$i];
                    $block[$mid][$i] = array(
                        $blocks[$i + 1],
                    );
                    $blocks[$i] = &$block[$mid][$i][0];
                break;
                case 2:
                    $block[$mid][$i - 1][1] = $blocks[$i];
                    $blocks[$i] = '';
                break;
            }
        }
        if ($block) {
            $db = vmc::database();
            foreach ($db->select('select avatar,member_id from vmc_b2c_members where member_id in(\''.implode("','", array_keys($block)).'\')') as $r) {
                $block_lib[$r['member_id']] = $r;
            }
            foreach ($block as $mid => $sizes) {
                foreach ($sizes as $i => $item) {
                    $block[$mid][$i][0] = '%IMG_'.$block_lib[$mid]['avatar'].'_S_'.$item[0].'_IMG%';
                }
            }
        }

        return implode('', $blocks);
    }
}
