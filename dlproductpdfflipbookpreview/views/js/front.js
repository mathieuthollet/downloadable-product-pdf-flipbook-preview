/*
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
*/
$(document).ready(function() {
    
    // Button trigger
    $("a#fancybox-flipbook").fancybox({});

    // Keyboard navigation
    $(window).bind('keydown', function(e) {
        if (e.keyCode == 37)
            $('#flipbox-content').turn('previous');
        else if (e.keyCode == 39)
            $('#flipbox-content').turn('next');
    });

    // Navigation styling
    $("#flipbox-navigation a.navigation").css({
        "opacity" : 0.25
    });
    $("#flipbox-navigation a.navigation").hover(function() {
        $(this).css({
            "opacity" : 0.5
        });
    }, function() {
        $(this).css({
            "opacity" : 0.25
        });
    });

    // Navigation links
    $("#flipbox-navigation a.prev").click(function() {
        $('#flipbox-content').turn('previous');
        return false;
    });
    $("#flipbox-navigation a.next").click(function() {
        $('#flipbox-content').turn('next');
        return false;
    });

    // Turn plugin
    $('<img>').attr('src', function() {
        var imgUrl = $('.page img').attr('src');
        return imgUrl;
    }).load(function(imgUrl) {
        var flipbox_content = $("#flipbox-content");
        var size = flipbox_content.turn("size");
        var width = size.width / 2;
        var height = (width * imgUrl.currentTarget.height) / imgUrl.currentTarget.width;
        $('#flipbox-content').turn({
            display : 'double',
            acceleration : true,
            gradients : true,
            elevation : 500,
            height : height
        });
        $('#flipbox-content img.flipbox-page-img').css({
            'width' : width,
            'height' : height
        });
    });
});
