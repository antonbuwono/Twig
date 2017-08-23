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
 * @category    Libraries
 * @author      Edmundas Kondrašovas <as@edmundask.lt>
 * @author      Anton Buwono <antonbuwono@gmail.com>
 * @license     http://www.opensource.org/licenses/MIT
 * @version     0.1.0
 * @copyright   Copyright (c) 2012 Edmundas Kondrašovas <as@edmundask.lt>
 */

class Twig {

    /**
     * Holds for Twig configuration object
     * @var Twig_Environment object
     */
    private $_twig;

    /**
     * Holds for Twig_Loader_Filesystem object
     * @var Twig_Loader_Filesystem object
     */
    private $_twig_loader;

    /**
     * Locations for templates
     * @var Array
     */
    private $_template_locations;

    /**
     * Registered functions
     * @var Array
     */
    private $_functions;

    /**
     * Registered filters
     * @var Array
     */
    private $_filter;

    /**
     * Theme name
     * @var String
     */
    private $_theme;

    /**
     * Data to pass to template
     * @var Array of mixed
     */
    private $_data = [];

    /**
     * Holds for Twig global variables
     * @var Array of mixed
     */
    private $_global = [];

    /**
     * The constructor
     */
    public function __construct() {
        $this->config->load('twig', true);

        // Register Twig auto-loader
        //
        // If you are not using Composer, use the Twig built-in autoloader:
        // require_once '/path/to/lib/Twig/Autoloader.php';
        Twig_Autoloader::register();

        // Setup template location
        $this->setTemplateLocations($this->config->item('default_theme', 'twig'));

        try {
            $this->_twig_loader = new Twig_Loader_Filesystem($this->_template_locations);
        } catch (Twig_Error_Loader $e) {
            log_message('error', 'Twig: Failed to load the defalult theme.');
            if (ENVIRONMENT !== 'production') {
                show_error($e->getRawMessage());
            }
        }

        // Initialize Twig environment
        try {
            $this->_twig = new Twig_Environment($this->_twig_loader, $this->config->item('environment', 'twig'));

            $this->addExtensions();
        } catch (Twig_Error $e) {
            log_message('error', 'Twig: Failed to initialize Twig environment.');
            if (ENVIRONMENT !== 'production') {
                show_error($e->getRawMessage());
            }
        }

        // Register functions
        $this->registerCIFunctions();

        // Initialize (template) defaults
        $this->theme($this->config->item('default_theme', 'twig'));
    }

    /**
     * __get
     *
     * Enables the use of CI super-global without having to define an extra variable.
     *
     * @param   $var
     * @return  mixed
     */
    public function __get($var) {
        return get_instance()->$var;
    }

    /**
     * Set template locations
     *
     * @access private
     * @param  string  $theme Theme name
     * @return void
     */
    private function setTemplateLocations($theme) {
        $this->_theme = $theme;
        $this->_template_locations = [];

        // application/views
        $this->_template_locations[] = VIEWPATH;

        // application/<MODULES LOCATION>/<MODULE NAME>/views
        // NOTE: only if HMVC is installed.
        if (method_exists($this->router, 'fetch_module')) {
            $module_name = $this->router->fetch_module();

            // Only if the current page is served from a module.
            if (!empty($module_name)) {
                foreach (Mpdules::$modules_location as $location => $offset) {
                    if (is_dir($modules_location.$module_name.'/views/')) {
                        $this->_template_locations[] = $modules_location.$module_name.'/views/';
                    }
                }
            }
        }

        // <THEME DIR>/<THEME NAME>
        // NOTE: See $config['theme_dir'] at config/twig.php
        array_unshift($this->_template_locations, $this->config->item('theme_dir', 'twig') . '/' . $theme);

        // No duplications.
        $this->_template_locations = array_unique($this->_template_locations);

        // Reset the paths if needed.
        if (is_object($this->_twig_loader)) {
            $this->_twig_loader->setPaths($this->_template_locations);
        }
    }

    /**
     * Add Twig Extensions
     *
     * @access private
     * @return void
     */
    private function addExtensions() {
        $this->_twig->addExtension(new nochso\HtmlCompressTwig\Extension(true));

        if (ENVIRONMENT !== 'production') {
            $this->_twig->addExtension(new Twig_Extension_Debug());
        }
    }

    /**
     * Register common CI Functions
     *
     * @access private
     * @return void
     */
    private function registerCIFunctions() {
        if (count($this->config->item('functions', 'twig')) > 0) {
            foreach ($this->config->item('functions', 'twig') as $function) {
                $this->_twig->addFunction(new Twig_SimpleFunction($function, $function));
            }
        }
    }

    /**
     * Register functions from custom helpers
     * @return void
     */
    private function registerCICustomHelpers() {
        $token = token_get_all(file_get_contents('anotherfile.php'));
    }

    /**
     * Set theme
     *
     * @access public
     * @param  string $theme Theme name
     * @return object        Instance of this class
     */
    public function theme($theme) {
        if (!is_dir($this->config->item('theme_dir', 'twig') . '/' . $theme)) {
            log_message('error', "Twig: Requested theme {$theme} has not been loaded because it does not exists.");
            if (ENVIRONMENT !== 'production') {
                show_error('Theme does not exists in ' . $this->config->item('theme_dir', 'twig') . $theme . '.');
            }
        }

        if ($theme != $this->_theme) {
            $this->_theme = $theme;
            $this->setTemplateLocations($this->_theme);
        }

        return $this;
    }

    /**
     * Load template before render/display
     *
     * @access private
     * @return Twig_TemplateInterface A template instance representing the given template filename.
     */
    private function load($template) {
        return $this->_twig->loadTemplate($template . '.' . $this->config->item('file_extension', 'twig'));
    }

    /**
     * Render template into variable
     *
     * @access public
     * @param  string $template Template filename
     * @param  array  $data     Extra variables to pass to template
     * @return string           The rendered template (compiled HTML)
     */
    public function render($template, $data = []) {
        $data = array_merge($this->_data, $data);

        $result = false;

        try {
            $result = $this->load($template)->render($data);
        } catch (Twig_Error_Loader $e) {
            log_message('error', "Twig: Failed to render template {$template}.");
            if (ENVIRONMENT !== 'production') {
                show_error($e->getRawMessage());
            }
        }

        return $result;
    }

    /**
     * Set syntax delimiters
     *
     * @access private
     * @return object  Instance of this class
     */
    private function setDelimiters() {
        $delimiters = $this->config->item('delimiters', 'twig');

        try {
            $this->_twig->setLexer(new Twig_Lexer($this->_twig, $delimiters));
        } catch (Twig_Error $e) {
            log_message('error', 'Twig: Failed to set delimiters.');
            if (ENVIRONMENT !== 'production') {
                show_error($e->getRawMessage());
            }
        }

        return $this;
    }

    /**
     * Render and display template
     *
     * @access public
     * @param  string $template Template filename
     * @param  array  $data     Extra variables to pass to template
     * @return void
     */
    public function display($template, $data = []) {
        $data = array_merge($this->_data, $data);

        $this->setDelimiters($this->setDelimiters());

        try {
            $this->load($template)->display($data);
        } catch (Twig_Error_Loader $e) {
            log_message('error', "Twig: Failed to display template {$template}.");
            if (ENVIRONMENT !== 'production') {
                show_error($e->getRawMessage());
            }
        }
    }

    /**
     * Set data
     *
     * @access public
     * @param  mixed   $variable Variable name or array of variable names with value
     * @param  mixed   $value    The value
     * @param  boolean $global   Mark variable(s) as global variable
     * @return object            Instance of this class
     */
    public function set($variable, $value = null, $global = false) {
        if (is_array($variable)) {
            foreach ($variables as $var_name => $var_value) {
                $this->set($var_name, $var_value, $global);
            }
        } else {
            if ($global) {
                $this->_twig->addGlobal($variable, $value);
                $this->_global[$variable] = $value;
            } else {
                $this->_data[$variable] = $value;
            }
        }

        return $this;
    }

    /**
     * Get data
     *
     * @access public
     * @return array  The data
     */
    public function get() {
        return [
            'data' => $this->_data,
            'global' => $this->_global
        ];
    }

    /**
     * Registers a function
     *
     * @access public
     * @param  string $functionName The function name
     * @return object               Instance of this class
     */
    public function registerFunction($functionName) {
        if (!in_array($this->_functions)) {
            $this->_functions[] = $functionName;

            $this->_twig->addFunction(new Twig_SimpleFunction($functionName, $functionName));
        }

        return $this;
    }

    /**
     * Register a filter
     *
     * @access public
     * @param  string $filterName The filter name
     * @return object             Instance of this class
     */
    public function registerFilter($filterName) {
        if (!in_array($this->_filters)) {
            $this->_filters[] = $filterName;

            $this->_twig->addFilter(new Twig_SimpleFilter($filterName, $filterName));
        }

        return $this;
    }

    /**
     * Get this class properties
     *
     * @access public
     * @return mixed  Array of properties
     */
    public function getProperties() {
        $vars = func_get_args();

        $result = [];

        foreach ($vars as $var) {
            $property = '_' . $var;
            if (property_exists($this, $property)) {

                $result[$var] = $this->{$property};
            }
        }

        return $result;
    }

}