<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}



// Config
require_once(dirname(__FILE__).'/config.php');



// Module main class
class DlProductPdfFlipbookPreview extends Module
{

    private $_html = '';
    protected $_postErrors = array();



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'dlproductpdfflipbookpreview';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Mathieu Thollet';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Downloadable product PDF flipbook preview');
        $this->description = $this->l('This modules shows a flipbook popin preview of downloadable products if files are PDF');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module ?');
    }



    /**
     * Installer
     * @return bool
     */
    public function install()
    {
        Configuration::updateValue('DL_PRODUCT_PDF_FLIPBOOK_PREVIEW_LIVE_MODE', DL_PRODUCT_PDF_FLIPBOOK_PREVIEW_LIVE_MODE_DEFAULT);
        Configuration::updateValue('DL_PRODUCT_PDF_FLIPBOOK_NB_PAGES', DL_PRODUCT_PDF_FLIPBOOK_NB_PAGES_DEFAULT);
        return parent::install() &&
            $this->registerHook('header') &&
            (Tools::version_compare(_PS_VERSION_, '1.7', '>=') || $this->registerHook('displayRightColumnProduct')) &&
            (Tools::version_compare(_PS_VERSION_, '1.7', '<') || $this->registerHook('displayProductButtons')) ;
    }



    /**
     * Uninstaller
     * @return bool
     */
    public function uninstall()
    {
        Configuration::deleteByName('DL_PRODUCT_PDF_FLIPBOOK_PREVIEW_LIVE_MODE');
        Configuration::deleteByName('DL_PRODUCT_PDF_FLIPBOOK_NB_PAGES');
        return parent::uninstall();
    }



    /**
     * Admin : Load the configuration form
     */
    public function getContent()
    {
        if (((bool)Tools::isSubmit('submitDPPFPModule')) == true) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        }
        $imagick_loaded = extension_loaded('imagick');
        $this->context->smarty->assign('imagick_loaded', $imagick_loaded);
        $this->_html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        if ($imagick_loaded) {
            $this->_html .= $this->_renderForm();
        }
        return $this->_html;
    }



    /**
     * Admin : Create the form that will be displayed in the configuration of your module.
     */
    protected function _renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitDPPFPModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->_getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array($this->_getConfigForm()));
    }



    /**
     * Admin : Create the structure of configuration form
     */
    protected function _getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'DL_PRODUCT_PDF_FLIPBOOK_PREVIEW_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Number of pages to display'),
                        'name' => 'DL_PRODUCT_PDF_FLIPBOOK_NB_PAGES',
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Clear cache'),
                        'name' => 'DL_PRODUCT_PDF_FLIPBOOK_DELETE_CACHE',
                        'is_bool' => true,
                        'desc' => $this->l('Delete generated image files'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }



    /**
     * Admin : Set values for the form inputs.
     */
    protected function _getConfigFormValues()
    {
        return array(
            'DL_PRODUCT_PDF_FLIPBOOK_PREVIEW_LIVE_MODE' => Configuration::get('DL_PRODUCT_PDF_FLIPBOOK_PREVIEW_LIVE_MODE'),
            'DL_PRODUCT_PDF_FLIPBOOK_NB_PAGES' => Configuration::get('DL_PRODUCT_PDF_FLIPBOOK_NB_PAGES'),
            'DL_PRODUCT_PDF_FLIPBOOK_DELETE_CACHE' => false,
        );
    }



    /**
     * Admin : forms validation
     * fills $this->_postErrors if errors encountered
     */
    protected function _postValidation()
    {
        if (!is_numeric(Tools::getValue('DL_PRODUCT_PDF_FLIPBOOK_NB_PAGES'))) {
            $this->_postErrors[] = $this->l('Number of pages must be numeric');
        }
    }



    /**
     * Admin : forms processing
     * Adds to $this->_html confirmation or error messages
     * @return string html
     */
    protected function _postProcess()
    {
        // Update values
        $clear_cache = false;
        $form_values = $this->_getConfigFormValues();
        foreach (array_keys($form_values) as $key) {
            if ($key != 'DL_PRODUCT_PDF_FLIPBOOK_DELETE_CACHE' && Tools::getValue($key) != Configuration::get($key)) {
                $clear_cache = true;
                Configuration::updateValue($key, Tools::getValue($key));
            }
        }
        $this->_html .= $this->displayConfirmation($this->l('Settings have been updated.'));

        // Clear cache : if checked, or if another value changed
        if (Tools::getValue('DL_PRODUCT_PDF_FLIPBOOK_DELETE_CACHE') || $clear_cache) {
            $this->_clearCacheFiles();
            $this->_html .= $this->displayConfirmation($this->l('Cache has been cleared.'));
        }

    }



     /**
     * Clear all generated image files
     */
    protected function _clearCacheFiles()
    {
        $this->rrmdir(DL_PRODUCT_PDF_FLIPBOOK_IMAGES_PATH);
        $this->rrmdir(DL_PRODUCT_PDF_FLIPBOOK_TMP_PATH);
    }



    /**
     * Front : Add the CSS & JavaScript files
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . 'views/js/vendor/turn.min.js', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/vendor/zoom.min.js', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
    }



    /**
     * Displays flipbook button on product page (1.6)
     */
    public function hookDisplayRightColumnProduct($params)
    {
        // If module live
        if (Configuration::get('DL_PRODUCT_PDF_FLIPBOOK_PREVIEW_LIVE_MODE')) {
            $id_product = (int)Tools::getValue('id_product');
            $filename = ProductDownload::getFilenameFromIdProduct($id_product);

            // If product downloadable file exists
            $nb_pages_conf = Configuration::get('DL_PRODUCT_PDF_FLIPBOOK_NB_PAGES');
            if (isset($filename) && $filename != '') {

                // Try ... catch because non PDF files will throw exceptions
                try {

                    // Cache die read / write
                    $image_path = DL_PRODUCT_PDF_FLIPBOOK_IMAGES_PATH . $filename . '/';
                    if (!is_dir($image_path)) {
                        mkdir($image_path);
                    }
                    if ($this->isDirEmpty($image_path)) {
                        $fileToConvert = DL_PRODUCT_PDF_FLIPBOOK_TMP_PATH.$filename.'.pdf';
                        copy(_PS_DOWNLOAD_DIR_.$filename, $fileToConvert);

                        // cache write (imagick needed)
                        if (extension_loaded('imagick')) {
                            $imagick = new Imagick();
                            $imagick->readImage($fileToConvert);
                            $nb_pages_doc = $imagick->getNumberImages();
                            if ($nb_pages_conf > $nb_pages_doc || $nb_pages_conf == 0 || is_null($nb_pages_conf)) {
                                $nb_pages_conf = $nb_pages_doc;
                            }
                            for ($i = 0; $i < $nb_pages_conf; $i++) {
                                $imagick->readImage($fileToConvert ."[$i]");
                                $imagick->setImageFormat('jpeg');
                                if ($i < 9) {
                                    $nbzero = '00';
                                } elseif ($i < 99) {
                                    $nbzero = '0';
                                } else {
                                    $nbzero = '';
                                }
                                $page = $i + 1;
                                $imagick->writeImage($image_path . $nbzero . $page . '.jpg');
                            }
                        }
                        unlink($fileToConvert);
                    }

                    // smarty vars
                    $url = "http://" . $_SERVER['HTTP_HOST'] . '/';
                    $url = str_replace('index.php', '', $url);
                    $files = scandir($image_path);
                    $image_files = array();
                    foreach ($files as $file) {
                        if ($file != "." && $file != "..") {
                            $image_files[] = $file;
                        }
                    };
                    $this->context->smarty->assign(array(
                        'navigation' => true,
                        'image_files_path' => DL_PRODUCT_PDF_FLIPBOOK_IMAGES_URI . $filename,
                        'image_files' => $image_files,
                    ));
                    return $this->display(__FILE__, 'right-column-product.tpl');
                } catch (Exception $e) {
                }
            }
        }
    }



    /**
     * Displays flipbook button on product page (1.7)
     */
    public function hookDisplayProductButtons($params) 
    {
        return $this->hookDisplayRightColumnProduct($params);
    }



    /**
     * Utility function : is dir empty ?
     * @return bool
     */
    public function isDirEmpty($dir)
    {
        if (!is_readable($dir)) {
            return null;
        }
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                return false;
            }
        }
        return true;
    }



    /**
     * Utility function : removes a directory content
     */
    public function rrmdir($dir, $delete = false)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if (!in_array($object, array('.', '..', 'index.php'))) {
                    if (is_dir($dir.'/'.$object)) {
                        $this->rrmdir($dir.'/'.$object, true);
                    } else {
                        unlink($dir.'/'.$object);
                    }
                }
            }
            if ($delete) {
                rmdir($dir);
            }
        }
    }
}
