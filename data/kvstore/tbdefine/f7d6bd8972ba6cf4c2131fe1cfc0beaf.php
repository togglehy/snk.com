<?php exit(); ?>a:3:{s:5:"value";a:7:{s:7:"columns";a:16:{s:7:"rule_id";a:6:{s:4:"type";s:6:"int(8)";s:8:"required";b:1;s:4:"pkey";b:1;s:5:"label";s:8:"规则id";s:5:"extra";s:14:"auto_increment";s:8:"realtype";s:6:"int(8)";}s:4:"name";a:10:{s:4:"type";s:12:"varchar(255)";s:8:"required";b:1;s:7:"default";s:0:"";s:5:"label";s:12:"规则名称";s:8:"editable";b:1;s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:13:"filterdefault";b:1;s:8:"is_title";b:1;s:8:"realtype";s:12:"varchar(255)";}s:11:"description";a:6:{s:4:"type";s:4:"text";s:5:"label";s:12:"规则描述";s:8:"required";b:0;s:7:"in_list";b:1;s:13:"filterdefault";b:1;s:8:"realtype";s:4:"text";}s:11:"create_time";a:7:{s:4:"type";s:4:"time";s:5:"label";s:12:"修改时间";s:8:"editable";b:1;s:7:"in_list";b:1;s:15:"default_in_list";b:0;s:13:"filterdefault";b:1;s:8:"realtype";s:16:"int(10) unsigned";}s:9:"from_time";a:8:{s:4:"type";s:4:"time";s:5:"label";s:12:"起始时间";s:7:"default";i:0;s:8:"editable";b:1;s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:13:"filterdefault";b:1;s:8:"realtype";s:16:"int(10) unsigned";}s:7:"to_time";a:8:{s:4:"type";s:4:"time";s:5:"label";s:12:"截止时间";s:7:"default";i:0;s:8:"editable";b:1;s:7:"in_list";b:1;s:15:"default_in_list";b:0;s:13:"filterdefault";b:1;s:8:"realtype";s:16:"int(10) unsigned";}s:13:"member_lv_ids";a:5:{s:4:"type";s:12:"varchar(255)";s:7:"default";s:0:"";s:8:"required";b:0;s:5:"label";s:18:"会员级别集合";s:8:"realtype";s:12:"varchar(255)";}s:6:"status";a:8:{s:4:"type";s:4:"bool";s:7:"default";s:5:"false";s:8:"required";b:1;s:5:"label";s:12:"是否启用";s:7:"in_list";b:1;s:13:"filterdefault";b:1;s:15:"default_in_list";b:1;s:8:"realtype";s:20:"enum('true','false')";}s:10:"conditions";a:4:{s:4:"type";s:9:"serialize";s:8:"required";b:1;s:5:"label";s:12:"规则条件";s:8:"realtype";s:8:"longtext";}s:21:"stop_rules_processing";a:9:{s:4:"type";s:4:"bool";s:7:"default";s:5:"false";s:8:"required";b:1;s:5:"label";s:12:"是否排它";s:7:"in_list";b:1;s:8:"editable";b:1;s:13:"filterdefault";b:1;s:15:"default_in_list";b:1;s:8:"realtype";s:20:"enum('true','false')";}s:10:"sort_order";a:8:{s:4:"type";s:16:"int(10) unsigned";s:7:"default";s:1:"0";s:8:"required";b:1;s:5:"label";s:9:"优先级";s:7:"in_list";b:1;s:8:"editable";b:1;s:15:"default_in_list";b:1;s:8:"realtype";s:16:"int(10) unsigned";}s:15:"action_solution";a:4:{s:4:"type";s:9:"serialize";s:8:"required";b:1;s:5:"label";s:12:"动作方案";s:8:"realtype";s:8:"longtext";}s:13:"free_shipping";a:4:{s:4:"type";s:19:"tinyint(1) unsigned";s:7:"default";s:1:"0";s:5:"label";s:9:"免运费";s:8:"realtype";s:19:"tinyint(1) unsigned";}s:10:"c_template";a:3:{s:4:"type";s:12:"varchar(100)";s:5:"label";s:18:"过滤条件模板";s:8:"realtype";s:12:"varchar(100)";}s:10:"s_template";a:3:{s:4:"type";s:12:"varchar(100)";s:5:"label";s:18:"优惠方案模板";s:8:"realtype";s:12:"varchar(100)";}s:10:"apply_time";a:3:{s:4:"type";s:4:"time";s:5:"label";s:15:"预过滤时间";s:8:"realtype";s:16:"int(10) unsigned";}}s:7:"comment";s:18:"商品促销规则";s:8:"idColumn";s:7:"rule_id";s:5:"pkeys";a:1:{s:7:"rule_id";s:7:"rule_id";}s:10:"textColumn";s:4:"name";s:7:"in_list";a:8:{i:0;s:4:"name";i:1;s:11:"description";i:2;s:11:"create_time";i:3;s:9:"from_time";i:4;s:7:"to_time";i:5;s:6:"status";i:6;s:21:"stop_rules_processing";i:7;s:10:"sort_order";}s:15:"default_in_list";a:5:{i:0;s:4:"name";i:1;s:9:"from_time";i:2;s:6:"status";i:3;s:21:"stop_rules_processing";i:4;s:10:"sort_order";}}s:3:"ttl";i:0;s:8:"dateline";i:1441697520;}