<script type="text/javascript">
function parseBarcode(barcode) {
  var url = new Url("dmi", "httpreq_parse_barcode");
  url.addParam("barcode", barcode);
  url.requestUpdate("parsed-barcode");
}

Main.add(function(){
  $("barcode").observe("keypress", function(e){
    var charCode = Event.key(e);
    var input = Event.element(e);
    if (charCode == 13) {
      parseBarcode(input.value);
      input.select();
    }
  });
});
</script>

<form name="parse-barcode" action="" method="get" onsubmit="return false">
<input type="text" id="barcode" size="50" />
</form>

<div id="parsed-barcode"></div> 