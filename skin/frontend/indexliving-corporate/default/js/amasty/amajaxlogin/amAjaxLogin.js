AmAjaxLogin = Class.create();
AmAjaxLogin.prototype = 
{
    options : null,
    
    url : null,
    
    timer: 0,
    
    srcImageProgress : null,
		
		extraParams: '',
    
    initialize : function(options) {
        this.url = options['send_url'];
        this.options = options;
        this.srcImageProgress = options['src_image_progress'];
    },
		
		setCookie: function(cname, cvalue, exdays){
			var d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			var expires = "expires="+d.toUTCString();
			document.cookie = cname + "=" + cvalue + "; " + expires;
		},
		
		getCookie: function(cname) {
			var name = cname + "=";
			var ca = document.cookie.split(';');
			for(var i=0; i<ca.length; i++) {
					var c = ca[i];
					while (c.charAt(0)==' ') c = c.substring(1);
					if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
			}
			return "";
		},
		
		
    /*start helper functions*/
    updateHeader : function() {
        if($$('.page-header-container')[0]){
            var url = this.url.replace(this.url.substring(this.url.length-6, this.url.length), 'header');//   
            new Ajax.Request(url, {
                method: 'post',
                onSuccess: function(transport) {
                   if(transport.responseText) {
                        var response = transport.responseText;
                        var holderDiv = document.createElement('div');
                        holderDiv = $(holderDiv);
                        holderDiv.innerHTML = response; 
                        text = ""; 
                        holderDiv.childElements().each(function(div){
                           if(div.hasClassName("page-header")) text += div.innerHTML;
                        })
                        if(text) $$('.page-header')[0].innerHTML = text;
                        AmAjaxLoginLoad('a[href*="customer/account/login/"]');    
                        AmAjaxLogoutLoad('a[href*="customer/account/logout/"]');      
                        reRunSomeScripts();       
                    }       
                }.bind(this),
            });
         }
    },
 
    showAnimation: function() {
        var progress = document.createElement('div');
        progress = jQuery(progress); // fix for IE
        progress.attr('id','amprogress');
        
        var container = document.createElement('div');
        container = jQuery(container); // fix for IE
        container.attr('id','amimg_container');
        container.appendTo(progress);
        
        var img = document.createElement('img');
        img = jQuery(img); // fix for IE
        img.attr('src', this.srcImageProgress);
        img.appendTo(container);
         
        container.width('150px');
        var width = container.width();
        width = "-" + width/2 + "px" ;
        container.css("margin-left", width);
        progress.hide().appendTo('body').fadeIn();   
    }, 
    
    hideAnimation: function() {
       /* if($('amprogress')) {
            jQuery('#amprogress').fadeOut(function() {
                if($('amprogress')) $('amprogress').remove();
            });     
        }*/
        if($('amprogress')) $('amprogress').remove();
    },
		
		setCookie: function(cname, cvalue, exdays){
			var d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			var expires = "expires="+d.toUTCString();
			document.cookie = cname + "=" + cvalue + "; " + expires;
		},
    
      //add parametr from form on product view page
    addFormParam: function(id) {
        postData = "";
        var form = $(id);
        if(form) {
            var len=form.elements.length-1;
            var validator = new Validation(form);
            if (validator.validate()) {
                postData = jQuery(form).serialize();               
            }
        }
        return postData;
    },
    
    showMessage: function(response, isShowMessage) {
			if(isShowMessage){
        if($('am-ajaxlogin-container'))$('am-ajaxlogin-container').remove();
        var container = document.createElement('div');
        container = $(container);
        container.id = 'am-ajaxlogin-container';  
        $$('body')[0].appendChild(container);
        
        var hideDiv = document.createElement('div');
        hideDiv = $(hideDiv);
        hideDiv.id = 'hideDiv';  
        Event.observe(hideDiv, 'click', function(){AmAjaxLoginObj.hideMessage()} );
        container.appendChild(hideDiv); 
         
        var loginBox = document.createElement('div');
        loginBox = $(loginBox);
        loginBox.id = 'am-ajaxlogin';  
        container.appendChild(loginBox); 
        
        var closeBox = document.createElement('div');
        closeBox = $(closeBox);
        closeBox.id = 'am-ajaxlogin-close';  
        closeBox.innerHTML = '<span>X</span>';  
        loginBox.appendChild(closeBox);
        Event.observe(closeBox, 'click', function(){AmAjaxLoginObj.hideMessage()} );
				
				var titleBox = document.createElement('div');
				titleBox = $(titleBox);
				titleBox.id = 'am-ajaxlogin-title';
				titleBox.innerHTML = response.title;
				loginBox.appendChild(titleBox);
					
        if(response.error){
					var errorBox = document.createElement('div');
					errorBox = $(errorBox);
					errorBox.id = 'am-ajaxlogin-error';
					text = response.error.replace(new RegExp('&lt;', 'g'), "<");
					text = text.replace(new RegExp('&gt;', 'g'), ">");
					errorBox.innerHTML = text;
					loginBox.appendChild(errorBox);    
        }
        
        var messageBox = document.createElement('div');
        messageBox = $(messageBox);
        messageBox.id = 'am-ajaxlogin-message';
        messageBox.innerHTML = response.message;  
        loginBox.appendChild(messageBox);
				
			}
			if (response.redirect && response.is_error == "2") {
				if (response.redirect == "2") { // after add to wishlist or review
						if (response.show_review_popup == '1') {
							$('review-open-popup').addClassName('reviewloggedin').setAttribute('href', '#review-form-popup');
							jQuery('.reviewloggedin').fancybox({
									afterShow:function(){
											jQuery('#review-form-popup').customRadioCheckbox();
											// Add customer name to field your name in review popup
											jQuery('#nickname_field').val(response.customer_nickname);
									}
							});
							$('review-open-popup').stopObserving('click', loadLoginWithAjax);
							jQuery('.reviewloggedin').click();
						}
						
						if (response.is_adding_to_wishlist == '1') {
							//add to wishlist
							if (response.wishlist_url)
								ajaxCart.ajaxAddToWishlist(response.wishlist_url);
						}
					
					
				}else{
					if(response.redirect == "1") {
						location.reload();
					}
					else {
						window.location = response.redirect;
					}    
				}
			}
			
			try {
					eval(response.script);
			} catch(e) {
					console.debug(e);
			}   
    }, 
    
   hideMessage: function() {
        if($('am-ajaxlogin-container'))
            new Effect.Opacity('am-ajaxlogin-container', { from: 1.0, to: 0, duration: 0.2, afterFinish: function () {$('am-ajaxlogin-container').remove(); } });
   },
    
    /*end helper functions*/
    
    /*start login functions*/
    
    login: function() {
         var postData = this.addFormParam('amajaxlogin-login-form');
         if('' == postData) return false;
         this.hideMessage();
         this.showAnimation();
         var url = this.url.replace(this.url.substring(this.url.length-6, this.url.length), 'login');//    replace ajax to login
         new Ajax.Request(url, {
            method: 'post',
            postBody : postData,
            onComplete: function()
            {
               this.hideAnimation();
            }.bind(this),
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON()
                if (transport.responseText.isJSON() && response) {
                    this.hideAnimation();
										if(response.is_error == "1"){
											this.showMessage(response, true); // Show message wrong passwords
										}else{
											 this.showMessage(response, false);
										}
										if(response.is_error == "2"){
											this.updateHeader();
											if($$('body')[0].hasClassName('customer-account-index') || $$('body')[0].hasClassName('checkout-onepage-index')) {
													window.location.reload();
											}    
										}
                }
            }.bind(this),
            onFailure: function()
            {
                this.hideAnimation();
            }.bind(this)    
        });
        return false;    
    }, 
		
		loginFormId: function(formId) {
         var postData = this.addFormParam(formId);
         if('' == postData) return false;
         this.hideMessage();
         this.showAnimation();
         var url = this.url.replace(this.url.substring(this.url.length-6, this.url.length), 'login');//    replace ajax to login
         new Ajax.Request(url, {
            method: 'post',
            postBody : postData,
            onComplete: function()
            {
               this.hideAnimation();
            }.bind(this),
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON()
                if (transport.responseText.isJSON() && response) {
                    this.hideAnimation();
					this.updateHeader(); // Update header first
					//response.redirect = "2"; 	
										
                    if (response.is_error == "1") {
						this.showMessage(response, true); // Show message wrong passwords
					} else {
						customerIsLoggedIn = true; // need to update this var after logging in
						this.showMessage(response, false);
					}
                     if(response.is_error == "2"){
                        if($$('body')[0].hasClassName('customer-account-index') || $$('body')[0].hasClassName('checkout-onepage-index')) {
                            window.location.reload();
                        }    
                     }
                }
            }.bind(this),
            onFailure: function()
            {
                this.hideAnimation();
            }.bind(this)    
        });
        return false;    
    }, 
    
    logoutAjax: function() {
         this.showAnimation();
         var url = this.url.replace(this.url.substring(this.url.length-6, this.url.length), 'logout');//    replace ajax to login
         new Ajax.Request(url, {
            method: 'post',
            onComplete: function()
            {
               this.hideAnimation();
            }.bind(this),
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON()
                if (transport.responseText.isJSON() && response) {
                     this.hideAnimation();
                     this.showMessage(response, false);
                     if(response.is_error == "2"){
                        this.updateHeader();
                        if($$('body')[0].hasClassName('customer-account-index') || $$('body')[0].hasClassName('checkout-onepage-index')) {
                            window.location.reload();
                        }    
                     }
                }
            }.bind(this),
            onFailure: function()
            {
                this.hideAnimation();
            }.bind(this)    
        });
        return false;    
    },
    
    sendLoginAjax : function() {
			new Ajax.Request(this.url, {
					method: 'post',
					onCreate: function()
					{
						 this.showAnimation();
					}.bind(this),
					onComplete: function()
					{
						 this.hideAnimation();
					}.bind(this),
					onSuccess: function(transport) {
							var response = transport.responseText.evalJSON();
							if (transport.responseText.isJSON() && response) {
									 this.hideAnimation();
									 this.showMessage(response, true);
							}
					}.bind(this),
					onFailure: function()
					{
							this.hideAnimation();
					}.bind(this)    
			});
			return false;
    },
		
		sendLoginAjaxReview : function(params) {
			new Ajax.Request(this.url, {
					method: 'post',
					parameters: params,
					onCreate: function()
					{
						 this.showAnimation();
					}.bind(this),
					onComplete: function()
					{
						 this.hideAnimation();
					}.bind(this),
					onSuccess: function(transport) {
							var response = transport.responseText.evalJSON();
							if (transport.responseText.isJSON() && response) {
									 this.hideAnimation();
									 this.showMessage(response, true);
							}
					}.bind(this),
					onFailure: function()
					{
							this.hideAnimation();
					}.bind(this)    
			});
			return false;
    },
    
    /*end login functions*/
    
    /*start forgot functions*/
    
    forgotPassword: function(){
         this.hideMessage();
         this.showAnimation();
         var url = this.url.replace(this.url.substring(this.url.length-6, this.url.length), 'forgotPassword');//    replace ajax to forgotPassword
         new Ajax.Request(url, {
            method: 'post',
            onComplete: function()
            {
               this.hideAnimation();
            }.bind(this),
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON()
                if (transport.responseText.isJSON() && response) {
                     this.hideAnimation();
                     this.showMessage(response, true);
                }
            }.bind(this),
            onFailure: function()
            {
                this.hideAnimation();
            }.bind(this)    
        });
        return false;    
    },
    
    forgotPasswordSend: function(){
         var postData = this.addFormParam('amajaxlogin-form-validate');
         if('' == postData) return false;
         this.hideMessage();
         this.showAnimation();
         var url = this.url.replace(this.url.substring(this.url.length-6, this.url.length), 'forgotpasswordpost');//    replace ajax to forgotpasswordpost
         new Ajax.Request(url, {
            method: 'post',
            postBody : postData,
            onComplete: function()
            {
               this.hideAnimation();
            }.bind(this),
            onSuccess: function(transport) {
                var response = transport.responseText.evalJSON()
                if (transport.responseText.isJSON() && response) {
                     this.hideAnimation();
                     this.showMessage(response, true);
                }
            }.bind(this),
            onFailure: function()
            {
                this.hideAnimation();
            }.bind(this)    
        });
        return false;        
    },
    
    /*end forgot functions*/
    
    /*start facebook functions*/
    
    loginS:  function (img, tim) {
        if(!img || this.timer) return;
        if(tim)  this.timer = 1;
        var postData = img.getAttribute("data");
        var iframe = createIframe("am_ajax_login_iframe", postData, 0);
        this.hideMessage();
        this.showAnimation();    
        
        jQuery(iframe).load(function(event){
             AmAjaxLoginObj.hideAnimation();
             var iframe = Event.element(event);
	     if(iframe.contentDocument || iframe.contentWindow.document){
             	var innerDoc = iframe.contentDocument || iframe.contentWindow.document;
		} else{
			var innerDoc = iframe;
		}
             if(iframe && innerDoc.getElementsByTagName("plaintext").length) {
                 iframe = jQuery(iframe);
                 var answer = iframe.contents().find("plaintext").html();
                 response = answer.evalJSON();
                 AmAjaxLoginObj.showMessage(response, false);
                 if(response.is_error == "2"){
                     if($$('body')[0].hasClassName('customer-account-index') || $$('body')[0].hasClassName('checkout-onepage-index')) {
                         window.location.reload();
                     }
                         AmAjaxLoginObj.updateHeader();
                 }    
             } 
             
             iframe.remove();
        });
    },
        
    loginTw:  function (img) {
        if(!img) return;
        this.img = img
        var postData = img.getAttribute("dataTw");
        this.newWindow = window.open(postData,  'Sample','toolbar=no,width=590,height=590,left=0,top=0, status=no,scrollbars=no,resize=no');
        window.amtimer = setInterval(function() {
            if (AmAjaxLoginObj.newWindow.closed) {
                AmAjaxLoginObj.loginS(AmAjaxLoginObj.img, 1) ; 
                while ((amtimer--)>0) {
                    window.clearInterval(amtimer); 
                }
            }
        }
        , 200);
    }
      
}

function AmAjaxLoginLoad(buttonClass){
	$$(buttonClass).each(function(link){
		link.onclick = '';		
        Event.observe(link, 'click', loadLoginWithAjax);
        if($$('a[href*="customer/account/logout/"]').length == 0){
            $$('ul.links li a[href$="customer/account/"]').each(function(link){
                link.onclick = '';
                Event.observe(link, 'click', loadLoginWithAjax);
            });

            $$('ul.links li a[href$="wishlist/"]').each(function(link){
                link.onclick = '';
                Event.observe(link, 'click', loadLoginWithAjax);
            });
        }
	})
}

function reRunSomeScripts() {
	/* mini login */
  jQuery('.togglelogin').click(function(e){
    jQuery('.box-header-content').hide();
    if (!jQuery(this).hasClass('active')){
      jQuery('.toggle-header-content').removeClass('active');
      jQuery(this).addClass('active');
      jQuery('#header-mini-login').show();
      event.stopPropagation();
    }
    else{
      jQuery(this).removeClass('active');
      jQuery('#header-mini-login').hide();
    }
  });
  /* */

  jQuery('.toggle-minicart').click(function(e){
    jQuery('.box-header-content').hide();
    if (!jQuery(this).hasClass('active')){
      jQuery('.toggle-header-content').removeClass('active');
      jQuery(this).addClass('active');
      jQuery('#mini-cart-info').show();
      event.stopPropagation();
    }
    else{
      jQuery(this).removeClass('active');
      jQuery('#mini-cart-info').hide();
    }
  });

  jQuery('#loginClose').click(function(e){
    jQuery('.box-header-content').hide();
    jQuery('#header .top-link-account .toggle-header-content').removeClass('active');
    jQuery('#header-mini-login').hide();
  });

  jQuery('#cartClose').click(function(){
    jQuery('.box-header-content').hide();
    jQuery('#header .header-minicart .toggle-header-content').removeClass('active');
    jQuery('#header-mini-login').hide();
  });
}

function AmAjaxLogoutLoad(buttonClass){
	$$(buttonClass).each(function(link){
		link.onclick = '';
		Event.observe(link, 'click', loadLogoutWithAjax);   
	})
}

function loadLoginWithAjax(event) {
		var element = Event.element(event);
		
    event.preventDefault();
		if (element.hasClassName('write-review-popup')) {
			var params = {review:'1'};
			AmAjaxLoginObj.sendLoginAjaxReview(params);    
		}
		else {
			AmAjaxLoginObj.sendLoginAjax();    
		}
}

function loadLogoutWithAjax(event) {
     event.preventDefault();
     AmAjaxLoginObj.logoutAjax();    
}

document.observe("dom:loaded", function() {
    // AmAjaxLoginLoad('a[href*="customer/account/login/"]');    
    AmAjaxLogoutLoad('a[href*="customer/account/logout/"]');    
});


function createIframe(name, src, debug) {
  src = src || 'javascript:false'; 
  var tmpElem = document.createElement('div');

  tmpElem.innerHTML = '<iframe name="'+name+'" id="'+name+'" src="'+src+'">';
  var iframe = tmpElem.firstChild;

  if (!debug) {
    iframe.style.display = 'none';
  }
  document.body.appendChild(iframe);

  return iframe;
}

function sendRequestByEnter(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode == 13) {
        return AmAjaxLoginObj.login();
    }
    return true;
}



