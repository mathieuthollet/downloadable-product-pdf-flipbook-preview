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
<center>
    <p>
        <a id="fancybox-flipbook" href="#flipbook-preview" class="btn btn-default">
            {l s='File preview' mod='dlproductpdfflipbookpreview'}
        </a>
    </p>
    <p style="font-style:italic;">
        {l s='Preview PDF.' mod='dlproductpdfflipbookpreview'}<br/>
        {l s='Buy the PDF for optimal quality' mod='dlproductpdfflipbookpreview'}
    </p>
</center>
<div style="display:none;">
    <div id="flipbook-preview" class="clearfix">
        <div class="flipbox-container">
            {if $navigation eq 'yes'}
                <div id="flipbox-navigation">
                    <a href="#" class="prev navigation"><</a>
                    <a href="#" class="next navigation">></a>
                </div>
            {/if}
            <div id="flipbox-content" style="width: 768px; max-width: 100%; height: auto; max-height: 100%;" >
                {foreach from=$image_files item=file}
                    <div class="page">
                        <img class="flipbox-page-img" src="{$image_files_path|escape:'htmlall':'UTF-8'}/{$file|escape:'htmlall':'UTF-8'}"/>
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
</div>

