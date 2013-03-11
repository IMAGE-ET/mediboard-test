{{mb_script module=facturation    script=facture}}
{{mb_script module="dPpatients"   script="pat_selector"}}

<script>
refreshList = function(){
  var oForm = getForm("choice-facture");
  if(!oForm._pat_name.value){
    oForm.patient_id.value = '';
  }
  var url = new Url("facturation" , "{{$tab}}", "tab");
  url.addParam("etat_cloture" , $V(oForm.etat_cloture) ? 1 : 0 );
  url.addParam("etat_ouvert"  , $V(oForm.etat_ouvert) ? 1 : 0 );
  url.addParam("patient_id"   , oForm.patient_id.value);
  url.addParam("chirSel"      , oForm.chirSel.value);
  
  url.addParam("_date_min"    , oForm._date_min.value);
  url.addParam("_date_max"    , oForm._date_max.value);
  url.addParam("no_finish_reglement" , $V(oForm.no_finish_reglement) ? 1 : 0);
  url.addParam("type_date_search" , $V(oForm.type_date_search));
  url.redirect();
}

printFacture = function(facture_id, edit_justificatif, edit_bvr) {
  var url = new Url('facturation', 'ajax_edit_bvr');
  url.addParam('facture_class'        , '{{$facture->_class}}');
  url.addParam('facture_id', facture_id);
  url.addParam('edition_justificatif', edit_justificatif);
  url.addParam('edition_bvr', edit_bvr);
  url.addParam('suppressHeaders', '1');
  url.popup(1000, 600);
}

Main.add(function () {
  Calendar.regField(getForm("choice-facture")._date_min);
  Calendar.regField(getForm("choice-facture")._date_max);
});
</script>

<div id="factures">
  <form name="choice-facture" action="" method="get">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="tab" value="{{$tab}}" />
    <table class="form" name="choix_type_facture">
      <tr>
        <th>Depuis le</th>
        <td>{{mb_field object=$filter field="_date_min" form="choice-facture" canNull="false" register=true onchange="refreshList()"}}</td>
        <td>
           <label>
             <input name="etat_ouvert" value="1" type="checkbox" {{if $etat_ouvert == 1}}checked="checked"{{/if}} onchange="refreshList();" />
             Ouvertes
           </label>
           <label>
             <input name="etat_cloture" value="1" type="checkbox" {{if $etat_cloture == 1}}checked="checked"{{/if}} onchange="refreshList();" />
             Cloturées 
           </label>
        </td>
        <th>Patient</th>
        <td>
          {{mb_field object=$patient field="patient_id" hidden=1}}
          <input type="text" name="_pat_name" style="width: 15em;" value="{{$patient->_view}}" onchange="refreshList();" readonly="readonly" ondblclick="PatSelector.init()" />
          <button class="cancel notext" type="button" onclick="$V(this.form._pat_name,''); $V(this.form.patient_id,'')"></button>
          <button class="search notext" type="button" onclick="PatSelector.init()">{{tr}}Search{{/tr}}</button>
          <script type="text/javascript">
            PatSelector.init = function(){
              this.sForm = "choice-facture";
              this.sId   = "patient_id";
              this.sView = "_pat_name";
              this.pop();
            }
          </script>
        </td>
      </tr>
      <tr>
        <th>Jusqu'au</th>
        <td>{{mb_field object=$filter field="_date_max" form="choice-facture" canNull="false" register=true onchange="refreshList()"}}</td>
        <td>
          <label>
            <input type="checkbox" name="no_finish_reglement" value="0" {{if $no_finish_reglement }}checked="checked"{{/if}} onchange="refreshList();"/>
            Uniquement réglées
          </label>
        </td>
        <th>Praticien</th>
        <td>
          <select name="chirSel" style="width: 15em;" onchange="refreshList();">
            <option value="0" {{if !$chirSel}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
            {{mb_include module=mediusers template=inc_options_mediuser selected=$chirSel list=$listChirs}}
          </select>
        </td>
      </tr>
      <tr>
        <th>Date de</th>
        <td>
          <select name="type_date_search" onchange="refreshList();">
            <option value="cloture" {{if $type_date_search == "cloture"}} selected="selected" {{/if}}>
              Cloture
            </option>
            <option value="ouverture" {{if $type_date_search == "ouverture"}} selected="selected" {{/if}}>
              Ouverture
            </option>
          </select>
        </td>
      </tr>
    </table>
  </form>
  {{mb_include module=facturation template=vw_list_factures}}
</div>