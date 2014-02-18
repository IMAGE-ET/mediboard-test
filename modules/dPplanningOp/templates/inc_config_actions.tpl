<script>
  viewScoreASA = function(view) {
    var url = new Url('planningOp', 'ajax_revert_asa');
    url.addParam('view', view);
    if (view) {
      url.requestModal(500);
    }
    else {
      url.requestUpdate("resultScoreASA");
    }
  }

  viewNoPratSejour = function() {
    var url = new Url("dPplanningOp", "vw_resp_no_prat");
    url.popup(700, 500, "printFiche");

    return false;
  }

  popAddOperation = function () {
    var url = new Url("dPplanningOp", "add_operation_csv");
    url.popup(800, 600, "Ajout des intervensions");
    
    return false;
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
  
  mergeInterv = function () {
    var url = new Url("dPplanningOp", "ajax_merge_interv");
    url.addParam("date_min", $V($("date_min")));
    url.requestUpdate("result-actions-change", { onComplete: function() {
      repeatActions("mergeInterv");
    }});
  }  
  
  mergeSejours = function () {
    var url = new Url("dPplanningOp", "ajax_merge_sejours");
    url.addParam("date_min", $V($("date_min")));
    url.requestUpdate("result-actions-change", { onComplete: function() {
      repeatActions("mergeSejours");
    }});
  }  
  
  repeatActions = function (func) {
    if ($V($("check_repeat_actions"))) {
      var date = Date.fromDATE($V($("date_min")));
      date.addDays(1);
      
      $V($("date_min"), date.toDATE());
      window[func]();
    }
  }

  Datamining = {
    board: function() {
      this.url = new Url('planningOp', 'datamining_board');
      this.url.requestModal('800', '600');
    },

    mine: function(miner_class, remine) {
      var url = new Url('planningOp', 'ajax_datamine_operation');
      url.addNotNullParam('miner_class', miner_class);
      url.addNotNullParam('remine', remine);
      url.addParam('automine', $('automine').checked ? 1 : 0);
      url.addParam('limit', $V('limit'));
      url.requestUpdate(SystemMessage.id, this.url.refreshModal.bind(this.url));
    },

    auto: function() {
      if ($('automine').checked) {
        Datamining.mine();
      }
    }
  }
</script>

<h2>Actions de maintenances</h2>

<table class="tbl">
  <tr>
    <th>{{tr}}Action{{/tr}}</th>
    <th>{{tr}}Status{{/tr}}</th>
  </tr>
  
  <tr>
    <td class="narrow">
      <button class="search" onclick="viewNoPratSejour()">
        Corriger les praticiens des séjours
      </button>
    </td>
    <td></td>
  </tr>
  
  <tr>
    <td class="narrow">
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
      <button class="hslip" onclick="return popAddOperation();">
        {{tr}}Add-Operation-CSV{{/tr}}
      </button>
    </td>
    <td></td>
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
    <td class="narrow">
      <button class="change" onclick="mergeInterv()">
        {{tr}}merge-interv{{/tr}}
      </button>  
      <br />

      <button class="change" onclick="mergeSejours()">
        {{tr}}merge-sejours{{/tr}}
      </button>
      <br />
      <input type="text" name="date_min" value="{{$today}}" id="date_min"   /> Date minimale (YYYY-MM-DD) <br />
      <input type="checkbox" name="see_yesterday"  id="see_yesterday"       /> Également ceux de la veille <br />
      <input type="checkbox" name="repeat_actions" id="check_repeat_actions"/> Relancer automatiquement
    </td>
    
    <td id="result-actions-change"></td>
  </tr>

  <tr>
    <td class="narrow">
      <button class="search" onclick="viewScoreASA(1);">Interventions ayant un score ASA à 1</button>
      <button class="save" onclick="viewScoreASA(0);">Corriger les scores ASA</button>
    </td>
    <td id="resultScoreASA"></td>
  </tr>
  
  <tr>
    <td colspan="2">
      <button class="search" onclick="Datamining.board()">{{tr}}Datamining{{/tr}}</button>
    </td>
  </tr>  
</table>