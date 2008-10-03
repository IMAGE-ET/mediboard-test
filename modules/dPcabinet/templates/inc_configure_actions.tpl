<table class="tbl">

  <tr>
    <th class="category">{{tr}}CPlageconsult{{/tr}}</th>
  </tr>
  
  <tr>
    <td class="button">
      <script type="text/javascript">
        PlageConsult = {
        	transfert: function() {
        	  var url = new Url();
        	  url.setModuleAction("dPcabinet", "transfert_plageconsult");
        	  url.popup(500, 600, "transfert");
        	}
        }
      </script>
      <button class="modify" type="button" onclick="PlageConsult.transfert()">
      	{{tr}}mod-dPcabinet-tab-transfert_plageconsult{{/tr}}
      </button>
    </td>
  </tr>
</table>
