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
    
    var parts = barcode.split(Barcode.code128FNC1);
    var composition = {};
    
    parts.each(function(p){
      for(var code in Barcode.code128Prefixes){
        if (p.indexOf(code) == 0) {
          composition[code] = p.substr(code.length, p.length-code.length);
          break;
        }
      }
    });

    return composition;
  }
};
