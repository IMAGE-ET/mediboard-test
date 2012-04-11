{{mb_script module=dPcabinet script=facture}}
{{mb_script module="dPpatients"    script="pat_selector"}}

<script>
refreshList = function(){
  var oForm = getForm("choice-facture");
  if(!oForm._pat_name.value){
    oForm.patient_id.value = '';
  }
  var url = new Url("cabinet" , "vw_factures");
  url.addParam("etat_cloture" , $V(oForm.etat_cloture) ? 1 : 0 );
  url.addParam("etat_ouvert"  , $V(oForm.etat_ouvert) ? 1 : 0 );
  url.addParam("patient_id"   , oForm.patient_id.value);
  url.addParam("chirSel"      , oForm.chirSel.value);
  url.addParam("no_finish_reglement" , $V(oForm.no_finish_reglement) ? 1 : 0);
  url.requestUpdate('factures');
}
  
viewFacture = function(element, factureconsult_id){
  element.up("tr").addUniqueClassName("selected");
   
  var url = new Url("cabinet"     , "ajax_view_facture");
  url.addParam("factureconsult_id", factureconsult_id);
  url.requestUpdate('load_facture');
}

printFacture = function(factureconsult_id, edit_justificatif, edit_bvr, preimpression, prat_id) {
  var url = new Url('dPcabinet', 'edit_bvr');
  url.addParam('factureconsult_id', factureconsult_id);
  url.addParam('edit_justificatif', edit_justificatif);
  url.addParam('edit_bvr', edit_bvr);
  url.addParam('preimpression', preimpression);
  url.addParam('prat_id', prat_id);
  url.addParam('suppressHeaders', '1');
  url.popup(1000, 600, 'systemMsg');
}
</script>

<div id="factures">
  <form name="choice-facture" action="" method="get">
    <input type="hidden" name="m" value="{{$m}}" />
    <input type="hidden" name="tab" value="{{$tab}}" />
    <table class="form" name="choix_type_facture">
      <tr>
        <th>Afficher les factures:</th>
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
        <th></th>
        <td>
          <label>
            <input type="checkbox" name="no_finish_reglement" value="0" onchange="refreshList();"/>
            Uniquement réglées
          </label>
        </td>
        <th >Praticien</th>
        <td ><!-- Un iclude existe peu etre! à voir-->
          <select name="chirSel" style="width: 15em;" onchange="refreshList();">
            <option value="0" {{if !$chirSel}} selected="selected" {{/if}}>&mdash; Choisir un professionnel</option>
            {{foreach from=$listChirs item=curr_chir}}
              <option class="mediuser" style="border-color: #{{$curr_chir->_ref_function->color}};" value="{{$curr_chir->user_id}}" {{if $chirSel == $curr_chir->user_id}} selected="selected" {{/if}}>
                {{$curr_chir->_view}}
              </option>
            {{/foreach}}
          </select>
        </td>
        {{if @$modules.tarmed->_can->read && $conf.tarmed.CCodeTarmed.use_cotation_tarmed}}
          <td>
            <button class="printPDF" onclick="printFacture(null , 0, 1, 0, {{if $chirSel}}{{$chirSel}}{{else}}null{{/if}});">Préimpression de BVR</button></td>
        {{/if}}
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
            <th> Date</th>
            <th> Patient</th>
          </tr>
          {{foreach from=$factures item=_facture}}
            <tr {{if $facture->_id == $_facture->_id}} class="selected" {{/if}}>
              <td>
                {{if $_facture->cloture}}
                  {{mb_value object=$_facture field="cloture"}}
                {{else}}
                  {{$_facture->ouverture|date_format:"%d/%m/%Y"}}
                {{/if}}
              </td>
              <td class="text">
                <a onclick="viewFacture(this, '{{$_facture->factureconsult_id}}');" href="#">{{$_facture->_ref_patient->_view}}</a>
              </td>
            </tr>
          {{/foreach}}
        </table>
      </td>
      <td>
        <table class="form">
          <tr>
            <th class="title ">Modifier une facture</th>
          </tr>
          <tr>
            <td id="load_facture">
              {{mb_include module=dPcabinet template="inc_vw_facturation"}}
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</div>