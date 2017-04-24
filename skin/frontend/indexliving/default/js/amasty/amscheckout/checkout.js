var amContentWin = null
var amQtyClick = []
var amQtyClickTimeout = 2000;
var checkoutRunning = false;
var amcheckouttimer = null;
var amLoadingProcesses = [];

function applyCoupon(config){
    if (config.html && config.html.coupon){
        
        if (config.html.coupon.output){
            $('checkout-coupon').update(config.html.coupon.output);
            cartEvents();
        }

        if ($('coupon_lookup'))
            $('coupon_lookup').innerHTML = config.html.coupon.message;
    }
    
    hideLoading();
}

function createAccountClick(){
    var ch = $('billing:create_account');
    var hidden = $('billing:method');
    $$(".amscheckout-password").each(function(div){
        if (ch.checked) { 
            div.show()
            hidden.value = 'register';
        }
        else {
            div.hide();
            hidden.value = 'guest';
        } 
    });
}



function showLoading(){
    $("amscheckout-processing").show();

    $("amscheckout-loading").show();
    
    $$("#shopping-cart-table input,#shopping-cart-table button").each(function(input){
        input.setAttribute("readonly", "readonly");
    })

    $$("#shopping-cart-table button").each(function(input){
        input.setAttribute("disabled", "disabled");
    })

}

function hideLoading(){
    $("amscheckout-processing").hide();
    $("amscheckout-loading").hide();

    $$("#shopping-cart-table input,#shopping-cart-table button").each(function(input){
        input.removeAttribute("readonly");
        input.removeAttribute("disabled");
    })
}

function initUpdatableFieldEvent(field, section){
    $$("[id='" + field + "']").each(function(input){
        var availableEvents = input.tagName.toLowerCase() == 'select' ? ['change'] : ['blur', 'change'];

        var updateInput = function(input){
            if (amcheckouttimer != null){
                clearTimeout(amcheckouttimer);
            }

            if (input.tagName.toLowerCase() == 'input'){
                  if (input.getAttribute('prev_val') != input.value){
                      updateCheckout(section);
                      input.setAttribute('prev_val', input.value);
                  }

            } else {
                updateCheckout(section);
            }
        }

        for(var ind in availableEvents){
            if (typeof(availableEvents[ind]) == 'string'){
                var observedEvent = availableEvents[ind];
                input.observe(observedEvent, function(){
                    updateInput(this);
                });
            }

        }


//                if (event == 'blur'){
            input.observe('keyup', function(){
                amcheckouttimer = setTimeout(function(){
                    updateInput(input);
                }, 2000);
            })

//                }

        if ($('billing:use_for_shipping_yes')) {
            $('billing:use_for_shipping_yes').observe("click", function(){
                updateCheckout('billing');
            });
        }
        
        if ($('billing:use_for_shipping_no')) {
            $('billing:use_for_shipping_no').observe("click", function(){
                updateCheckout('shipping');
            });
        }
				
				if ($('billing_as_shipping_yes')) {
            $('billing_as_shipping_yes').observe("click", function(){
                updateCheckout('shipping');
            });
        }
				
				if ($('billing_as_shipping_no')) {
            $('billing_as_shipping_no').observe("click", function(){
                updateCheckout('billing');
            });
        }
        

        if (input.tagName.toLowerCase() == 'input'){
            input.setAttribute('prev_val', input.value);
        }
    })

}

function ajaxUpdate(url, after, form){
    if (!form) {
        form = $('amscheckout-onepage');
    }   
    
    var params = form.tagName ? form.serialize(true) : form;
    
//    showLoading();
    
    params.isAjax = 1;
    return new Ajax.Request(url, {
        method: 'post',
        parameters: params,
        onSuccess: function(response) {
            var config = response.responseText.evalJSON();

            if (config.html){
                if (config.html.shipping_method && $('checkout-shipping-method-load'))
                    $('checkout-shipping-method-load').update(config.html.shipping_method);

                if (config.html.payment_method && $('co-payment-form-update')){
                    $('co-payment-form-update').update(config.html.payment_method);

                    if (payment.initWhatIsCvvListeners)
                        payment.initWhatIsCvvListeners();
                }

                if (config.html.review && $('checkout-review-load')){
                    $('checkout-review-load').update(config.html.review);
                    reviewEvents();
                }

                if (config.html.cart && $('amscheckout-cart')){
                    $('amscheckout-cart').update(config.html.cart);
                    cartEvents();
                }
            }

            if (typeof(after) == 'function'){
                after(config)
            }

//            hideLoading(); 
					var myNav = navigator.userAgent.toLowerCase();
					var isIE = (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
					if(isIE == 7 || isIE == 8){
						return;
					}else{
						jQuery('#co-shipping-method-form').customRadioCheckbox();
						jQuery('#co-payment-form-update').customRadioCheckbox();
					}
        },
        on403: function(){
            document.location.reload();
        } 
    });
}

function reviewEvents(){

    $$('[id="review-qty"]').each(function(input){

        function inputQtyHandler(){
            for(var ind in amQtyClick){
                var t = amQtyClick[ind];
                if (typeof(t) != 'function'){
                    clearTimeout(t);
                }
            }

            if (this.getAttribute('prev_val') != this.value){
                updateReview();
                this.setAttribute('prev_val', this.value);
            }
        }

        input.observe("blur", inputQtyHandler);

        input.observe("keyup", function(){
            amQtyClick.push(setTimeout(inputQtyHandler.bind(this), amQtyClickTimeout))
        })
    });
}

function amLoadingProcess(section, pendingRequest, hideLoading){
    
    var process = {
        'pendingRequest': pendingRequest,
        'loadingProcess': this
    };
    
    this.showSmallProcessing = function(section, show){
        
        if (hideLoading !== true) {
            var processing = $(section + "-processing");
            var number = $(section + "-number");
            if (processing && number){
                if (show){
                    number.hide();
                    processing.show();
                } else {
                    number.show();
                    processing.hide();
                }

            }
        }        
    }
    
    
    this.before = function(){

        if (amLoadingProcesses.length > 0){
            for(var ind in amLoadingProcesses){
                if (typeof(amLoadingProcesses[ind]) !== 'function'){
                    amLoadingProcesses[ind].pendingRequest.transport.abort();
                    amLoadingProcesses.splice(amLoadingProcesses.indexOf(amLoadingProcesses[ind]), 1);
                }
            }
        }

        amLoadingProcesses.push(process);

//        $("amscheckout-processing").show();

        if (section != "shipping_method" && section != "payment_method") {
            var shippingMethod = $("amscheckout-loading-shipping-method");
            if (shippingMethod) {
                shippingMethod.show();
            }
            
            this.showSmallProcessing("shipping-method", true);
            this.showSmallProcessing("review", true);
        }

        if (section != "payment_method") {
            var paymentMethod = $("amscheckout-loading-payment-method");
            if (paymentMethod) {
                paymentMethod.show();
            }
            
            this.showSmallProcessing("payment-method", true);
            this.showSmallProcessing("review", true);
        }
        
        if (section == "payment_method") {
            this.showSmallProcessing("review", true);
        }

        $('amscheckout-submit').disabled = true;
    };

    this.after = function(){

        amLoadingProcesses.splice(amLoadingProcesses.indexOf(process), 1);

        if (amLoadingProcesses.length == 0) {
            if (section != "shipping_method" && section != "payment_method") {
                var shippingMethod = $("amscheckout-loading-shipping-method");
                if (shippingMethod) {
                    shippingMethod.hide();
                }
                
                this.showSmallProcessing("shipping-method", false);
                this.showSmallProcessing("review", false);
            }

            if (section != "payment_method") {
                var paymentMethod = $("amscheckout-loading-payment-method");
                if (paymentMethod) {
                    paymentMethod.hide();
                }
                
                this.showSmallProcessing("payment-method", false);
                this.showSmallProcessing("review", false);
            }
            
            if (section == "payment_method") {
                this.showSmallProcessing("review", false);
            }

            $('amscheckout-submit').disabled = false;
            checkShipping(this.value);
//            $("amscheckout-processing").hide();
        }
    }
}
function checkShipping(address_id){
    if(!address_id){
        if ($('shipping-address-select')) {
            var address_id = jQuery('#shipping-address-select').find(":selected").val();
        }
    }
    //var params = this.value;
    new Ajax.Request(checkShippingType, {
        method: 'post',
        parameters: {
            address_id: address_id
        },
        onComplete: function (response) {
            if(response.responseJSON.surat_thani || response.responseJSON.pattani || response.responseJSON.nara_thiwat || response.responseJSON.yala) {
                if (response.responseJSON.surat_thani) {
                    //if (response.responseJSON.ilm && response.responseJSON.thaipost) {
                    //
                    //    $('outside_service').style.display = 'block';
                    //    jQuery('#billing_as_shipping_yes').attr("disabled", true);
                    //    jQuery('#billing_as_shipping_no').attr("disabled", true);
                    //    jQuery('#amscheckout-submit').attr("disabled", true);
                    //}
                    //else {
                        if (response.responseJSON.ilm) {
                            $('notice_service').style.display = 'block';
                            //$('outside_service').style.display = 'block';
                            //jQuery('#billing_as_shipping_yes').attr("disabled", true);
                            //jQuery('#billing_as_shipping_no').attr("disabled", true);
                            //jQuery('#amscheckout-submit').attr("disabled", true);
                        }
                        //if (response.responseJSON.thaipost) {
                        //    $('notice_service').style.display = 'block';
                        //}
                    //}
                }
                if (response.responseJSON.pattani || response.responseJSON.nara_thiwat || response.responseJSON.yala) {
                    if (response.responseJSON.ilm && response.responseJSON.thaipost) {

                        $('outside_service').style.display = 'block';
                        jQuery('#billing_as_shipping_yes').attr("disabled", true);
                        jQuery('#billing_as_shipping_no').attr("disabled", true);
                        jQuery('#amscheckout-submit').attr("disabled", true);
                    }
                    else {
                        if (response.responseJSON.ilm) {

                            $('outside_service').style.display = 'block';
                            jQuery('#billing_as_shipping_yes').attr("disabled", true);
                            jQuery('#billing_as_shipping_no').attr("disabled", true);
                            jQuery('#amscheckout-submit').attr("disabled", true);
                        }
                    }
                }
            }
            else{
                if(response.responseJSON.ilm){
                    jQuery('#amscheckout-submit').attr("disabled", true);
                }
                else{
                    jQuery('#amscheckout-submit').attr("disabled", false);
                }
                jQuery('#billing_as_shipping_yes').attr("disabled", false);
                jQuery('#billing_as_shipping_no').attr("disabled", false);
                if($('outside_service')){
                    $('outside_service').style.display = 'none';
                }
                if($('notice_service') && !response.responseJSON.ilm){
                    $('notice_service').hide();
                }
            }
        }.bind(this)
    });
}