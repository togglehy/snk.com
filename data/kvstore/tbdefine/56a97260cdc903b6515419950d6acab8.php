<?php exit(); ?>a:3:{s:5:"value";a:10:{s:7:"columns";a:15:{s:7:"menu_id";a:5:{s:4:"type";s:6:"number";s:4:"pkey";b:1;s:5:"extra";s:14:"auto_increment";s:7:"comment";s:14:"后台菜单ID";s:8:"realtype";s:21:"mediumint(8) unsigned";}s:9:"menu_type";a:7:{s:4:"type";s:11:"varchar(80)";s:8:"required";b:1;s:5:"width";i:100;s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:7:"comment";s:12:"菜单类型";s:8:"realtype";s:11:"varchar(80)";}s:6:"app_id";a:7:{s:4:"type";s:15:"table:apps@base";s:8:"required";b:1;s:5:"width";i:100;s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:7:"comment";s:19:"所属app(应用)ID";s:8:"realtype";s:11:"varchar(32)";}s:10:"workground";a:3:{s:4:"type";s:12:"varchar(200)";s:7:"comment";s:12:"顶级菜单";s:8:"realtype";s:12:"varchar(200)";}s:10:"menu_group";a:3:{s:4:"type";s:12:"varchar(200)";s:7:"comment";s:9:"菜单组";s:8:"realtype";s:12:"varchar(200)";}s:10:"menu_title";a:4:{s:4:"type";s:12:"varchar(100)";s:8:"is_title";b:1;s:7:"comment";s:12:"菜单标题";s:8:"realtype";s:12:"varchar(100)";}s:9:"menu_path";a:3:{s:4:"type";s:12:"varchar(255)";s:7:"comment";s:30:"菜单对应执行的url路径";s:8:"realtype";s:12:"varchar(255)";}s:8:"disabled";a:3:{s:4:"type";s:4:"bool";s:7:"default";s:5:"false";s:8:"realtype";s:20:"enum('true','false')";}s:7:"display";a:4:{s:4:"type";s:21:"enum('true', 'false')";s:7:"default";s:5:"false";s:7:"comment";s:12:"是否显示";s:8:"realtype";s:21:"enum('true', 'false')";}s:10:"permission";a:3:{s:4:"type";s:11:"varchar(80)";s:7:"comment";s:25:"权限,有效显示范围";s:8:"realtype";s:11:"varchar(80)";}s:5:"addon";a:3:{s:4:"type";s:4:"text";s:7:"comment";s:12:"额外信息";s:8:"realtype";s:4:"text";}s:4:"icon";a:3:{s:4:"type";s:12:"varchar(100)";s:7:"comment";s:30:"WORKGROUND菜单ICON classname";s:8:"realtype";s:12:"varchar(100)";}s:6:"target";a:4:{s:4:"type";s:11:"varchar(10)";s:7:"default";s:0:"";s:7:"comment";s:6:"跳转";s:8:"realtype";s:11:"varchar(10)";}s:10:"menu_order";a:4:{s:4:"type";s:6:"number";s:7:"default";s:1:"0";s:7:"comment";s:6:"排序";s:8:"realtype";s:21:"mediumint(8) unsigned";}s:6:"parent";a:4:{s:4:"type";s:12:"varchar(255)";s:7:"default";s:1:"0";s:7:"comment";s:9:"父节点";s:8:"realtype";s:12:"varchar(255)";}}s:5:"index";a:3:{s:13:"ind_menu_type";a:1:{s:7:"columns";a:1:{i:0;s:9:"menu_type";}}s:13:"ind_menu_path";a:1:{s:7:"columns";a:1:{i:0;s:9:"menu_path";}}s:14:"ind_menu_order";a:1:{s:7:"columns";a:1:{i:0;s:10:"menu_order";}}}s:7:"version";s:13:"$Rev: 44008 $";s:8:"unbackup";b:1;s:7:"comment";s:15:"后台菜单表";s:8:"idColumn";s:7:"menu_id";s:5:"pkeys";a:1:{s:7:"menu_id";s:7:"menu_id";}s:7:"in_list";a:2:{i:0;s:9:"menu_type";i:1;s:6:"app_id";}s:15:"default_in_list";a:2:{i:0;s:9:"menu_type";i:1;s:6:"app_id";}s:10:"textColumn";s:10:"menu_title";}s:3:"ttl";i:0;s:8:"dateline";i:1441697516;}