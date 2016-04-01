/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
var $tab = jQuery.noConflict();
jQuery(document).ready(function($tab) {
  if( $tab("ul.tabs").length ){
    $tab(".tab_content").hide(); //Hide all content
    var hash, $tabActive, $contentActive;
    var getHash = location.hash;

    if(getHash !== '' && $tab(getHash).length > 0){
      hash = location.hash.replace("#","");

      $tab("ul.tabs li").removeClass("active");
      $tab(".tab_content").hide();

      $tabActive = $tab("ul.tabs a[href='#" + hash + "']");
      $contentActive = $tab(".tab_container #" + hash);

      $tabActive.closest('li').addClass('active');
      $contentActive.show();
    }else{
      $tab("ul.tabs li:first").addClass("active").show(); //Activate first tab
      $tab(".tab_content:first").show(); //Show first tab content
    }

    //On Click Event
    $tab("ul.tabs li").click(function() {
      $tab("ul.tabs li").removeClass("active"); //Remove any "active" class
      $tab(this).addClass("active"); //Add "active" class to selected tab
      $tab(".tab_content").hide(); //Hide all tab content

      var activeTab = $tab(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
      //location.hash = activeTab;
      $tab(activeTab).fadeIn(); //Fade in the active ID content
      return false;
    });
  }

  if( $tab("ul.tabsPage").length ){
    var hash;
    var getHash = location.hash;

    if( (getHash !== '') && ($tab('ul.tabsPage a[href="' + getHash + '"]').length > 0) ){
      hash = location.hash.replace("#c","");
    }else{
      location.hash = 'ctab-cms01';
      hash = 'tab-cms01';
    }

    var $tabActive = $tab('ul.tabsPage a[href="#c' + hash + '"]');
    var $contentActive = $tab(".tab_container #" + hash);

    //When page loads...
    $tab(".tab_content").hide(); //Hide all content
    $tabActive.closest('li').addClass('active');
    $contentActive.show();

    //On Click Event
    $tab("ul.tabsPage li").click(function(e) {

      $tab("ul.tabsPage li").removeClass("active"); //Remove any "active" class
      $tab(this).addClass("active"); //Add "active" class to selected tab
      $tab(".tab_content").hide(); //Hide all tab content

      var activeTab = $tab(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
      location.hash = activeTab;
      activeTab = activeTab.replace("#c","#");

      $tab(activeTab).fadeIn(); //Fade in the active ID content

      return false;
    });
  }


  $tab("a.review-tab").click(function() {

    $tab("ul.tabs li").removeClass("active"); //Remove any "active" class
    $tab("ul.tabs li.product-review").addClass("active"); //Add "active" class to selected tab
    $tab(".tab_content").hide(); //Hide all tab content

    var activeTab = $tab(this).attr("href"); //Find the href attribute value to identify the active tab + content
    $tab(activeTab).fadeIn(); //Fade in the active ID content
    $tab('#nickname_field').focus(); //Fade in the active ID content
    return false;
  });

});