<script>

initUpdatePrescriptions = function() {
  var url = new Url("soins", "httpreq_vw_bilan_list_prescriptions");
  url.addParam("prat_bilan_id" , "{{$chirSel}}");
  url.addParam("_date_entree_prevue", "{{$date}}");
  url.addParam("_date_sortie_prevue", "{{$date}}");
  url.addParam("board" , "1");
  url.periodicalUpdate("prescriptions_non_signees", { frequency: 300 } );
};

updatePrescriptions = function() {
  var url = new Url("soins", "httpreq_vw_bilan_list_prescriptions");
  url.addParam("prat_bilan_id" , "{{$chirSel}}");
  url.addParam("_date_entree_prevue", "{{$date}}");
  url.addParam("_date_sortie_prevue", "{{$date}}");
  url.addParam("board" , "1");
  url.requestUpdate("prescriptions_non_signees");
};

updateMessagerie = function() {
  var url = new Url("messagerie", "ajax_list_mails");
  url.addParam('account_id', "{{$account->_id}}");
  url.addParam('mode', 'all');
  url.requestUpdate("messagerie", {
    method: "get",
    onComplete: function() {
      console.log($$('#messagerie tr').length);
      if ($$('#messagerie tr').length <= 2) {
        $('tab_messagerie').addClassName('empty');
      }
    }
  });

};

updateNbPrescriptions = function(nb) {
  $('nb_prescriptions').update('('+nb+')');
};

initUpdateActes = function() {
  var url = new Url("board", "ajax_list_interv_non_cotees");
  url.addParam("praticien_id", "{{$chirSel}}");
  url.addParam("fin", "{{$date}}");
  url.addParam("board"       , "1");
  url.periodicalUpdate("actes_non_cotes", { frequency: 300 } );
};

updateActes = function() {
  var url = new Url("board", "ajax_list_interv_non_cotees");
  url.addParam("praticien_id", "{{$chirSel}}");
  url.addParam("fin", "{{$date}}");
  url.addParam("board"       , "1");
  url.requestUpdate("actes_non_cotes");
};

Main.add(function () {
  var tabs = Control.Tabs.create('tab-worklist', true);
  {{if "dPprescription"|module_active}}
    initUpdatePrescriptions();
  {{/if}}
  initUpdateActes();

  {{if "messagerie"|module_active && $account->_id}}
    updateMessagerie();
  {{/if}}


});

</script>


<ul id="tab-worklist" class="control_tabs">
  {{if "dPprescription"|module_active}}
  <li>
     <a href="#prescriptions_non_signees" class="empty">
       Prescriptions non signées <small>(&ndash;)</small>
     </a>
  </li>
  {{/if}}
  <li>
    <a href="#actes_non_cotes" class="empty">
      Actes non cotés
      <small>(&ndash;)</small>
    </a>
  </li>
  {{if "messagerie"|module_active}}
    <li>
      <a href="#messagerie" id="tab_messagerie">Messagerie</a>
    </li>
  {{/if}}

</ul>

{{if "dPprescription"|module_active}}
  <div id="prescriptions_non_signees">
  </div>
{{/if}}

<div id="actes_non_cotes">
</div>

{{if "messagerie"|module_active}}
  <table id="messagerie" class="tbl"></table>
{{/if}}