{{mb_script module=facturation    script=facture}}
{{mb_script module="dPpatients"   script="pat_selector"}}

<script>
refreshList = function(){
  var oForm = getForm("choice-facture");
  if(!oForm._pat_name.value){
    oForm.patient_id.value = '';
  }
  var url = new Url("facturation" , "{{$tab}}");
  url.addElement(oForm.etat_cloture);
  url.addElement(oForm.patient_id);
  url.addElement(oForm.chirSel);
  url.addElement(oForm._date_min);
  url.addElement(oForm._date_max);
  url.addElement(oForm.type_date_search);
  {{if $conf.dPfacturation.CRelance.use_relances}}
    url.addParam("etat_relance" , $V(oForm.etat_relance) ? 1 : 0 );
  {{/if}}
  {{if $facture->_class == "CFactureEtablissement"}}
    url.addParam("etat_cotation" , $V(oForm.etat_cotation) ? 1 : 0 );
  {{/if}}
  url.addParam("no_finish_reglement" , $V(oForm.no_finish_reglement) ? 1 : 0);
  url.requestUpdate("factures");
}

printFacture = function(facture_id, type_pdf) {
  var url = new Url('facturation', 'ajax_edit_bvr');
  url.addParam('facture_class', '{{$facture->_class}}');
  url.addParam('facture_id'   , facture_id);
  url.addParam('type_pdf'     , type_pdf);
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
      {{assign var="classe" value=$facture->_class}}
      <tr>
        <th>Depuis le</th>
        <td>{{mb_field object=$filter field="_date_min" form="choice-facture" canNull="false" register=true}}</td>
        {{if !$conf.dPfacturation.$classe.use_auto_cloture}}
          <th>Etat</th>
          <td>
            <select name="etat_cloture">
              <option value="0" {{if $etat_cloture == "0"}} selected="selected" {{/if}}>-- Toutes</option>
              <option value="1" {{if $etat_cloture == "1"}} selected="selected" {{/if}}>Non cloturées</option>
              <option value="2" {{if $etat_cloture == "2"}} selected="selected" {{/if}}>Cloturées</option>
              </option>
            </select>
          </td>
        {{else}}
          <th></th>
          <td><input type="hidden" name="etat_cloture" value="0" /></td>
        {{/if}}
        <th>Patient</th>
        <td>
          {{mb_field object=$patient field="patient_id" hidden=1}}
          <input type="text" name="_pat_name" style="width: 15em;" value="{{$patient->_view}}" readonly="readonly" ondblclick="PatSelector.init()" />
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
        <td>{{mb_field object=$filter field="_date_max" form="choice-facture" canNull="false" register=true}}</td>
        <th></th>
        <td>
          <label>
            <input type="checkbox" name="no_finish_reglement" value="0" {{if $no_finish_reglement }}checked="checked"{{/if}}/>
            Uniquement réglées
          </label>
          {{if $conf.dPfacturation.CRelance.use_relances}}
            <label>
              <input name="etat_relance" value="1" type="checkbox" {{if $etat_relance == 1}}checked="checked"{{/if}}/>
               Relancées 
            </label>
          {{/if}}
        </td>
        <th>Praticien</th>
        <td>
          <select name="chirSel" style="width: 15em;">
            <option value="0" {{if !$chirSel}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
            {{if $facture->_class == "CFactureEtablissement"}} 
              <b><option value="-1" {{if $chirSel == "-1"}} selected="selected" {{/if}}>&mdash; Tous</option></b>
            {{/if}}
            {{mb_include module=mediusers template=inc_options_mediuser selected=$chirSel list=$listChirs}}
          </select>
        </td>
      </tr>
      
      <tr>
        {{if !$conf.dPfacturation.$classe.use_auto_cloture}}
        <th>Date de</th>
        <td>
          <select name="type_date_search">
            <option value="cloture" {{if $type_date_search == "cloture"}} selected="selected" {{/if}}>
              Cloture
            </option>
            <option value="ouverture" {{if $type_date_search == "ouverture"}} selected="selected" {{/if}}>
              Ouverture
            </option>
          </select>
        </td>
        {{else}}
          <th></th>
          <td><input type="hidden" name="type_date_search" value="ouverture" /></td>
        {{/if}}
        <th></th>
        <td colspan="3">
          {{if $facture->_class == "CFactureEtablissement"}}
            <label>
              <input name="etat_cotation" value="1" type="checkbox" {{if $etat_cotation == 1}}checked="checked"{{/if}}/>
              Non cotées
            </label>
          {{/if}}
        </td>
      </tr>
      <tr>
        <td class="button" colspan="6">
          <button type="button" onclick="refreshList();" class="submit" >{{tr}}Validate{{/tr}}</button>
          <button type="button" onclick="showLegend();" class="search" style="float:right;">Légende</button>
        </td>
      </tr>
    </table>
  </form>
  {{mb_include module=facturation template=vw_list_factures}}
</div>