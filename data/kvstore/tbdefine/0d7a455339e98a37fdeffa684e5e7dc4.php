<?php exit(); ?>a:3:{s:5:"value";a:9:{s:7:"columns";a:13:{s:10:"comment_id";a:6:{s:4:"type";s:10:"bigint(17)";s:8:"required";b:1;s:4:"pkey";b:1;s:5:"label";s:2:"ID";s:7:"comment";s:2:"ID";s:8:"realtype";s:10:"bigint(17)";}s:12:"comment_type";a:5:{s:4:"type";a:2:{s:7:"comment";s:6:"评价";s:7:"consult";s:6:"咨询";}s:5:"label";s:6:"类型";s:7:"default";s:7:"comment";s:8:"required";b:1;s:8:"realtype";s:25:"enum('comment','consult')";}s:14:"for_comment_id";a:4:{s:4:"type";s:10:"bigint(17)";s:5:"label";s:9:"回复给";s:7:"default";i:0;s:8:"realtype";s:10:"bigint(17)";}s:8:"goods_id";a:5:{s:4:"type";s:11:"table:goods";s:5:"label";s:12:"相关商品";s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:8:"realtype";s:19:"bigint(20) unsigned";}s:10:"product_id";a:4:{s:4:"type";s:14:"table:products";s:5:"label";s:18:"相关规格货品";s:7:"default";i:0;s:8:"realtype";s:21:"mediumint(8) unsigned";}s:8:"order_id";a:8:{s:4:"type";s:12:"table:orders";s:5:"label";s:12:"相关订单";s:10:"searchtype";s:3:"has";s:10:"filtertype";s:6:"normal";s:13:"filterdefault";s:4:"true";s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:8:"realtype";s:19:"bigint(20) unsigned";}s:9:"member_id";a:5:{s:4:"type";s:12:"mediumint(8)";s:7:"in_list";b:0;s:5:"label";s:12:"相关会员";s:7:"default";i:0;s:8:"realtype";s:12:"mediumint(8)";}s:11:"author_name";a:8:{s:4:"type";s:12:"varchar(100)";s:5:"label";s:9:"发表人";s:10:"searchtype";s:3:"has";s:10:"filtertype";s:6:"normal";s:13:"filterdefault";s:4:"true";s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:8:"realtype";s:12:"varchar(100)";}s:10:"createtime";a:6:{s:4:"type";s:4:"time";s:7:"in_list";b:1;s:10:"filtertype";s:6:"normal";s:13:"filterdefault";s:4:"true";s:5:"label";s:12:"创建时间";s:8:"realtype";s:16:"int(10) unsigned";}s:9:"lastreply";a:7:{s:4:"type";s:4:"time";s:5:"label";s:18:"最后回复时间";s:10:"filtertype";s:6:"normal";s:13:"filterdefault";s:4:"true";s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:8:"realtype";s:16:"int(10) unsigned";}s:5:"title";a:5:{s:4:"type";s:12:"varchar(255)";s:5:"label";s:6:"标题";s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:8:"realtype";s:12:"varchar(255)";}s:7:"content";a:6:{s:4:"type";s:8:"longtext";s:5:"label";s:6:"内容";s:10:"searchtype";s:3:"has";s:10:"filtertype";s:6:"normal";s:13:"filterdefault";s:4:"true";s:8:"realtype";s:8:"longtext";}s:7:"display";a:7:{s:4:"type";s:4:"bool";s:7:"in_list";b:1;s:5:"label";s:12:"是否公开";s:10:"filtertype";s:4:"bool";s:7:"default";s:5:"false";s:15:"default_in_list";b:1;s:8:"realtype";s:20:"enum('true','false')";}}s:6:"engine";s:6:"innodb";s:7:"comment";s:16:"咨询\评价表";s:5:"index";a:1:{s:20:"index_for_comment_id";a:1:{s:7:"columns";a:1:{i:0;s:14:"for_comment_id";}}}s:8:"idColumn";s:10:"comment_id";s:5:"pkeys";a:1:{s:10:"comment_id";s:10:"comment_id";}s:7:"in_list";a:7:{i:0;s:8:"goods_id";i:1;s:8:"order_id";i:2;s:11:"author_name";i:3;s:10:"createtime";i:4;s:9:"lastreply";i:5;s:5:"title";i:6;s:7:"display";}s:15:"default_in_list";a:6:{i:0;s:8:"goods_id";i:1;s:8:"order_id";i:2;s:11:"author_name";i:3;s:9:"lastreply";i:4;s:5:"title";i:5;s:7:"display";}s:10:"textColumn";s:12:"comment_type";}s:3:"ttl";i:0;s:8:"dateline";i:1441007286;}