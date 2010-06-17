Barcode = {
  parseDate: function(barcode) {
    barcode = String(barcode);
    
    var date, match;
    
    function getLastDay(month, year) {
      var dd = new Date(year, month, 0);
      return dd.getDate();
    }
    
    // 2016-08
    if (match = barcode.match(/(\d{4})-(\d{2})/)) {
      date = getLastDay(match[2], match[1])+"-"+match[2]+"-"+match[1];
    }
    
    // 130828
    if (match = barcode.match(/(\d{2})(\d{2})(\d{2})/)) {
      date = getLastDay(match[2], match[1])+"-"+match[2]+"-20"+match[1];
    }
    
    console.debug(date, match);
  }
};
