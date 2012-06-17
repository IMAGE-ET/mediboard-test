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
      <button class="modify" type="button" onclick="PlageConsult.transfert();">
      	{{tr}}mod-dPcabinet-tab-transfert_plageconsult{{/tr}}
      </button> 
    </td>
  </tr>

  <tr>
    <th class="category">{{tr}}Conslutations{{/tr}}</th>
  </tr>
  
  <tr>
    <td class="button">
      <button class="modify" type="button" onclick="new Url('cabinet', 'macro_stats').requestModal(1000, 600);">
        {{tr}}mod-dPcabinet-tab-macro_stats{{/tr}}
      </button> 
    </td>
  </tr>

</table>
