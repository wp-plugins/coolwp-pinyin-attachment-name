<?php
/*
Plugin Name: CoolWP Pinyin Attachment Name(附件名中文改拼音)
Plugin URI: http://suoling.net/coolwp-pinyin-attachment-name/
Description:这是一个将用户上传附件的附件名中的中文改为拼音的WordPress插件。<a href="http://suoling.net/coolwp-pinyin-attachment-name/" title="CoolWP Pinyin Attachment Name(附件名中文改拼音)">点击这里了解更多</a>。
Version: 1.1
Author: suifengtec
Author URI: http://suoling.net/
License: GPLv3
*/
/*
这是一个将上传附件的简体中文名称改为拼音名称的Wordpress插件。
字典函数来自Xiaole Tao的插件Pinyin Permalinks,与之兼容。
*/
defined('ABSPATH') or exit;


final class WP_Pinyin_Attachment_Name{

	protected static $_instance = null;
    public $support;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }	  
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.1' );
    }

    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.1' );
    }  

    public function __construct() {

    	add_filter('sanitize_file_name', array( $this, 'modify_file_name' ), 5, 1 );
    }

    /**
     * modify_file_name
     * @param  string $filename the file name.
     * @return string the modifed file name.
     */
    public function modify_file_name( $filename ){ 

    		$length = apply_filters( 'wp_attachment_pinyin_name_length', 100 );

			$parts = explode('.', $filename);
			$filename = array_shift($parts);
			$extension = array_pop($parts);
			foreach ( (array) $parts as $part)
				$filename .= '.' . $part;
	
			$filename = $this->pinyin($filename,100);	
			$filename .= '.' . $extension;

			return $filename;


    }
    /**
     * Change a string to Chinese pinyin string,if it contains any Simplified Chinese character.
     * @param  [type]  $str    [description]
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    public function pinyin($str,$length=100){ 

    		$str = urldecode($str);
			$parts = explode('.', $str);
			$str = array_shift($parts);
			
    		if(!preg_match('/[一-龥]/u', $str))
    			return $str;
			$exists = $this->exist('pinyin_permalink');
			if(!$exists){
				require_once('dict.php');
				$str = coolwp_getPinyinPermalink($str,$length);
				return $str;
			}else{
				$str = apply_filters( 'wp_attachment_pinyin_name', getPinyinPermalink($str));
				return $str;
			}

				
    }

    /**
     * Does the support plugin exist?
     * @param  string $str the slug of the support plugin.
     * @return boolean       
     */
    public function exist( $str='' ){ 

    		$str = !empty($str)?trim($str):'pinyin_permalink';
    		if(empty($str)) return false;
    		$support_plugin = apply_filters( 'wp_attachment_pinyin_name_support', array('pinyin_permalink'=>'pinyin-permalink/pinyin-permalink.php'));
    		$active_plugins = apply_filters( 'active_plugins', get_option('active_plugins' ) );
    		if( in_array( $support_plugin[$str], $active_plugins ) ) 
				return true;
    		return  false;


    }

}/*//CLASS*/

global $WP_Pinyin_Attachment_Name;
$WP_Pinyin_Attachment_Name = WP_Pinyin_Attachment_Name::instance();