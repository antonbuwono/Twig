<?php defined('BASEPATH') or die();

/**
 * Twig - Twig template engine implementation for CodeIgniter
 *
 * Modified from Twiggy by Edmundas Kondrašovas.
 *
 * Twig is not just a simple implementation of Twig template engine
 * for CodeIgniter. It supports themes, layouts, templates for regular
 * apps and also for apps that use HMVC (module support).
 *
 * @package     CodeIgniter
 * @subpackage  Twig
 * @category    Config
 * @author      Edmundas Kondrašovas <as@edmundask.lt>
 * @author      Anton Buwono <antonbuwono@gmail.com>
 * @license     http://www.opensource.org/licenses/MIT
 * @version     0.1.1
 * @copyright   Copyright (c) 2012 Edmundas Kondrašovas <as@edmundask.lt>
 */

/*
| -------------------------------------------------------------------
| Twig Cache Dir
| -------------------------------------------------------------------
|
| Path to the cache folder for compiled twig templates.
|
*/
$config['cache_dir'] = APPPATH . 'cache/twig';

/*
| -------------------------------------------------------------------
| Themes Base Dir
| -------------------------------------------------------------------
|
| Directory where themes are located at.
|
*/
$config['theme_dir']        = FCPATH . 'themes';

/*
| -------------------------------------------------------------------
| Default theme
| -------------------------------------------------------------------
*/
$config['default_theme']    = 'default';

/*
| -------------------------------------------------------------------
| Template file extension
| -------------------------------------------------------------------
|
| This lets you define the extension for template files. It doesn't
| affect how Twig deals with templates but this may help you if you
| want to distinguish different kinds of templates.
|
| For example, for CodeIgniter you may use *.html.twig template files
| and *.html.jst for js templates.
|
*/
$config['file_extension']   = 'twig';

/*
| -------------------------------------------------------------------
| Syntax Delimiters
| -------------------------------------------------------------------
|
| If you don't like the default Twig syntax delimiters or if they
| collide with other languages (for example, you use handlebars.js
| in your templates), here you can change them.
|
| Ruby erb style:
|
|   'tag_comment'   => array('<%#', '#%>'),
|   'tag_block'     => array('<%', '%>'),
|   'tag_variable'  => array('<%=', '%>')
|
| Smarty style:
|
|    'tag_comment'  => array('{*', '*}'),
|    'tag_block'    => array('{', '}'),
|    'tag_variable' => array('{$', '}'),
|
*/
$config['delimiters'] = [
    'tag_comment'  => ['{#', '#}'],
    'tag_block'    => ['{%', '%}'],
    'tag_variable' => ['{{', '}}'],
];

/*
|--------------------------------------------------------------------------
| Auto-reigster functions
|--------------------------------------------------------------------------
|
| Here you can list all the functions that you want Twig to automatically
| register them for you.
|
| NOTE: only registered functions can be used in Twig templates.
|
*/
$config['functions'] = [
    'base_url',
    'site_url',
    'lang',

    'form_open',
    'form_close',
    'form_label',
    'form_input',
    'form_password',
    'form_checkbox',
    'form_dropdown',
    'form_textarea',
    'form_button',
    'form_submit',
];

/*
| -------------------------------------------------------------------
| Environment options
| -------------------------------------------------------------------
|
| These are all twig-specific options that you can set. To learn more
| about each option, check the official documentation.
|
| NOTE: cache option works slightly differently than in Twig. In Twig
| you can either set the value to FALSE to disable caching, or set
| the path to where the cached files should be stored (which means
| caching would be enabled in that case). This is not entirely
| convenient if you need to switch between enabled or disabled
| caching for debugging or other reasons.
|
| Therefore, here the value can be either TRUE or FALSE. Cache
| directory can be set separately.

| debug:
|   Boolean, Twig default = false
|   When set to true, it automatically set "auto_reload" to true as
|   well
|
| charset:
|   String, Twig default = 'utf-8'
|   The charset used by the templates.
|
| base_template_class:
|   String, Twig default = 'Twig_Template'
|   The base template class to use for generated templates.
|
| cache:
|   Boolean (false) or String, Twig default = false
|   An absolute path where to store the compiled templates, or false
|   to disable compilation cache.
|
| auto_reload:
|   Boolean, Twig default = null
|   Whether to reload the template if the original source changed.
|   If you don't provide the autoreload option, it will be determined
|   automatically based on the debug value.
|
| strict_variables:
    Boolean, Twig default = false
|   If set to false, Twig will silently ignore invalid variables
|   (variables and or attributes/methods that do not exist) and
|   replace them with a null value. When set to true, Twig throws an
|   exception instead.
|
| autoescape:
|   Boolean, Twig default = true
|   If set to true, auto-escaping will be enabled by default for all
|   templates. As of Twig 1.8, you can set the escaping strategy to
|   use (html, js, false to disable). As of Twig 1.9, you can set the
|   escaping strategy to use (css, url, html_attr, or a PHP callback
|   that takes the template "filename" and must return the escaping
|   strategy to use -- the callback cannot be a function name to
|   avoid collision with built-in escaping strategies).
|
| optimizations:
|   Integer, Twig default = -1
|   A flag that indicates which optimizations to apply
|   (-1 -- all optimizations are enabled; set it to 0 to disable).
|
*/
$config['environment']['debug']               = (ENVIRONMENT != 'production');
$config['environment']['charset']             = 'utf-8';
$config['environment']['base_template_class'] = 'Twig_Template';
$config['environment']['cache']               = (ENVIRONMENT != 'production') ? false :  $config['cache_dir'];
$config['environment']['auto_reload']         = null;
$config['environment']['strict_variables']    = false;
$config['environment']['autoescape']          = true;
$config['environment']['optimizations']       = -1;
