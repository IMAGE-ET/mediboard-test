<script type="text/javascript">
  viewNoPratSejour = function() {
    var url = new Url("dPplanningOp", "vw_resp_no_prat"); 
    url.popup(700, 500, "printFiche");
  }
  
  checkSynchroSejour = function(sType) {
    var url = new Url("dPplanningOp", "check_synchro_hours_sejour");
    url.addParam("type", sType);
    url.requestUpdate("resultSynchroSejour");
  }
  
  closeSejourConsult = function() {
    var url = new Url("dPplanningOp", "ajax_close_sejour_consult");
    url.requestUpdate("result-close-sejour-consult");
  }

  popAddOperation = function () {
    var url = new Url("dPplanningOp", "add_operation_csv");
    url.popup(800, 600, "Ajout des intervensions");
    return false;
  }
  
  mergeInterv = function () {
    var url = new Url("dPplanningOp", "ajax_merge_interv");
    url.addParam("date_min_interv", $V($("date_min_interv")));
    url.requestUpdate("result-merge-interv");
  }  
</script>

<h2>Actions de maintenances</h2>

<table class="tbl">
  <tr>
    <th style="width: 50%">{{tr}}Action{{/tr}}</th>
    <th style="width: 50%">{{tr}}Status{{/tr}}</th>
  </tr>
  
  <tr>
    <td>
      <button class="change" onclick="viewNoPratSejour()">
        Corriger les praticiens des séjours
      </button>
    </td>
    <td></td>
  </tr>
  
  <tr>
    <td>
      <button class="search" onclick="checkSynchroSejour('check_entree');">
        Nombre d'heure d'entrée non conforme
      </button>
			<button class="save" onclick="checkSynchroSejour('fix_entree');">
        Corriger les problèmes d'entrée
      </button>
      <br />
      <button class="search" onclick="checkSynchroSejour('check_sortie');">
        Nombre d'heure de sortie non conforme
      </button>
      <button class="save" onclick="checkSynchroSejour('fix_sortie');">
        Corriger les problèmes de sortie
      </button>
    </td>
    <td id="resultSynchroSejour"></td>
  </tr>
	
	<tr>
    <td>
      <button class="change" onclick="closeSejourConsult()">
        {{tr}}close-sejour-consult{{/tr}}
      </button>
    </td>
    <td id="result-close-sejour-consult"></td>
  </tr>
  
  <tr>
    <td>
      <button class="hslip" onclick="return popAddOperation();">
        {{tr}}Add-Operation-CSV{{/tr}}
      </button>
    </td>
    <td></td>
  </tr>
  
  <tr>
    <td>
      <button class="change" onclick="mergeInterv()">
        {{tr}}merge-interv{{/tr}}
      </button>
      
      <input type="text" name="date_min_interv" value="{{$today}}" id="date_min_interv"/> Date minimale (YYYY-MM-DD)
    </td>
    <td id="result-merge-interv"></td>
  </tr>
</table>