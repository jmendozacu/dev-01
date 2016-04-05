var AjaxCart = Class.create();

AjaxCart.prototype = {
  ajaxLoading: null,
  miniCartElement: null,
  sidebarCartElement: null,
  miniCartItemCount: null,
  productCartFormElement: null,
  productCartFormObject: null,
  optionsPopup: null,
  plusOneElement: null,
  initConfig: {
    'show_success_message': true,
    'timerErrorMessage': 3000,
    'addWishlistItemUrl': null,
		'removeItemUrl': null
  },

  initialize : function(initConfig) {
    this.initConfig = initConfig;
    this.createElements();
    if ($('product_addtocart_form')) this.productCartFormObject = new VarienForm('product_addtocart_form');
    if ($$('div.mini-cart-info')[0]) this.miniCartElement = $$('div.mini-cart-info')[0];
    if ($$('span.mini-cart-item-count')[0]) this.miniCartItemCount = $$('span.mini-cart-item-count')[0];
    if ($$('div.block-cart')[0]) this.sidebarCartElement = $$('div.block-cart')[0];
    if ($('product_addtocart_form')) this.productCartFormElement = $('product_addtocart_form');

    /* replace button add to cart */
    this.replaceButtonAddToCart('button.btn-cart');

  },

  getInitConfig: function(config_name) {
    return this.initConfig[config_name];
  },

  createElements: function() {
    $(document.body).insert('<div class="block" id="ajax_cart_loading"></div>');
    this.ajaxLoading = $('ajax_cart_loading');
    this.ajaxLoading.hide();

    $(document.body).insert('<div id="ajaxcart-overlay"></div>');
    this.optionsPopup = $('ajaxcart-overlay');
    this.optionsPopup.hide();

    $(document.body).insert('<div id="plus_one"></div>');
    this.plusOneElement = $('plus_one');
    this.plusOneElement.hide();
  },

  validateProductCartForm: function() {
    return this.productCartFormObject.validator.validate();
  },

  sendAjaxAddToCart: function(url) {
    new Ajax.Request(url, {

      onCreate: function() {
        this.ajaxLoading.show();
        this.optionsPopup.hide();
      }.bind(this),

      onComplete: function(response) {
        /* hide ajax loading */
        this.ajaxLoading.hide();

        /* show popup if product type is grouped */
        if (response.responseJSON.html_popup) {
          this.optionsPopup.update(response.responseJSON.html_popup);
          this.optionsPopup.show();
          if ($('ajaxcart_product_addtocart_form')) {
            Event.observe($('ajaxcart_add'), 'click', this.addToCartFromPopup.bind(this));
            Event.observe($('ajaxcart_cancel'), 'click', this.closeOptionsPopup.bind(this));
          }
        }

        /* show message, update cart info */
        else {
          if (response.responseJSON) {
            this.showMessage(response.responseJSON);
            this.updateCartInfo(response.responseJSON);
          }
        }
      }.bind(this)
    });
  },

  plusOneEffect: function() {
    Effect.Appear(this.plusOneElement, { queue: 'end'});
    Effect.Fade(this.plusOneElement, { queue: 'end'});
  },

  updateCartInfo: function(JSON_response) {
    /* update mini cart */
    if (this.miniCartElement && JSON_response.mini_cart_html) {
      this.miniCartElement.update(JSON_response.mini_cart_html);
      //this.reRunCoreCode();
      Effect.Pulsate(this.miniCartElement);
    }
    /* update top link cart */
    if (this.miniCartItemCount && JSON_response.toplink_cart_html) {
      this.miniCartItemCount.update(JSON_response.toplink_cart_html);
      Effect.Pulsate(this.miniCartItemCount);
    }
    /* update sidebar cart */
    if (this.sidebarCartElement && JSON_response.sidebar_cart_html) {
      this.sidebarCartElement.replace(JSON_response.sidebar_cart_html);
      this.sidebarCartElement = $$('div.block-cart')[0];
      Effect.Pulsate(this.sidebarCartElement);
    }
  },

  showMessage: function(responseJSON) {
    if (responseJSON.success == 'true') {
      if (this.getInitConfig('show_success_message')) {
        this.optionsPopup.update(responseJSON.success_message);
        Event.observe($('ajaxcart_continue_shopping'), 'click', this.closeOptionsPopup.bind(this));
        this.optionsPopup.show();
      } else {
        this.plusOneEffect();
      }
    }
    else if (responseJSON.message) {
      this.optionsPopup.update(responseJSON.message);
      this.optionsPopup.show();
      setTimeout(function() {
        Effect.Fade(this.optionsPopup);
      }.bind(this),this.getInitConfig('timerErrorMessage'));
    }
  },

  reRunCoreCode: function() {
    // =============================================
    // Skip Links
    // =============================================

    var skipContents = $j('.skip-content');
    var skipLinks = $j('.skip-link');

    skipLinks.on('click', function (e) {
      e.preventDefault();

      var self = $j(this);
      var target = self.attr('href');

      // Get target element
      var elem = $j(target);

      // Check if stub is open
      var isSkipContentOpen = elem.hasClass('skip-active') ? 1 : 0;

      // Hide all stubs
      skipLinks.removeClass('skip-active');
      skipContents.removeClass('skip-active');

      // Toggle stubs
      if (isSkipContentOpen) {
        self.removeClass('skip-active');
      } else {
        self.addClass('skip-active');
        elem.addClass('skip-active');
      }
    });

    $j('#header-cart').on('click', '.skip-link-close', function(e) {
      var parent = $j(this).parents('.skip-content');
      var link = parent.siblings('.skip-link');

      parent.removeClass('skip-active');
      link.removeClass('skip-active');

      e.preventDefault();
    });
  },

  //replace action of button add to cart
  replaceButtonAddToCart: function(addCartClass) {
    $$(addCartClass).each(function(elementAdd){
      var onclick = elementAdd.readAttribute('onclick');
			// Fix add to cart in IE8
			// Add data-onclick for button add to cart
			var myNav = navigator.userAgent.toLowerCase();
			var isIE = (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
			if(isIE == 7 || isIE == 8){
				elementAdd.writeAttribute("data-onclick", onclick);
			}
      if(onclick){
        var on_click_url =  onclick.toString();
        var pattern_url = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
        var url_check = pattern_url.test(on_click_url);

        if ((on_click_url.search('addWItemToCart') != -1)) {
          elementAdd.onclick = '';
          Event.observe(elementAdd, 'click', this.addToCartWishListPage.bind(this));
        }
        else if (url_check) {
          elementAdd.onclick = '';
          Event.observe(elementAdd, 'click', this.getOptionPopUpContent.bind(this));
        } else {
          if(this.productCartFormElement){
            var urlAction = this.productCartFormElement.readAttribute('action');
            if(urlAction.search('checkout/cart/add') != -1){  
              elementAdd.onclick = '';        
              Event.observe(elementAdd, 'click', this.sendProductCartForm.bind(this));
            }
          }
        }
      }
    }.bind(this));
  },
	
	removeItem: function(id, isInCart) {
		if (confirm('Are you sure you would like to remove this item from the shopping cart?')) {
			var url = this.getInitConfig('removeItemUrl');
			url = url + 'id/' + id;		
			new Ajax.Request(url, {
				onCreate: function() {
					this.ajaxLoading.show();
					//this.optionsPopup.hide();
				}.bind(this),

				onComplete: function(transport) {
        /* hide ajax loading */
					this.ajaxLoading.hide();
					
					if (transport && transport.responseText){
            try{
                response = eval('(' + transport.responseText + ')');
            }
            catch (e) {
                response = {};
            }
					}
					if (response.mini_cart_html) {
						//this.updateCartInfo(response.responseJSON);
						if (this.miniCartElement) {
							this.miniCartElement.update(response.mini_cart_html);
							//this.reRunCoreCode();
							Effect.Pulsate(this.miniCartElement);
						}
						
						if (isInCart == '1') {
							location.reload(); 
						}
          }

				}.bind(this)
			});
		}		
	},

  addToCartWishListPage: function(event) {
    // TODO
    // Fix add to cart in IE8
		// Get data-onclick instead of onclick attribute in add to cart button
		var myNav = navigator.userAgent.toLowerCase();
		var isIE = (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
		if(isIE == 7 || isIE == 8){
      var dataOnclick = event.srcElement.outerHTML;
      var firstIndex = dataOnclick.lastIndexOf('data-onclick');
      var lastIndex = dataOnclick.lastIndexOf(")") + 3;
      var onclick = dataOnclick.substring(firstIndex, lastIndex).substring(14);
			
		}else{
			var button = event.currentTarget;
			var onclick = button.readAttribute('onclick');	
		}
    var wishlist_item_id = onclick.match(/\(.+\)/).pop().replace(/[\(\)]/g,'');
		var item_qty = $('wl_qty_'+wishlist_item_id).value;
		var wishlistForm = $('wishlist-view-form');
		var formKeyInput = wishlistForm['form_key'];
    //var item_qty = $('wishlist-view-form')['qty['+wishlist_item_id+']'].getValue();
    //var form_key = $('wishlist-view-form')['form_key'].getValue();
    var form_key = Form.Element.getValue(formKeyInput);
    var url = this.getInitConfig('addWishlistItemUrl');
    if (wishlist_item_id && item_qty && url) {
      new Ajax.Request(url, {
        parameters: {
          form_key: form_key,
          wishlist_item_id: wishlist_item_id,
          qty: item_qty
        },

        onCreate: function() {
          this.optionsPopup.hide();
          this.ajaxLoading.show();
        }.bind(this),

        onComplete: function(response) {
          /* hide ajax loading */
          this.ajaxLoading.hide();

          /* show message, update cart info */
          if (response.responseJSON) {
            if(response.responseJSON.redirect_url){            
              setLocation(response.responseJSON.redirect_url);  
            }
            if(response.responseJSON.success=="process"){
              if (response.responseJSON.html_popup) {
                this.optionsPopup.update(response.responseJSON.html_popup);
                this.optionsPopup.show();
                if ($('ajaxcart_product_addtocart_form')) {
                  Event.observe($('ajaxcart_add'), 'click', this.addToCartFromWishlist.bind(this));
                  Event.observe($('ajaxcart_cancel'), 'click', this.closeOptionsPopup.bind(this));
                }
              }

            } else if(response.responseJSON.success=="true"){
              this.updateCartInfo(response.responseJSON);
              this.removeWishlistItem(wishlist_item_id);
            }
            this.showMessage(response.responseJSON);          
          }

        }.bind(this)
      });
    } else {
      //
    }
  },

  removeWishlistItem: function(wishlist_item_id) {
    Effect.Fade($('item_'+wishlist_item_id));
  },
	
  sendProductCartForm: function(event) {
    var actionUrl = this.productCartFormElement.readAttribute('action');
    var data_form = this.productCartFormElement.serialize();
    if ((this.validateProductCartForm())&&((actionUrl.search('checkout/cart/add') != -1)||(actionUrl.search('wishlist/index/cart') != -1))){
      actionUrl = actionUrl.replace('checkout/cart/add','ajaxcart/index/addToCart');
      actionUrl = actionUrl.replace('wishlist/index/cart','ajaxcart/index/addToCart');
      this.sendAjaxAddToCart(actionUrl+"?"+data_form);
    }
  },

  getOptionPopUpContent: function(event) {
		// Fix add to cart in IE8
		// Get data-onclick instead of onclick attribute in add to cart button
		var myNav = navigator.userAgent.toLowerCase();
		var isIE = (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
		if(isIE == 7 || isIE == 8){
      var dataOnclick = event.srcElement.outerHTML;
      var firstIndex = dataOnclick.lastIndexOf('data-onclick');
      var lastIndex = dataOnclick.lastIndexOf("/')") + 3;
      var onclick = dataOnclick.substring(firstIndex, lastIndex).substring(14);
		}else{
			var button = event.currentTarget;
			var onclick = button.readAttribute('onclick');	
		}
    var urlAdd = onclick.match(/[\'\"](.+?)[\'\"]/).pop();
    //    urlAdd = urlAdd.substring(1,urlAdd.length);
    if (urlAdd.search('checkout/cart/add') != -1) {
      /* when button is submit button of cart form for simple product */
      urlAdd = urlAdd.replace('checkout/cart/add','ajaxcart/index/addToCart');
      this.sendAjaxAddToCart(urlAdd);
    } else {
      /* when button is a link to product page */
      var urlPartsArray = urlAdd.split('?');
      if(urlAdd.search('options=cart') != -1){
        urlAdd = urlAdd;
      } else {
        if (urlPartsArray[1]) {
          urlAdd +="&options=cart";
        } else {
          urlAdd +="?options=cart";
        }        
      }
      this.getOptionOfProduct(urlAdd);
    }
  },

  getOptionOfProduct: function(url) {
    new Ajax.Request(url, {
      onCreate: function() {
        this.ajaxLoading.show();
      }.bind(this),

      onComplete: function(response) {
        this.ajaxLoading.hide();
        if (response.responseJSON.redirect_url) {
          setLocation(response.responseJSON.redirect_url);
        }

        if (response.responseJSON.html_popup) {
          this.optionsPopup.update(response.responseJSON.html_popup);
          this.optionsPopup.show();
          if ($('ajaxcart_product_addtocart_form')) {
            Event.observe($('ajaxcart_add'), 'click', this.addToCartFromPopup.bind(this));
            Event.observe($('ajaxcart_cancel'), 'click', this.closeOptionsPopup.bind(this));
          }
        }
      }.bind(this)
    });    
  },

  closeOptionsPopup: function() {
    this.optionsPopup.hide();
  },

  addToCartFromPopup: function() {
    var actionUrl = $('ajaxcart_product_addtocart_form').readAttribute('action');
    var data_form = $('ajaxcart_product_addtocart_form').serialize();
    var productOptionsForm = new VarienForm('ajaxcart_product_addtocart_form');
    if ((productOptionsForm.validator.validate())&&(actionUrl.search('checkout/cart/add')) != -1) {
      actionUrl = actionUrl.replace('checkout/cart/add','ajaxcart/index/addToCart');
      this.sendAjaxAddToCart(actionUrl+"?"+data_form);
    }
  } ,
  addToCartFromWishlist: function() {
    var actionUrl = $('ajaxcart_product_addtocart_form').readAttribute('action');
    var data_form = $('ajaxcart_product_addtocart_form').serialize();
    var productOptionsForm = new VarienForm('ajaxcart_product_addtocart_form');
    if ((productOptionsForm.validator.validate())&&(actionUrl.search('checkout/cart/add')) != -1) {
      actionUrl = actionUrl.replace('checkout/cart/add','ajaxcart/index/addWishlistItem');
      this.sendAjaxAddToCart(actionUrl+"?"+data_form);
    }
		var wishlist_item_id = $('ajaxcart_product_addtocart_form')['wishlist_item_id'].getValue();
		this.removeWishlistItem(wishlist_item_id);
  },
	
	ajaxAddToWishlist: function(url) {
		url = url.replace('wishlist/index/add','customwishlist/index/add');
		if(!customerIsLoggedIn){
			window.location.href = BASE_URL + 'customer/account/login';
		}
		new Ajax.Request(url, {
			onCreate: function() {
				this.ajaxLoading.show();
			}.bind(this),

			onComplete: function(transport) {
				if (transport && transport.responseText) {
					try {
						response = eval('(' + transport.responseText + ')');
					}
					catch (e) {
						response = {};
					}
				}
				this.ajaxLoading.hide();
				this.optionsPopup.update(response.html);
        this.optionsPopup.show();
				//this.optionsPopup.update("Fuck YOU, do not work");
      //this.optionsPopup.show();
			}.bind(this)
		});
	},
	
	ajaxRemoveCompareItem: function(url) {
		new Ajax.Request(url, {
				parameters: {isAjax: 1, method: 'POST'},
				onLoading: function(){$('compare-list-please-wait').show();},
				onSuccess: function(transport) {
						$('compare-list-please-wait').hide();
						window.location.reload();
						window.opener.location.reload();
				}
		});
	}
}
  
