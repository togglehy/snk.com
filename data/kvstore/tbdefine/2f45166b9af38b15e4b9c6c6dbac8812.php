<?php exit(); ?>a:3:{s:5:"value";a:9:{s:7:"columns";a:18:{s:10:"product_id";a:6:{s:4:"type";s:6:"number";s:8:"required";b:1;s:4:"pkey";b:1;s:5:"extra";s:14:"auto_increment";s:5:"label";s:8:"货品ID";s:8:"realtype";s:21:"mediumint(8) unsigned";}s:8:"goods_id";a:5:{s:4:"type";s:11:"table:goods";s:7:"default";i:0;s:8:"required";b:1;s:5:"label";s:8:"商品ID";s:8:"realtype";s:19:"bigint(20) unsigned";}s:7:"barcode";a:4:{s:4:"type";s:12:"varchar(128)";s:5:"label";s:6:"条码";s:7:"in_list";b:1;s:8:"realtype";s:12:"varchar(128)";}s:2:"bn";a:5:{s:4:"type";s:11:"varchar(30)";s:5:"label";s:6:"货号";s:10:"filtertype";s:6:"normal";s:7:"in_list";b:1;s:8:"realtype";s:11:"varchar(30)";}s:5:"price";a:7:{s:4:"type";s:5:"money";s:7:"default";s:1:"0";s:8:"required";b:1;s:5:"label";s:12:"销售价格";s:10:"filtertype";s:6:"number";s:7:"in_list";b:1;s:8:"realtype";s:13:"decimal(20,3)";}s:8:"mktprice";a:5:{s:4:"type";s:5:"money";s:5:"label";s:9:"市场价";s:10:"filtertype";s:6:"number";s:7:"in_list";b:1;s:8:"realtype";s:13:"decimal(20,3)";}s:4:"name";a:10:{s:4:"type";s:12:"varchar(200)";s:8:"required";b:1;s:7:"default";s:0:"";s:5:"label";s:12:"货品名称";s:10:"searchtype";s:3:"has";s:10:"filtertype";s:6:"custom";s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:8:"is_title";b:1;s:8:"realtype";s:12:"varchar(200)";}s:6:"weight";a:6:{s:4:"type";s:13:"decimal(20,3)";s:5:"label";s:12:"单位重量";s:10:"filtertype";s:6:"number";s:7:"default";i:0;s:7:"in_list";b:1;s:8:"realtype";s:13:"decimal(20,3)";}s:4:"unit";a:6:{s:4:"type";s:11:"varchar(20)";s:5:"label";s:6:"单位";s:10:"filtertype";s:6:"normal";s:7:"default";s:3:"件";s:7:"in_list";b:1;s:8:"realtype";s:11:"varchar(20)";}s:9:"spec_info";a:7:{s:4:"type";s:4:"text";s:5:"label";s:6:"规格";s:10:"filtertype";s:6:"normal";s:7:"in_list";b:1;s:15:"default_in_list";b:1;s:10:"searchtype";s:3:"has";s:8:"realtype";s:4:"text";}s:9:"spec_desc";a:4:{s:4:"type";s:9:"serialize";s:5:"label";s:19:"规格值,序列化";s:7:"in_list";b:1;s:8:"realtype";s:8:"longtext";}s:10:"is_default";a:4:{s:4:"type";s:4:"bool";s:8:"required";b:1;s:7:"default";s:5:"false";s:8:"realtype";s:20:"enum('true','false')";}s:8:"image_id";a:3:{s:4:"type";s:11:"varchar(32)";s:5:"label";s:11:"相册图ID";s:8:"realtype";s:11:"varchar(32)";}s:6:"uptime";a:6:{s:4:"type";s:4:"time";s:10:"depend_col";s:19:"marketable:true:now";s:5:"label";s:12:"上架时间";s:7:"in_list";b:1;s:7:"orderby";b:1;s:8:"realtype";s:16:"int(10) unsigned";}s:8:"downtime";a:6:{s:4:"type";s:4:"time";s:10:"depend_col";s:20:"marketable:false:now";s:5:"label";s:12:"下架时间";s:7:"in_list";b:1;s:7:"orderby";b:1;s:8:"realtype";s:16:"int(10) unsigned";}s:11:"last_modify";a:4:{s:4:"type";s:11:"last_modify";s:5:"label";s:18:"最后修改时间";s:7:"in_list";b:1;s:8:"realtype";s:16:"int(10) unsigned";}s:8:"disabled";a:3:{s:4:"type";s:4:"bool";s:7:"default";s:5:"false";s:8:"realtype";s:20:"enum('true','false')";}s:10:"marketable";a:7:{s:4:"type";s:4:"bool";s:7:"default";s:4:"true";s:8:"required";b:1;s:5:"label";s:6:"上架";s:10:"filtertype";s:3:"yes";s:7:"in_list";b:1;s:8:"realtype";s:20:"enum('true','false')";}}s:7:"comment";s:15:"商品货品表";s:5:"index";a:4:{s:12:"ind_goods_id";a:1:{s:7:"columns";a:1:{i:0;s:8:"goods_id";}}s:12:"ind_disabled";a:1:{s:7:"columns";a:1:{i:0;s:8:"disabled";}}s:11:"ind_barcode";a:1:{s:7:"columns";a:1:{i:0;s:7:"barcode";}}s:6:"ind_bn";a:2:{s:7:"columns";a:1:{i:0;s:2:"bn";}s:6:"prefix";s:6:"UNIQUE";}}s:6:"engine";s:6:"innodb";s:8:"idColumn";s:10:"product_id";s:5:"pkeys";a:1:{s:10:"product_id";s:10:"product_id";}s:7:"in_list";a:13:{i:0;s:7:"barcode";i:1;s:2:"bn";i:2;s:5:"price";i:3;s:8:"mktprice";i:4;s:4:"name";i:5;s:6:"weight";i:6;s:4:"unit";i:7;s:9:"spec_info";i:8;s:9:"spec_desc";i:9;s:6:"uptime";i:10;s:8:"downtime";i:11;s:11:"last_modify";i:12;s:10:"marketable";}s:10:"textColumn";s:4:"name";s:15:"default_in_list";a:2:{i:0;s:4:"name";i:1;s:9:"spec_info";}}s:3:"ttl";i:0;s:8:"dateline";i:1441697519;}