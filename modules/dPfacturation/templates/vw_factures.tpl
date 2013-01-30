{{mb_script module=facturation     script=facture}}
{{mb_script module="dPpatients"   script="pat_selector"}}

<script>
refreshList = function(){
  var oForm = getForm("choice-facture");
  if(!oForm._pat_name.value){
    oForm.patient_id.value = '';
  }
  var url = new Url("dPfacturation" , "vw_factures_sejour", "tab");
  url.addParam("etat_cloture" , $V(oForm.etat_cloture) ? 1 : 0 );
  url.addParam("etat_ouvert"  , $V(oForm.etat_ouvert) ? 1 : 0 );
  url.addParam("patient_id"   , oForm.patient_id.value);
  url.addParam("chirSel"      , oForm.chirSel.value);
  
  url.addParam("_date_min"    , oForm._date_min.value);
  url.addParam("_date_max"    , oForm._date_max.value);
  url.addParam("no_finish_reglement" , $V(oForm.no_finish_reglement) ? 1 : 0);
  url.redirect();
}

viewFacture = function(element, facture_id, facture_class){
  if (element) {
    element.up("tr").addUniqueClassName("selected");
  }
   
  var url = new Url("dPfacturation"     , "ajax_view_facture");
  url.addParam("facture_id", facture_id);
  url.addParam("object_class", facture_class);
  url.requestUpdate('load_facture');
}

printFacture = function(facture_id, edit_justificatif, edit_bvr) {
//  var url = new Url('planningOp', 'ajax_edit_bvr');
//  url.addParam('facture_id', facture_id);
//  url.addParam('edition_justificatif', edit_justificatif);
//  url.addParam('edition_bvr', edit_bvr);
//  url.addParam('suppressHeaders', '1');
//  url.popup(1000, 600);
}
Main.add(function () {
  Calendar.regField(getForm("choice-facture")._date_min, null);
  Calendar.regField(getForm("choice-facture")._date_max, null);
  
  {{if $facture->_id}}
    viewFacture(null, '{{$facture->_id}}', '{{$facture->_class}}');
  {{/if}}
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
            {{foreach from=$listChirs item=curr_chir}}
              <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}" {{if $chirSel == $curr_chir->user_id}} selected="selected" {{/if}}>
                {{$curr_chir->_view}}
              </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
    </table>
  </form>
  <table class="main">
    <tr>
      <td style="width:200px;">
        <table class="tbl">
          <tr>
            <th colspan="2" class="title">Factures</th>
          </tr>
          <tr>
            <th>Date</th>
            <th>Patient</th>
          </tr>
          {{foreach from=$factures item=_facture}}
            <tr class="{{if $facture->_id == $_facture->_id}}selected{{/if}}">
              <td>
                {{if $_facture->cloture}}
                  {{mb_value object=$_facture field="cloture"}}
                {{else}}
                  {{$_facture->ouverture|date_format:"%d/%m/%Y"}}
                {{/if}}
              </td>
              <td class="text">
                <a onclick="viewFacture(this, '{{$_facture->facture_id}}', '{{$_facture->_class}}');" href="#"
                  onmouseover="ObjectTooltip.createEx(this, '{{$_facture->_ref_patient->_guid}}')">
                  {{$_facture->_ref_patient->_view|truncate:30:"...":true}}
                </a>
              </td>
            </tr>
          {{/foreach}}
        </table>
      </td>
      <td id="load_facture">&nbsp;</td>
    </tr>
  </table>
</div>