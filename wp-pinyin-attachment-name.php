<?php
/*
Plugin Name: WP Pinyin Attachment Name(附件名中文改拼音)
Plugin URI: http://suoling.net/coolwp-pinyin-attachment-name/
Description:这是一个将用户上传附件的附件名中的中文改为拼音的Wordpress插件。<a href="http://suoling.net/coolwp-pinyin-attachment-name/" title="Coolwp Pinyin Attachment Name(附件名中文改拼音)">点击这里了解更多</a>。
Version: 1.0
Author: Suifengtec
Author URI: http://suoling.net/
License: GPLv3
*/
/*
这是一个将上传附件的简体中文名称改为拼音名称的Wordpress插件。
*/

// 防止直接载入此文件
defined( 'ABSPATH' ) || exit;

/*
判断Xiaole Tao的插件Pinyin Permalinks是否已启用，因为本插件的字典函数用的是Pinyin Permalinks这个插件的字典函数
*/
function pinyin_permalink_exist(){
	//初始化所有全局变量,其实不初始化也没关系,这里是防止某些古董php版本register_globals(PHP4.2.0开始默认值从 on 改为 off 了， PHP 5.3.0 起废弃并将自 PHP 5.4.0 起移除,虽然没有主机商傻到用PHP4.2.0之前的PHP版本，但为安全考虑，还是来那么一小下吧。当 register_globals =on时，各种变量都被注入代码，例如来自 HTML 表单的请求变量。再加上 PHP 在使用变量之前是无需进行初始化的，这就使得更容易写出不安全的代码)打开可能造成意想不到问题.
	$pinyin_permalink_exist= false;
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( is_plugin_active('pinyin-permalink/pinyin-permalink.php') ) {
		$pinyin_permalink_exist='1';
	}else{
		$pinyin_permalink_exist='0';
	}
	return $pinyin_permalink_exist;
}


//附件名HOOK
function cwp_change_upload_file_name($filename) {
	$parts = explode('.', $filename);
	$filename = array_shift($parts);
	$extension = array_pop($parts);
	foreach ( (array) $parts as $part)
	$filename .= '.' . $part;
	if(preg_match('/[一-龥]/u', $filename)){
		/*
		如果你喜欢，你可以用md5(原文件名)做为上传后的文件名，不推荐这样做。
		*/
		//$filename = md5($filename);
		if('0'==pinyin_permalink_exist()){
			require_once('dict.php');
			$filename =coolwp_getPinyinPermalink($filename);
		}elseif('1'==pinyin_permalink_exist()){
			$filename =getPinyinPermalink($filename);
		}
	}
	$filename .= '.' . $extension;
	return $filename ;
}
add_filter('sanitize_file_name', 'cwp_change_upload_file_name', 5,1);