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

</script>

<h2>Actions de maintenances</h2>

<table class="tbl">
  <tr>
    <th style="width: 50%">Action</th>
    <th style="width: 50%">Status</th>
  </tr>
  
  <tr>
    <td>
      <button class="change" onclick="viewNoPratSejour()">
        Corriger les praticiens des séjours
      </button>
    </td>
    <td>
    </td>
  </tr>
  
  <tr>
    <td>
      <button class="search" onclick="checkSynchroSejour('check_entree');">
        Nombre d'heure d'entrée non conforme
      </button>
      <br />
      <button class="search" onclick="checkSynchroSejour('check_sortie');">
        Nombre d'heure de sortie non conforme
      </button>
      <br />
      <button class="save" onclick="checkSynchroSejour('fix_entree');">
        Corriger les problèmes d'entrée
      </button>
      <br />
      <button class="save" onclick="checkSynchroSejour('fix_sortie');">
        Corriger les problèmes de sortie
      </button>
    </td>
    <td id="resultSynchroSejour">
    </td>
  </tr>

</table>