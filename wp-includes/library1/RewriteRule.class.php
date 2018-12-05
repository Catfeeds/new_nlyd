<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace library;

/**
 * Autoload.
 */
class RewriteRule
{
    public $model = 'student';
    public $project = 'nlyd-student';

    public function __construct()
    {
        //配置自己的重写规则
        add_action( 'init', array($this,'theme_functionality_urls'),10,0);

        //配置自己的重写参数
        add_action( 'init', array($this,'custom_rewrite_tag'),10,0);

        //配置自己的重写模版
        add_action('template_redirect', array($this,'custom_rewrite_template'));
    }

    /**
     * 添加URL重定向规则
     */
    public function theme_functionality_urls() {
        //add_rewrite_rule('(.?.+?)\/(.*)$','index.php?controller=$matches[1]&action=$matches[2]','top');
        add_rewrite_rule('(.?.+?)\/(.?.+?)(\/.*)?$','index.php?pagename=$matches[1]&action=$matches[2]&tag=$matches[3]','top');
    }

    /**
     * 添加URL重定向参数
     */
    public function custom_rewrite_tag(){
        add_rewrite_tag('%pagename%', '([^&]+)');
        add_rewrite_tag('%action%', '([^&]+)');
        add_rewrite_tag('%tag%', '([^&]+)');
    }

    /**
     * 添加模板载入规则
     */
    public function custom_rewrite_template(){

        global $wp_query;
        $query = $wp_query->query;
        //var_dump($query);
        if(empty($query['pagename']) ){
            return;
        }
        $action = empty($query['action']) ? 'index' : $query['action'];

        //定义model
        define('MODEL',$this->model);
        //定义控制器
        define('CONTROLLER',$query['pagename']);
        //$GLOBALS['controller'] = $query['pagename'];
        //定义方法
        //$GLOBALS['action'] = $query['action'];
        define('ACTION',$action);
        //项目默认路径
        define( 'leo_student_path', PLUGINS_PATH.$this->project.'/' );
        define( 'leo_student_url', plugins_url($this->project ) );
        define( 'leo_student_version','1.0' );

        define( 'student_css_url', leo_student_url.'/Public/css/' );
        define( 'student_js_url', leo_student_url.'/Public/js/' );
        define( 'student_view_path', leo_student_path.'View/' );

        if(!empty($query['tag'])){
            parseUrl(trim($query['tag'],'/'));
        }

        //引入模板
        $this->load_rewrite_template();

    }


    /**
     * 引入url重定向模板
     */
    public function load_rewrite_template(){

        $class_path = leo_student_path.'Controller/class-'.MODEL.'-'.CONTROLLER.'.php';
        include_once (leo_student_path.'Controller/class-student-home.php');
        if(is_file($class_path)){
            include_once ($class_path);
            $class = ucfirst(MODEL).'_'.ucfirst(CONTROLLER);
            //var_dump($class);die;
            if(class_exists($class)){
                //var_dump(ACTION);
                new $class(ACTION);
            }
        }

    }


}
new RewriteRule();