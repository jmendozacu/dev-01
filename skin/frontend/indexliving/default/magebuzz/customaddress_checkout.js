CityUpdater = Class.create();
CityUpdater.prototype = {
	initialize: function(countryEl, regionEl, cityTextEl, citySelectEl, cities) {
		this.regionEl = $(regionEl);
		this.cityTextEl = $(cityTextEl);
		this.citySelectEl = $(citySelectEl);
		this.cities = cities;
		this.countryEl = $(countryEl);
		//if (this.citySelectEl.options.length<=1) {
		this.update();
		//	}

		this.regionEl.changeUpdater = this.update.bind(this);

		Event.observe(this.regionEl, 'change', this.update.bind(this));
		Event.observe(this.countryEl, 'change', this.update.bind(this));
		Event.observe(this.citySelectEl, 'change', this.updateCity.bind(this));
	},

	update: function() {
		// 551: Surat Thani
		if(jQuery('.checkout-onepage-index')){
			if(this.regionEl.value == 551){
				new Ajax.Request(checkShippingType, {
					method: 'post',
					onComplete: function(response) {
						if(response.responseJSON.ilm && response.responseJSON.thaipost){
							jQuery('#amscheckout-submit').attr("disabled", true);
							$('outside_service_zone').style.display = 'block';
							jQuery('#billing_as_shipping_yes').attr("disabled", true);
							jQuery('#billing_as_shipping_no').attr("disabled", true);
							jQuery('#billing-form-show').hide();
						}
						else{
							if (response.responseJSON.ilm) {
								jQuery('#amscheckout-submit').attr("disabled", true);
								$('outside_service_zone').style.display = 'block';
								jQuery('#billing_as_shipping_yes').attr("disabled", true);
								jQuery('#billing_as_shipping_no').attr("disabled", true);
								jQuery('#billing-form-show').hide();
							}
							if (response.responseJSON.thaipost) {
								$('notice_service').style.display = 'block';
							}
						}
					}.bind(this)
				});
			}
			else {
				jQuery('#amscheckout-submit').attr("disabled", false);
				if($('outside_service_zone')){
					$('outside_service_zone').style.display = 'none';
				}
				jQuery('#billing_as_shipping_yes').attr("disabled", false);
				jQuery('#billing_as_shipping_no').attr("disabled", false);

				if($('notice_service')){
					$('notice_service').hide();
				}
			}
			if(this.regionEl.value == 515 || this.regionEl.value == 509 || this.regionEl.value == 530){
				new Ajax.Request(checkShippingType, {
					method: 'post',
					onComplete: function(response) {
						if(response.responseJSON.ilm && response.responseJSON.thaipost){
							jQuery('#amscheckout-submit').attr("disabled", true);
							$('outside_service_zone').style.display = 'block';
							jQuery('#billing_as_shipping_yes').attr("disabled", true);
							jQuery('#billing_as_shipping_no').attr("disabled", true);
							jQuery('#billing-form-show').hide();
						}
						else{
							if (response.responseJSON.ilm) {
								jQuery('#amscheckout-submit').attr("disabled", true);
								$('outside_service_zone').style.display = 'block';
								jQuery('#billing_as_shipping_yes').attr("disabled", true);
								jQuery('#billing_as_shipping_no').attr("disabled", true);
								jQuery('#billing-form-show').hide();
							}
							//if (response.responseJSON.thaipost) {
							//	$('notice_service').style.display = 'block';
							//}
						}
					}.bind(this)
				});
			}
			else {
				jQuery('#amscheckout-submit').attr("disabled", false);
				if($('outside_service_zone')){
					$('outside_service_zone').style.display = 'none';
				}
				jQuery('#billing_as_shipping_yes').attr("disabled", false);
				jQuery('#billing_as_shipping_no').attr("disabled", false);
			}
		}

		if (this.cities[this.regionEl.value]) {
			var i, option, city, def;
			def = this.citySelectEl.getAttribute('defaultValue');
			if (this.cityTextEl) {
				if (!def) {
					def = this.cityTextEl.value.toLowerCase();
				}
				////need to comment this to avoid issue when saving address without touching city field
				//this.cityTextEl.value = '';
			}

			this.citySelectEl.options.length = 1;
			for (cityId in this.cities[this.regionEl.value]) {
				city = this.cities[this.regionEl.value][cityId];

				option = document.createElement('OPTION');
				option.value = city.code;
				option.text = city.name.stripTags();
				option.title = city.name;

				if (this.citySelectEl.options.add) {
					this.citySelectEl.options.add(option);
				} else {
					this.citySelectEl.appendChild(option);
				}

				if (cityId==def || (city.name && city.name==def) ||
					(city.name && city.code.toLowerCase()==def)
				) {
					this.citySelectEl.value = city.code;
				}
			}

			// Sort Alphabetize
			this.sortAlphabetize();

			if (this.cityTextEl) {
				this.cityTextEl.style.display = 'none';
			}
			this.citySelectEl.style.display = '';
		}
		else {
			this.citySelectEl.options.length = 1;
			if (this.cityTextEl) {
				this.cityTextEl.style.display = '';
			}
			this.citySelectEl.style.display = 'none';
			Validation.reset(this.citySelectEl);
		}
	},

	sortAlphabetize: function () {
		elem = this.citySelectEl;
		var tmpAry = new Array();
		var currentVal = $(elem).value;
		for (var i=0;i<$(elem).options.length;i++) {
			if (i == 0) continue;
			tmpAry[i-1] = new Array();
			tmpAry[i-1][0] = $(elem).options[i].text;
			tmpAry[i-1][1] = $(elem).options[i].value;
		}
		tmpAry.sort();
		while ($(elem).options.length > 0) {
			$(elem).options[0] = null;
		}
		for (var i=1;i<=tmpAry.length;i++) {
			var op = new Option(tmpAry[i-1][0], tmpAry[i-1][1]);
			$(elem).options[i] = op;
		}
		$(elem).value = currentVal;
		return;
	},

	updateCity: function() {
		var sIndex = this.citySelectEl.selectedIndex;
		this.cityTextEl.value = this.citySelectEl.options[sIndex].value;
	}
}

SubdistrictUpdater = Class.create();
SubdistrictUpdater.prototype = {
	initialize: function(countryEl, regionEl, citySelectEl, subdistrictTextEl, subdistrictEl, zipEl, subdistricts) {
		this.countryEl = $(countryEl);
		this.regionEl = $(regionEl);
		this.citySelectEl = $(citySelectEl);
		this.subdistrictTextEl = $(subdistrictTextEl);
		this.subdistrictEl = $(subdistrictEl);
		this.zipEl = $(zipEl);
		this.subdistricts = subdistricts;
		//if (this.citySelectEl.options.length<=1) {
		this.update();
		//	}

		this.citySelectEl.changeUpdater = this.update.bind(this);
		Event.observe(this.regionEl, 'change', this.update.bind(this));
		Event.observe(this.countryEl, 'change', this.update.bind(this));
		Event.observe(this.citySelectEl, 'change', this.update.bind(this));
		Event.observe(this.subdistrictEl, 'change', this.updateSubdistrict.bind(this));
	},

	update: function() {
		if (this.subdistricts[this.citySelectEl.value]) {
			var i, option, subdistrict, def;
			def = this.subdistrictEl.getAttribute('defaultValue');
			if (this.subdistrictTextEl) {
				if (!def) {
					def = this.subdistrictTextEl.value.toLowerCase();
				}
			}

			this.subdistrictEl.options.length = 1;
			for (subdistrictId in this.subdistricts[this.citySelectEl.value]) {
				subdistrict = this.subdistricts[this.citySelectEl.value][subdistrictId];

				option = document.createElement('OPTION');
				option.value = subdistrict.code;
				option.text = subdistrict.name.stripTags();
				option.title = subdistrict.name;

				if (this.subdistrictEl.options.add) {
					this.subdistrictEl.options.add(option);
				} else {
					this.subdistrictEl.appendChild(option);
				}

				if (subdistrictId==def || (subdistrict.name && subdistrict.name==def) ||
					(subdistrict.name && subdistrict.code.toLowerCase()==def)
				) {
					this.subdistrictEl.value = subdistrict.code;
				}
			}

			// Sort Alphabetize
			this.sortAlphabetize();

			if (this.subdistrictTextEl) {
				this.subdistrictTextEl.style.display = 'none';
			}
			this.subdistrictEl.style.display = '';
		}
		else {
			this.subdistrictEl.options.length = 1;
			if (this.subdistrictTextEl) {
				this.subdistrictTextEl.style.display = '';
			}
			this.subdistrictEl.style.display = 'none';
			Validation.reset(this.subdistrictEl);
		}
	},

	sortAlphabetize: function () {
		elem = this.subdistrictEl;
		var tmpAry = new Array();
		var currentVal = $(elem).value;
		for (var i=0;i<$(elem).options.length;i++) {
			if (i == 0) continue;
			tmpAry[i-1] = new Array();
			tmpAry[i-1][0] = $(elem).options[i].text;
			tmpAry[i-1][1] = $(elem).options[i].value;
		}
		tmpAry.sort();
		while ($(elem).options.length > 0) {
			$(elem).options[0] = null;
		}
		for (var i=1;i<=tmpAry.length;i++) {
			var op = new Option(tmpAry[i-1][0], tmpAry[i-1][1]);
			$(elem).options[i] = op;
		}
		$(elem).value = currentVal;
		return;
	},

	updateSubdistrict: function() {
		var sIndex = this.subdistrictEl.selectedIndex;
		this.subdistrictTextEl.value = this.subdistrictEl.options[sIndex].value;

		if (sIndex) {
			var selectedSubdistrict = this.subdistricts[this.citySelectEl.value][this.subdistrictTextEl.value];
			var zipcode = '';
			if (selectedSubdistrict != undefined) {
				zipcode = selectedSubdistrict.zipcode;
			}

			if (zipcode && zipcode != 'null') {
				this.updateZipcode(zipcode);
				//this.zipEl.value = zipcode;
			}
			else {
				this.updateZipcode('');
				//this.zipEl.value = '';
			}
		}
		else {
			//this.zipEl.value = '';
			this.updateZipcode('');
			this.subdistrictTextEl.value = '';
		}

	},

	/*
	 * this function make sure that we can update zipcode when creating order in backend
	 * prefer to find better solution later
	 */
	updateZipcode: function(zipcode) {
		if (this.zipEl != null) {
			this.zipEl.value = zipcode;
			this.zipEl.focus();
		}
		else if (order && order.billingAddressContainer && this.subdistrictEl.id == 'order-billing_address_subdistrict_id') {
			$('order-billing_address_postcode').value = zipcode;
			if($('order-shipping_as_billing').checked){
				$('order-shipping_address_postcode').value = zipcode;
			}
		}
		else if (order && order.shippingAddressContainer && this.subdistrictEl.id == 'order-shipping_address_subdistrict_id') {
			$('order-shipping_address_postcode').value = zipcode;
		}
	}
}
