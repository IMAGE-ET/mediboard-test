Barcode = {
  code128FNC1: "@",
  parseDate: function(barcode, iso) {
    barcode = String(barcode);
    
    var date = null, match;
    
    function getLastDay(month, year) {
      var dd = new Date(year, month, 0);
      return dd.getDate();
    }
    
    // 2016-08
    if (match = barcode.match(/(\d{4})-(\d{2})/)) {
      date = match[1]+"-"+match[2]+"-"+getLastDay(match[2], match[1]);
    }
    
    // 130828
    if (match = barcode.match(/(\d{2})(\d{2})(\d{2})/)) {
      date = "20"+match[1]+"-"+match[2]+"-"+getLastDay(match[2], match[1]);
    }
    
    if (date && !iso) {
      date = Date.fromDATE(date).toLocaleDate();
    }
    
    return date;
  },
  getType: function(barcode){
    barcode = String(barcode);
    
    if (barcode.indexOf(Barcode.code128FNC1) != -1) {
      return "code128";
    }
    
    return null;
  },
  
  parseCode128: function(barcode){
    barcode = String(barcode);
    var composition = {};
    var parts;
    
    if (Barcode.getType(barcode) ==  "code128") {
      parts = barcode.split(Barcode.code128FNC1);
      
      parts.each(function(p){
        for(var code in Barcode.code128Prefixes){
          if (p.indexOf(code) == 0) {
            composition[code] = p.substr(code.length, p.length-code.length);
            break;
          }
        }
      });
    }
    else {
      (parts = barcode.match(/(10)([a-z0-9]{7,})(17)(\d{6})$/i)) ||
      (parts = barcode.match(/(17)(\d{6})(10)([a-z0-9]{7,})$/i));
      
      if (!parts) return;
      
      var prop = null;
      parts.each(function(p){
        if (p.match(/^(10|17)$/)) {
          prop = p;
        }
        else if (prop) {
          composition[prop] = p;
        }
        else prop = null;
      });
    }
    
    return composition;
  }
};
