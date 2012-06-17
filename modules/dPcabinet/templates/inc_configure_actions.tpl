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
    <th class="category">{{tr}}CConsultation{{/tr}}</th>
  </tr>
  
  <tr>
    <td class="button">
      <script type="text/javascript">
        Consultations = {
          macroStats: function(button) { 
            var form = button.form;
            var url = new Url('cabinet', 'macro_stats');
            url.addElement(form.period);
            url.addElement(form.date);
            url.requestModal(1000, 600);
          }
        };
        Main.add(function() {
          Calendar.regField(getForm("MacroStats").date);
        });
      </script>
      
      <form name="MacroStats" method="get">
        <input type="hidden" name="date" value="{{$date}}" />
        <select name="period">
          <option value="day"  >{{tr}}Day  {{/tr}}</option>
          <option value="week" >{{tr}}Week {{/tr}}</option>
          <option value="month">{{tr}}Month{{/tr}}</option>
          <option value="year" >{{tr}}Year {{/tr}}</option>
        </select>
        <button class="modify" type="button" onclick="Consultations.macroStats(this);">
          {{tr}}mod-dPcabinet-tab-macro_stats{{/tr}}
        </button> 
      </form>
    </td>
  </tr>

</table>
