<?php exit(); ?>a:3:{s:5:"value";a:8:{s:7:"columns";a:15:{s:10:"request_id";a:9:{s:4:"type";s:10:"bigint(20)";s:8:"required";b:1;s:4:"pkey";b:1;s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:10:"searchtype";s:3:"has";s:10:"filtertype";s:3:"yes";s:5:"label";s:12:"售后编号";s:8:"realtype";s:10:"bigint(20)";}s:8:"order_id";a:9:{s:4:"type";s:16:"table:orders@b2c";s:7:"default";i:0;s:8:"required";b:1;s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:10:"searchtype";s:3:"has";s:10:"filtertype";s:3:"yes";s:5:"label";s:9:"订单号";s:8:"realtype";s:19:"bigint(20) unsigned";}s:9:"member_id";a:7:{s:4:"type";s:17:"table:members@b2c";s:7:"default";s:1:"0";s:8:"required";b:1;s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:5:"label";s:9:"申请人";s:8:"realtype";s:21:"mediumint(8) unsigned";}s:11:"delivery_id";a:4:{s:4:"type";s:18:"table:delivery@b2c";s:7:"default";s:1:"0";s:5:"label";s:9:"退货单";s:8:"realtype";s:19:"bigint(20) unsigned";}s:7:"bill_id";a:4:{s:4:"type";s:19:"table:bills@ectools";s:7:"default";s:1:"0";s:5:"label";s:9:"退款单";s:8:"realtype";s:19:"bigint(20) unsigned";}s:14:"member_addr_id";a:3:{s:4:"type";s:22:"table:member_addrs@b2c";s:7:"comment";s:18:"会员收货地址";s:8:"realtype";s:7:"int(10)";}s:7:"subject";a:8:{s:4:"type";s:12:"varchar(200)";s:8:"required";b:1;s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:10:"searchtype";s:3:"has";s:10:"filtertype";s:3:"yes";s:5:"label";s:24:"售后服务请求标题";s:8:"realtype";s:12:"varchar(200)";}s:11:"description";a:6:{s:4:"type";s:8:"longtext";s:8:"required";b:1;s:10:"searchtype";s:3:"has";s:10:"filtertype";s:3:"yes";s:5:"label";s:12:"问题描述";s:8:"realtype";s:8:"longtext";}s:7:"remarks";a:3:{s:4:"type";s:8:"longtext";s:5:"label";s:15:"管理员备注";s:8:"realtype";s:8:"longtext";}s:7:"product";a:3:{s:4:"type";s:9:"serialize";s:5:"label";s:21:"需售后服务货品";s:8:"realtype";s:8:"longtext";}s:8:"req_type";a:9:{s:4:"type";a:4:{i:1;s:6:"退货";i:2;s:6:"更换";i:3;s:6:"维修";i:4;s:12:"投诉建议";}s:7:"default";s:1:"1";s:8:"required";b:1;s:7:"comment";s:18:"售后服务类型";s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:7:"orderby";b:1;s:5:"label";s:18:"售后服务类型";s:8:"realtype";s:21:"enum('1','2','3','4')";}s:13:"delivery_type";a:5:{s:4:"type";a:3:{i:0;s:1:"-";i:1;s:15:"快递至商家";i:2;s:24:"商家安排上门取件";}s:7:"default";s:1:"0";s:7:"comment";s:18:"商品返回方式";s:5:"label";s:18:"商品返回方式";s:8:"realtype";s:17:"enum('0','1','2')";}s:6:"status";a:9:{s:4:"type";a:5:{i:1;s:9:"申请中";i:2;s:12:"接受申请";i:3;s:15:"申请被拒绝";i:4;s:9:"处理中";i:5;s:12:"处理完成";}s:7:"default";s:1:"1";s:8:"required";b:1;s:7:"comment";s:18:"售后申请状态";s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:7:"orderby";b:1;s:5:"label";s:18:"售后申请状态";s:8:"realtype";s:25:"enum('1','2','3','4','5')";}s:10:"createtime";a:5:{s:4:"type";s:4:"time";s:5:"label";s:12:"创建时间";s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:8:"realtype";s:16:"int(10) unsigned";}s:11:"last_modify";a:6:{s:4:"type";s:11:"last_modify";s:5:"label";s:12:"更新时间";s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:7:"orderby";b:1;s:8:"realtype";s:16:"int(10) unsigned";}}s:6:"engine";s:6:"innodb";s:7:"comment";s:12:"售后申请";s:8:"idColumn";s:10:"request_id";s:7:"in_list";a:8:{i:0;s:10:"request_id";i:1;s:8:"order_id";i:2;s:9:"member_id";i:3;s:7:"subject";i:4;s:8:"req_type";i:5;s:6:"status";i:6;s:10:"createtime";i:7;s:11:"last_modify";}s:15:"default_in_list";a:8:{i:0;s:10:"request_id";i:1;s:8:"order_id";i:2;s:9:"member_id";i:3;s:7:"subject";i:4;s:8:"req_type";i:5;s:6:"status";i:6;s:10:"createtime";i:7;s:11:"last_modify";}s:5:"pkeys";a:1:{s:10:"request_id";s:10:"request_id";}s:10:"textColumn";s:8:"order_id";}s:3:"ttl";i:0;s:8:"dateline";i:1441697516;}