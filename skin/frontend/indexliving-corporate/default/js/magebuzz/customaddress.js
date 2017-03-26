var eventMatchers = {
  'HTMLEvents': /^(?:load|unload|abort|error|select|change|submit|reset|focus|blur|resize|scroll)$/,
  'MouseEvents': /^(?:click|mouse(?:down|up|over|move|out))$/
}
var defaultOptions = {
  pointerX: 0,
  pointerY: 0,
  button: 0,
  ctrlKey: false,
  altKey: false,
  shiftKey: false,
  metaKey: false,
  bubbles: true,
  cancelable: true
}

Event.simulate = function(element, eventName) {
  var options = Object.extend(defaultOptions, arguments[2] || { });
  var oEvent, eventType = null;

  element = $(element);

  for (var name in eventMatchers) {
    if (eventMatchers[name].test(eventName)) { eventType = name; break; }
  }

  if (!eventType)
    throw new SyntaxError('Only HTMLEvents and MouseEvents interfaces are supported');

  if (document.createEvent) {
    oEvent = document.createEvent(eventType);
    if (eventType == 'HTMLEvents') {
      oEvent.initEvent(eventName, options.bubbles, options.cancelable);
    }
    else {
      oEvent.initMouseEvent(eventName, options.bubbles, options.cancelable, document.defaultView,
        options.button, options.pointerX, options.pointerY, options.pointerX, options.pointerY,
        options.ctrlKey, options.altKey, options.shiftKey, options.metaKey, options.button, element);
    }
    element.dispatchEvent(oEvent);
  }
  else {
    options.clientX = options.pointerX;
    options.clientY = options.pointerY;
    oEvent = Object.extend(document.createEventObject(), options);
    element.fireEvent('on' + eventName, oEvent);
  }
  return element;
}

Element.addMethods({ simulate: Event.simulate });


Validation.add('validate-zip-international','Please enter a valid zip code. For example 90602 or 90602-1234.', function(v) {
  return Validation.get('IsEmpty').test(v) || /(^\d{5}$)|(^\d{5}-\d{4}$)/.test(v);
});

Validation.add('validate-phoneNumber','Please enter a valid phone number.', function(v) {
  return Validation.get('IsEmpty').test(v)
    || /^[0-9\+]{1,}[0-9\-]{3,15}$/.test(v);
});

RegionUpdater.prototype.sortSelect = function($super){
  this.regionSelectEl.simulate('change');
}

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
    if (this.cities[this.regionEl.value]) {
      var i, option, city, def, isSelected = false;
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
          isSelected = true;
        }
      }

      if (!isSelected) {
        this.citySelectEl[0].selected = true;
      }

      if (this.cityTextEl) {
        this.cityTextEl.style.display = 'none';
      }
      this.citySelectEl.style.display = '';
    }
    else {
      this.citySelectEl.options.length = 1;
      if (this.cityTextEl) {
        this.cityTextEl.style.display = '';
        this.cityTextEl.value = "";
      }
      this.citySelectEl.style.display = 'none';
      Validation.reset(this.citySelectEl);
    }
  },

  updateCity: function() {
    var sIndex = this.citySelectEl.selectedIndex;
    if (this.citySelectEl.options[sIndex]) {
      this.cityTextEl.value = this.citySelectEl.options[sIndex].value;
    }
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

  updateSubdistrict: function() {
    var sIndex = this.subdistrictEl.selectedIndex;
    if (sIndex >= 0) {
      this.subdistrictTextEl.value = this.subdistrictEl.options[sIndex].value;
      if (this.citySelectEl.value == '' || this.citySelectEl.value == 'undefined') {
        this.citySelectEl[0].selected = true;
        return;
      }
      var selectedSubdistrict = this.subdistricts[this.citySelectEl.value][this.subdistrictTextEl.value];
      var zipcode = '';
      if (selectedSubdistrict != undefined) {
        zipcode = selectedSubdistrict.zipcode;
      }

      if (zipcode && zipcode != 'null') {
        this.updateZipcode(zipcode);
      }
    }
    else {
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
    }
    else if (order && order.billingAddressContainer && this.subdistrictEl.id == 'order-billing_address_subdistrict_id') {
      $('order-billing_address_postcode').value = zipcode;
      //sync shipping postcode by billing postcode
      if ($('order-shipping_as_billing') && $('order-shipping_as_billing').checked) {
        $('order-shipping_address_postcode').value = zipcode;
      }
    }
    else if (order && order.shippingAddressContainer && this.subdistrictEl.id == 'order-shipping_address_subdistrict_id') {
      $('order-shipping_address_postcode').value = zipcode;
    }
  }
}

ZipcodeUpdater = Class.create();
ZipcodeUpdater.prototype = {
  initialize: function(countryEl, regionTextEl, regionEl, cityTextEl, cityEl, subdistrictTextEl, subdistrictEl, zipEl, zipcodeData) {
    this.countryEl = $(countryEl);
    this.regionTextEl = $(regionTextEl);
    this.regionEl = $(regionEl);
    this.cityTextEl = $(cityTextEl);
    this.cityEl = $(cityEl);
    this.subdistrictTextEl = $(subdistrictTextEl);
    this.subdistrictEl = $(subdistrictEl);
    this.zipEl = $(zipEl);
    this.zipcodeData = zipcodeData;

    Event.observe(this.regionEl, 'change',  this.updateRegion.bind(this));
    Event.observe(this.cityEl, 'change',  this.updateCity.bind(this));
    Event.observe(this.zipEl, 'change', this.updateZipcode.bind(this));
  },

  updateZipcode: function() {
    if (this.regionEl && this.zipcodeData[this.zipEl.value]) {
      this.regionEl.value = this.zipcodeData[this.zipEl.value].region;
      this.regionEl.simulate('change');
    }
    else {
      // do anything
    }
  },

  updateRegion: function() {
    if (this.cityEl && this.zipcodeData[this.zipEl.value]) {
      this.cityEl.value = this.zipcodeData[this.zipEl.value].city;
      this.cityEl.simulate('change');
    }
    else {
      // do anything
    }
  },

  updateCity: function() {
    if(this.subdistrictEl){
      if (this.subdistrictEl && this.zipcodeData[this.zipEl.value] && this.zipcodeData[this.zipEl.value].is_more_than_one) {
        this.subdistrictEl.value = '';
      }
      else {
        this.subdistrictEl.value = this.zipcodeData[this.zipEl.value].subdistrict;
      }
      this.subdistrictEl.simulate('change');
    }
  }
}

Object.extend(Validation, {
  isVisible : function(elm) {
    if(!$(elm).visible()) return false;
    if (elm.up('.update_address')) {
      return true;
    }
    while(elm.tagName != 'BODY') {
      if(!$(elm).visible()) return false;
      elm = elm.parentNode;
    }
    return true;
  }
});

AddressValidator = Class.create();
AddressValidator.prototype = {
  initialize: function(config) {
    this.config = config;
  },

  validateAddress: function(addressId) {
    var formId = 'update_address_' + addressId;
    var form = new VarienForm(formId);
    if(form.validator.validate()){
      return true;
    }
    return false;
  }
}
var addressValidator = new AddressValidator();