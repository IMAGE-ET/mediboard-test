DateFormat = Class.create();
Object.extend(DateFormat, {
	MONTH_NAMES: ['January','February','March','April','May','June','July','August','September','October','November','December','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
	DAY_NAMES: ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
	LZ: function(x) {return(x<0||x>9?"":"0")+x},
	compareDates: function(date1,dateformat1,date2,dateformat2) {
		var d1=DateFormat.parseFormat(date1,dateformat1);
		var d2=DateFormat.parseFormat(date2,dateformat2);
		if (d1==0 || d2==0) return -1;
		else if (d1 > d2) return 1;
		return 0;
	},
	format: function(date,format) {
    if (!date) return;
    
		format += "";
		var result = "", 
			i = 0, 
			c="",
			token="",
			y=date.getFullYear()+"",
			M=date.getMonth()+1,
			d=date.getDate(),
			E=date.getDay(),
			H=date.getHours(),
			m=date.getMinutes(),
			s=date.getSeconds(),
			h=(H == 0 ? 12 : (H>12 ? H-12 : H));

		// Convert real date parts into formatted versions
		var value = {
			y:   y+'',
			yy:  y.substring(2,4),
			yyyy:y,
			M:   M,
			MM:  DateFormat.LZ(M),
			MMM: DateFormat.MONTH_NAMES[M-1],
			NNN: DateFormat.MONTH_NAMES[M+11],
			d:   d,
			dd:  DateFormat.LZ(d),
			E:   DateFormat.DAY_NAMES[E+7],
			EE:  DateFormat.DAY_NAMES[E],
			H:   H,
			HH:  DateFormat.LZ(H),
			h:   h,
			hh:  DateFormat.LZ(h),
			K:   H % 12,
			KK:  DateFormat.LZ(H % 12),
			k:   H + 1,
			kk:  DateFormat.LZ(H + 1),
			a:   H > 11 ? 'PM' : 'AM',
			m:   m,
			mm:  DateFormat.LZ(m),
			s:   s,
			ss:  DateFormat.LZ(s)
		};

		while (i < format.length) {
			c = format.charAt(i);
			token = "";
			while ((format.charAt(i)==c) && (i < format.length))
				token += format.charAt(i++);
			if (value[token] != null) result += value[token];
			else result += token;
		}
		return result;
	},
	_isInteger: function(val) {
		return parseInt(val) == val;
	},
	_getInt: function(str,i,minlength,maxlength) {
		// A possible replacement of this function, to be tested
		var sub = str.substring(i, i+maxlength);
		if (!sub) return null;
		return sub+'';

		for (var x=maxlength; x>=minlength; x--) {
			var token=str.substring(i,i+x);
			if (token.length < minlength) return null;
			if (DateFormat._isInteger(token)) return token;
		}
		return null;
	},
	parseFormat: function(val,format) {
		val=val+"";
		format=format+"";
		var i_val=0;
		var i_format=0;
		var c="";
		var token="";
		var token2="";
		var x,y;
		var now=new Date();
		var year=now.getYear();
		var month=now.getMonth()+1;
		var date=1;
		var hh=now.getHours();
		var mm=now.getMinutes();
		var ss=now.getSeconds();
		var ampm="";

		while (i_format < format.length) {
			// Get next token from format string
			c=format.charAt(i_format);
			token="";
      
			while ((format.charAt(i_format)==c) && (i_format < format.length))
				token += format.charAt(i_format++);

			// Extract contents of value based on format token
			if (token=="yyyy" || token=="yy" || token=="y") {
				if (token=="yyyy") x=4;y=4;
				if (token=="yy") x=2;y=2;
				if (token=="y") x=2;y=4;
				year=DateFormat._getInt(val,i_val,x,y);
				if (year==null) return 0;
				i_val += year.length;
				if (year.length==2) {
					if (year > 70) year=1900+(year-0);
					else year=2000+(year-0);
				}
			} else if (token=="MMM"||token=="NNN") {
				month=0;
				for (var i=0; i<DateFormat.MONTH_NAMES.length; i++) {
					var month_name=DateFormat.MONTH_NAMES[i];
					if (val.substring(i_val,i_val+month_name.length).toLowerCase()==month_name.toLowerCase()) {
						if (token=="MMM"||(token=="NNN"&&i>11)) {
							month=i+1;
							if (month>12) month -= 12;
							i_val += month_name.length;
							break;
						}
					}
				}
				if ((month < 1)||(month>12)) return 0;
			} else if (token=="EE"||token=="E") {
				for (var i=0; i<DateFormat.DAY_NAMES.length; i++) {
					var day_name=DateFormat.DAY_NAMES[i];
					if (val.substring(i_val,i_val+day_name.length).toLowerCase()==day_name.toLowerCase()) {
						i_val += day_name.length;
						break;
					}
				}
			} else if (token=="MM"||token=="M") {
				month=DateFormat._getInt(val,i_val,token.length,2);
				if(month==null||(month<1)||(month>12)) return 0;
				i_val+=month.length;
			} else if (token=="dd"||token=="d") {
				date=DateFormat._getInt(val,i_val,token.length,2);
				if(date==null||(date<1)||(date>31)) return 0;
				i_val+=date.length;
			} else if (token=="hh"||token=="h") {
				hh=DateFormat._getInt(val,i_val,token.length,2);
				if(hh==null||(hh<1)||(hh>12)) return 0;
				i_val+=hh.length;
			} else if (token=="HH"||token=="H") {
				hh=DateFormat._getInt(val,i_val,token.length,2);
				if(hh==null||(hh<0)||(hh>23)) return 0;
				i_val+=hh.length;
			} else if (token=="KK"||token=="K") {
				hh=DateFormat._getInt(val,i_val,token.length,2);
				if(hh==null||(hh<0)||(hh>11)) return 0;
				i_val+=hh.length;
			} else if (token=="kk"||token=="k") {
				hh=DateFormat._getInt(val,i_val,token.length,2);
				if(hh==null||(hh<1)||(hh>24)) return 0;
				i_val+=hh.length;hh--;
			} else if (token=="mm"||token=="m") {
				mm=DateFormat._getInt(val,i_val,token.length,2);
				if(mm==null||(mm<0)||(mm>59)) return 0;
				i_val+=mm.length;
			} else if (token=="ss"||token=="s") {
				ss=DateFormat._getInt(val,i_val,token.length,2);
				if(ss==null||(ss<0)||(ss>59)) return 0;
				i_val+=ss.length;
			} else if (token=="a") {
				if (val.substring(i_val,i_val+2).toLowerCase()=="am") ampm="AM";
				else if (val.substring(i_val,i_val+2).toLowerCase()=="pm") ampm="PM";
				else return 0;
				i_val+=2;
			} else {
				if (val.substring(i_val,i_val+token.length)!=token) return 0;
				else i_val+=token.length;
			}
		}
		// If there are any trailing characters left in the value, it doesn't match
		if (i_val != val.length) return 0;
		// Is date valid for month?
		if (month==2) {
			// Check for leap year
			if (((year%4==0)&&(year%100 != 0)) || (year%400==0)) { // leap year
				if (date > 29) return 0;
			} else if (date > 28) {
				return 0;
			}
		}
		if ((month==4)||(month==6)||(month==9)||(month==11))
			if (date > 30) return 0;
		// Correct hours value
		if (hh<12 && ampm=="PM") hh=hh-0+12;
		else if (hh>11 && ampm=="AM") hh-=12;
		return new Date(year,month-1,date,hh,mm,ss);
	},
	parse: function(val, format) {
		if (format) {
			return DateFormat.parseFormat(val, format);
		} else {
			var preferEuro = (arguments.length == 2) ? arguments[1] : false;
			var generalFormats = ['y-M-d','MMM d, y','MMM d,y','y-MMM-d','d-MMM-y','MMM d'];
			var monthFirst     = ['M/d/y','M-d-y','M.d.y','MMM-d','M/d','M-d'];
			var dateFirst      = ['d/M/y','d-M-y','d.M.y','d-MMM','d/M','d-M'];
			var checkList=[generalFormats, preferEuro?dateFirst:monthFirst, preferEuro?monthFirst:dateFirst];
			var d=null;
			for (var i=0; i<checkList.length; i++) {
				var l=checkList[i];
				for (var j=0; j<l.length; j++) {
					d = DateFormat.parseFormat(val,l[j]);
					if (d!=0) return new Date(d);
				}
			}
			return null;
		}
	}
});

DateFormat.prototype = {
	initialize: function(format) { this.format = format; },
	parse: function(value) { return DateFormat.parseFormat(value, this.format); },
	format: function(value) { return DateFormat.format(value, this.format); }
};

Object.extend(Date.prototype, {
  format: function(format) {
  	return DateFormat.format(this, format);
  },
  getWeekNumber: function() {
  	var d = new Date(this.getFullYear(), this.getMonth(), this.getDate(), 0, 0, 0);
  	d.setDate(d.getDate() - (d.getDay() + 6) % 7 + 3); // Nearest Thu
  	var ms = d.valueOf(); // GMT
  	d.setMonth(0);
  	d.setDate(4); // Thu in Week 1
  	return Math.round((ms - d.valueOf()) / (7 * 864e5)) + 1;
  }
});


var Calendar = {
  // This function is bound to date specification
  dateProperties: function(date, dates) {
    if (!date) return {};
    var properties = {}, 
        sDate = date.toDATE();
  
    if (dates.limit.start && dates.limit.start > sDate ||
        dates.limit.stop && dates.limit.stop < sDate) {
      properties.disabled = true;
    }
  
    if ((dates.current.start || dates.current.stop) && 
       !(dates.current.start && dates.current.start > sDate || dates.current.stop && dates.current.stop < sDate)) {
      properties.className = "active";
    }
    
    if (dates.spots.include(sDate)) {
      properties.label = "Date";
    }
    return properties;
  },

  prepareDates: function(dates) {
    dates.current.start = Calendar.prepareDate(dates.current.start);
    dates.current.stop  = Calendar.prepareDate(dates.current.stop);
    dates.limit.start = Calendar.prepareDate(dates.limit.start);
    dates.limit.stop  = Calendar.prepareDate(dates.limit.stop);
    dates.spots = dates.spots.map(Calendar.prepareDate);
  },
  
  prepareDate: function(date) {
    if (!date) return null;
    return Date.isDATETIME(date) ? Date.fromDATETIME(date).toDATE() : date;
  },
  
  regField: function(element, dates, options){
    if (!$(element)) return;

    if (dates) {
      dates.spots = $A(dates.spots);
    }

    dates = Object.extend({
      current: {
        start: null,
        stop: null
      },
      limit: {
        start: null,
        stop: null
      },
      spots: []
    }, dates);

    Calendar.prepareDates(dates);
    
    // Default options
    options = Object.extend({
      datePicker: true,
      timePicker: false,
      altElement: element,
      altFormat: 'yyyy-MM-dd',
      icon: "images/icons/calendar.gif",
      locale: "fr_FR", 
      timePickerAdjacent: true, 
      use24hrs: true,
      container: $(document.body),
      dateProperties: function(date){return Calendar.dateProperties(date, dates)}
    }, options || {});
    
    var elementView;
    
    if (!(elementView = $(element.form.elements[element.name+'_da']))) {
      elementView = new Element('input', {type: 'text', disabled: true}).addClassName(element.className || 'date');
      element.insert({before: elementView});
    }
    
    if (element.hasClassName('dateTime')) {
      options.timePicker = true;
      options.altFormat = 'yyyy-MM-dd HH:mm:ss';
    }
    else if (element.hasClassName('time')) {
      options.timePicker = true;
      options.datePicker = false;
      options.altFormat = 'HH:mm:ss';
      options.icon = "images/icons/time.png";
    }
    
    var datepicker = new Control.DatePicker(elementView, options);
    
    if (options.noView) {
      datepicker.element.setStyle({width: 0, height: 0, border: 'none'});
      if (datepicker.icon) datepicker.icon.style.position = 'relative';
    }
    
    // We update the view
    if (element.value && !elementView.value) {
      var date = DateFormat.parse(element.value, datepicker.options.altFormat);
      elementView.value = DateFormat.format(date, datepicker.options.currentFormat);
    }
    
    if (datepicker.icon && !options.noView) {
      if (!element.hasClassName('notNull')) {
        var cancelIcon = new Element("img", {src: "images/icons/cancel.png", width: 10, height: 10, title: "Vider"});
        var dim = datepicker.icon.getDimensions();
        cancelIcon.setStyle({
          position: "absolute",
          cursor: "pointer",
          right: "14px", 
          top: "5px"
        }).observe("click", function(){
          $V(datepicker.element, "");
          $V(datepicker.altElement, "");
        });
        
        var overlay = new Element('div');
        overlay.addClassName('datepickerOverlay '+datepicker.element.className)
               .setStyle({
                 height: '1em'
               })
               .observe('mouseover', function(e){e.stop();cancelIcon.show()})
               .observe('mouseout', function(e){e.stop();cancelIcon.hide()});
               
        datepicker.element.insert({after: overlay});
        overlay.insert(cancelIcon.hide());
      }
    
      datepicker.icon.observe("click", function(){
        (function(){$(datepicker.datepicker.element).unoverflow()}).defer();
      });
    }
    datepicker.element.observe('change', function(){oInput.fire("ui:change")});
  }
};


/**
 * Durations expressed in milliseconds
 */
Object.extend(Date, {
  // Exact durations
  millisecond: 1,
  second: 1000,
  minute: 60 * 1000,
  hour: 60 * 60 * 1000,
  day: 24 * 60 * 60 * 1000,
  week: 7 * 24 * 60 * 60 * 1000,
  
  // Approximative durations
  month: 30 * 24 * 60 * 60 * 1000,
  year: 365.2425 * 24 * 60 * 60 * 1000,

  isDATE: function(sDate) {
    var re = /^\d{4}-\d{2}-\d{2}$/;
    return re.match(sDate);
  },
  isDATETIME: function(sDateTime) {
    var re = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;
    return re.match(sDateTime);
  },
  
  // sDate must be: YYYY-MM-DD
  fromDATE: function(sDate) {
    var match, re = /^(\d{4})-(\d{2})-(\d{2})$/;

    if (!(match = re.exec(sDate)))
      Assert.that(match, "'%s' is not a valid DATE", sDate);

    return new Date(match[1], match[2] - 1, match[3]); // Js months are 0-11!!
  },

  // sDateTime must be: YYYY-MM-DD HH:MM:SS
  fromDATETIME : function(sDateTime) {
    var match, re = /^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/;
        
    if (/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})$/.exec(sDateTime))
      sDateTime += '-00';
    
    if (!(match = re.exec(sDateTime)))
      Assert.that(match, "'%s' is not a valid DATETIME", sDateTime);

    return new Date(match[1], match[2] - 1, match[3], match[4], match[5], match[6]); // Js months are 0-11!!
  },

  fromLocaleDate : function(sDate) {
    var match, re = /^(\d{2})\/(\d{2})\/(\d{4})$/;
    if (!(match = re.exec(sDate)))
      Assert.that(match, "'%s' is not a valid display date", sDate);

    return new Date(match[3], match[2] - 1, match[1]); 
  },

  fromLocaleDateTime : null
} );

Class.extend(Date, {
  toDATE: function() {
    var y = this.getFullYear();
    var m = this.getMonth()+1; // Js months are 0-11!!
    var d = this.getDate();
    
    return printf("%04d-%02d-%02d", y, m, d);
  },
  
  toDATETIME: function(useSpace) {
    var h = this.getHours();
    var m = this.getMinutes();
    var s = this.getSeconds();
    
    if(useSpace)
      return this.toDATE() + printf(" %02d:%02d:%02d", h, m, s);
    else
      return this.toDATE() + printf("+%02d:%02d:%02d", h, m, s);
  },
  
  toLocaleDate: function() {
    var y = this.getFullYear();
    var m = this.getMonth()+1; // Js months are 0-11!!
    var d = this.getDate();
    
    return printf("%02d/%02d/%04d", d, m, y);
  },
  
  toLocaleDateTime: function () {
    var h = this.getHours();
    var m = this.getMinutes();
    
    return this.toLocaleDate() + printf(" %02d:%02d", h, m);
  },
  
  addDays: function(iDays) {
    this.setDate(this.getDate() + iDays);
  }
} );