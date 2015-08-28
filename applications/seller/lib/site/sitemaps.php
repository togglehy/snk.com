<?php
/**
* sitemaps生成类
*/
class seller_site_sitemaps {
    
	/**
	* 构造方法 实例化APP
	* @param object $app app实例
	*/
    public function __construct( $app ) {
        $this->app = $app;
    }
    
    /*
     * 返回map
     * @return array
     * array(
     *  array(
     *      'url' => '...........'
     *      ),
     *  array(
     *      'url' => '...........'
     *      )
     * )
     */
    
    public function get_arr_maps() {
        $this->router = app::get('site')->router();
        $this->_get( 'article_indexs','site_article','index','article_id',$tmp );
        return $tmp;
    }    
}