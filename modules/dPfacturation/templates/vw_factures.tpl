{{mb_script module=facturation    script=facture}}
{{mb_script module="dPpatients"   script="pat_selector"}}

<script>
  changePage = function(page) {
    var url = new Url("facturation" , "ajax_list_factures");
    url.addParam('facture_class', '{{$facture->_class}}');
    url.addParam('page'         , page);
    url.requestUpdate("liste_factures");
  }

  refreshList = function(){
  var oForm = getForm("choice-facture");
  if(!oForm._pat_name.value){
    oForm.patient_id.value = '';
  }
  var url = new Url("facturation" , "{{$tab}}");
  url.addElement(oForm.patient_id);
  url.addElement(oForm.chirSel);
  url.addElement(oForm._date_min);
  url.addElement(oForm._date_max);
  url.addElement(oForm.type_date_search);
  url.addElement(oForm.num_facture);
  url.addElement(oForm.page);

  {{if !$conf.dPfacturation.Other.use_search_easy}}
    url.addElement(oForm.etat_cloture);
    url.addElement(oForm.numero);
    url.addElement(oForm.xml_etat);
    {{if $conf.dPfacturation.CRelance.use_relances}}
      url.addParam("etat_relance" , $V(oForm.etat_relance) ? 1 : 0 );
    {{/if}}
    {{if $facture->_class == "CFactureEtablissement"}}
      url.addParam("etat_cotation" , $V(oForm.etat_cotation) ? 1 : 0 );
    {{/if}}
    url.addParam("no_finish_reglement" , $V(oForm.no_finish_reglement) ? 1 : 0);
  {{else}}
    url.addElement(oForm.search_easy);
  {{/if}}
  url.requestUpdate("factures");
};

viewPatient = function() {
  var form = getForm("choice-facture");
  if (form.patient_id.value) {
    var url = new Url('patients', 'vw_edit_patients', 'tab');
    url.addElement(form.patient_id);
    url.redirect();
  }
};

updateEtatSearch = function() {
  {{if !$conf.dPfacturation.Other.use_search_easy}}
    var form = getForm("choice-facture");
    if ($V(form.type_date_search) == "cloture") {
      form.etat_cloture[1].disabled = "disabled";
      if ($V(form.etat_cloture) == 1) {
        $V(form.etat_cloture, 0);
      }
    }
    else {
      form.etat_cloture[1].disabled = "";
    }
  {{/if}}
};

Main.add(function () {
  Calendar.regField(getForm("choice-facture")._date_min);
  Calendar.regField(getForm("choice-facture")._date_max);
});
</script>

<div id="factures">
  <form name="choice-facture" action="" method="get">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="tab" value="{{$tab}}" />
    <input type="hidden" name="page" value="{{$page}}" onchange="refreshList()"/>
    <table class="form" name="choix_type_facture">
      {{assign var="classe" value=$facture->_class}}
      <tr>
        <th class="narrow">Patient</th>
        <td class="narrow">
          {{mb_field object=$patient field="patient_id" hidden=1}}
          <input type="text" name="_pat_name" style="width: 15em;" value="{{$patient->_view}}" readonly="readonly" ondblclick="PatSelector.init()" />
          <button class="cancel notext" type="button" onclick="$V(this.form._pat_name,''); $V(this.form.patient_id,'')"></button>
          <button class="search notext" type="button" onclick="PatSelector.init()">{{tr}}Search{{/tr}}</button>
          <button class="edit notext" type="button" onclick="viewPatient();">{{tr}}View{{/tr}}</button>
          <script>
            PatSelector.init = function(){
              this.sForm = "choice-facture";
              this.sId   = "patient_id";
              this.sView = "_pat_name";
              this.pop();
            }
          </script>
        </td>
        {{if !$conf.dPfacturation.Other.use_search_easy}}
          <th>{{mb_title object=$facture field=numero}}</th>
          <td>
            <select name="numero">
              <option value="0" {{if $numero == "0"}} selected="selected" {{/if}}>-- Toutes</option>
              <option value="1" {{if $numero == "1"}} selected="selected" {{/if}}>1</option>
              <option value="2" {{if $numero == "2"}} selected="selected" {{/if}}>2</option>
              <option value="3" {{if $numero == "3"}} selected="selected" {{/if}}>3</option>
              </option>
            </select>
          </td>
          <th>{{mb_title object=$facture field=facture}}</th>
          <td>
            <select name="xml_etat">
              <option value="" {{if !$xml_etat}} selected="selected" {{/if}}>-- Toutes</option>
              <option value="-1" {{if $xml_etat == "-1"}} selected="selected" {{/if}}>
                {{tr}}CFactureEtablissement.facture.-1{{/tr}}</option>
              <option value="0" {{if $xml_etat == "0"}} selected="selected" {{/if}}>
                {{tr}}CFactureEtablissement.facture.0{{/tr}}</option>
              <option value="1" {{if $xml_etat == "1"}} selected="selected" {{/if}}>
                {{tr}}CFactureEtablissement.facture.1{{/tr}}</option>
              </option>
            </select>
          </td>
        {{else}}
          <th>{{mb_label object=$facture field=type_facture}}</th>
          <td>
            <select name="search_easy">
              <option value="0" {{if $search_easy == "0"}} selected="selected" {{/if}}>-- Toutes</option>
              {{if $conf.dPfacturation.Other.use_field_definitive}}
                <option value="1" {{if $search_easy == "1"}} selected="selected" {{/if}}>Définitive</option>
              {{/if}}
              {{if !$conf.dPfacturation.$classe.use_auto_cloture}}
                <option value="2" {{if $search_easy == "2"}} selected="selected" {{/if}}>Cloturée</option>
                <option value="3" {{if $search_easy == "3"}} selected="selected" {{/if}}>Non cloturée</option>
              {{/if}}
              {{if $facture->_class == "CFactureEtablissement"}}
                <option value="4" {{if $search_easy == "4"}} selected="selected" {{/if}}>Non cotée</option>
                <option value="5" {{if $search_easy == "5"}} selected="selected" {{/if}}>Extournée</option>
              {{/if}}
              <option value="6" {{if $search_easy == "6"}} selected="selected" {{/if}}>Réglée</option>
              {{if $conf.dPfacturation.CRelance.use_relances}}
                <option value="7" {{if $search_easy == "7"}} selected="selected" {{/if}}>Relancée</option>
              {{/if}}
            </select>
          </td>
        {{/if}}
      </tr>
      <tr>
        <th></th>
        <td>
          <input type="text" name="_seek_patient" style="width: 13em;"/>
          <script>
            Main.add(function () {
              var form = getForm("choice-facture");
              var url = new Url("system", "ajax_seek_autocomplete");
              url.addParam("object_class", "CPatient");
              url.addParam("field", "patient_id");
              url.addParam("view_field", "_pat_name");
              url.addParam("input_field", "_seek_patient");
              url.autoComplete(form.elements._seek_patient, null, {
                minChars: 3,
                method: "get",
                select: "view",
                dropdown: false,
                width: "300px",
                afterUpdateElement: function(field,selected){
                  $V(field.form.patient_id, selected.getAttribute("id").split("-")[2]);
                  $V(field.form.elements._pat_name, selected.down('.view').innerHTML);
                  $V(field.form.elements._seek_patient, "");
                }
              });
            });
          </script>
        </td>
        {{if !$conf.dPfacturation.Other.use_search_easy}}
          {{if !$conf.dPfacturation.$classe.use_auto_cloture}}
            <th>Etat</th>
            <td>
              <select name="etat_cloture">
                <option value="0" {{if $etat_cloture == "0"}} selected="selected" {{/if}}>-- Toutes</option>
                <option value="1" {{if $etat_cloture == "1"}} selected="selected" {{/if}} {{if $type_date_search == "cloture"}}disabled="disabled" {{/if}}>Non cloturées</option>
                <option value="2" {{if $etat_cloture == "2"}} selected="selected" {{/if}}>Cloturées</option>
                </option>
              </select>
            </td>
          {{else}}
            <th></th>
            <td><input type="hidden" name="etat_cloture" value="0" /></td>
          {{/if}}
        {{/if}}
        <th>Depuis le</th>
        <td>{{mb_field object=$filter field="_date_min" form="choice-facture" canNull="false" register=true}}</td>
      </tr>
      <tr>
        <th>Praticien</th>
        <td>
          <select name="chirSel" style="width: 15em;">
            <option value="0" {{if !$chirSel}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
            {{if $facture->_class == "CFactureEtablissement"}} 
              <option value="-1" {{if $chirSel == "-1"}} selected="selected" {{/if}}><b>&mdash; Tous</b></option>
            {{/if}}
            {{mb_include module=mediusers template=inc_options_mediuser selected=$chirSel list=$listChirs}}
          </select>
        </td>
        {{if !$conf.dPfacturation.Other.use_search_easy}}
          <th></th>
          <td class="narrow">
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
        {{/if}}
        <th>Jusqu'au</th>
        <td>{{mb_field object=$filter field="_date_max" form="choice-facture" canNull="false" register=true}}</td>
      </tr>
      
      <tr>
        <th>Numéro de facture</th>
        <td><input name="num_facture" value="{{$num_facture}}" type="text" /></td>
        {{if !$conf.dPfacturation.Other.use_search_easy}}
          <th></th>
          <td>
            {{if $facture->_class == "CFactureEtablissement"}}
              <label>
                <input name="etat_cotation" value="1" type="checkbox" {{if $etat_cotation == 1}}checked="checked"{{/if}}/>
                Non cotées
              </label>
            {{/if}}
          </td>
        {{/if}}
        {{if !$conf.dPfacturation.$classe.use_auto_cloture}}
          <th>Date de</th>
          <td>
            <select name="type_date_search" onchange="updateEtatSearch();">
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
      </tr>
      <tr>
        <td class="button" colspan="6">
          <button type="button" onclick="$V(this.form.page, 0);refreshList();" class="submit" >{{tr}}Validate{{/tr}}</button>
          <button type="button" onclick="showLegend();" class="search" style="float:right;">Légende</button>
        </td>
      </tr>
    </table>
  </form>
  {{mb_include module=facturation template=vw_list_factures}}
</div>