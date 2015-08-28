<?php

class b2c_mdl_goods_mark extends dbeav_model
{
    public function __construct($app)
    {
        parent::__construct($app);
    }

    public function avg_mark($gids)
    {
        $sql = 'SELECT avg(mark_star) as num,goods_id FROM '.$this->table_name(1).' WHERE  goods_id in ('.implode(',', (array)$gids).') group by goods_id';
        $res = $this->db->select($sql);
        if (!$res || empty($res) || count($res) < 1) {
            return $res;
        }

        return utils::array_change_key($res, 'goods_id');
    }
}
