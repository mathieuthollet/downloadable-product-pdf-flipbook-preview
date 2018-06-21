{*
* 2007-2016 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="panel">
    <h3><i class="icon icon-credit-card"></i> {l s='Downloadable product PDF flipbook preview' mod='dlproductpdfflipbookpreview'}</h3>
    <p>
        {l s='This modules shows a flipbook popin preview of downloadable products if files are PDF.' mod='dlproductpdfflipbookpreview'}<br />
    </p>
    {if !$imagick_loaded}
        <div class="alert alert-danger">
            {l s='Error : this module needs the imagick PHP extension' mod='dlproductpdfflipbookpreview'}<br/>
            <a target="_blank" href="http://php.net/manual/book.imagick.php">
                {l s='Image Processing (ImageMagick) php extension documentation' mod='dlproductpdfflipbookpreview'}
            </a>
        </div>
    {/if}
</div>
