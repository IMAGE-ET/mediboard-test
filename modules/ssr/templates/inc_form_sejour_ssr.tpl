{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_script module=patients script=pat_selector}}

<script type="text/javascript">
ProtocoleSelector.init = function(){
  this.sForSejour      = true;
  this.sForm           = "editSejour";
  this.sChir_id        = "praticien_id";
  //this.sServiceId      = "service_id";
  //this.sDP             = "DP";
  //this.sDepassement    = "depassement";
  
  this.sLibelle_sejour = "libelle";
  //this.sType           = "type";
  this.sForceType      = "ssr";
  this.sDuree_prevu    = "_duree_prevue";
  //this.sConvalescence  = "convalescence";
  this.sRques_sej      = "rques";

  //this.sProtoPrescAnesth = "_protocole_prescription_anesth_id";
  //this.sProtoPrescChir   = "_protocole_prescription_chir_id";
  
  this.pop();
}

function updateSortiePrevue() {
  var oForm = document.editSejour;
    
  if (!oForm._duree_prevue.value) {
    $V(oForm._duree_prevue, 0);
  }
  
  var sDate = oForm.entree_prevue.value;
  if (!sDate) {
    return;
  }
  
  // Add days
  var dDate = Date.fromDATETIME(sDate);
  var nDuree = parseInt(oForm._duree_prevue.value, 10);
  dDate.addDays(nDuree);

  // Update fields
  $V(oForm.sortie_prevue,    dDate.toDATETIME());
  $V(oForm.sortie_prevue_da, dDate.toLocaleDateTime());
}

function updateDureePrevue() {
  var oForm = document.editSejour;
  
  if(oForm.entree_prevue.value) {
    var dEntree = Date.fromDATETIME(oForm.entree_prevue.value);
    var dSortie = Date.fromDATETIME(oForm.sortie_prevue.value);
    var iSecondsDelta = dSortie - dEntree;
    var iDaysDelta = iSecondsDelta / (24 * 60 * 60 * 1000);
    oForm._duree_prevue.value = Math.floor(iDaysDelta);
  }
}

function cancelSejourSSR() {
  var oForm = document.editSejour;
  var oElement = oForm.annule;
  
  if (oElement.value == "0") {
    if (confirm("Voulez-vous vraiment annuler le dossier ?")) {
      oElement.value = "1";
      oForm.submit();
      return;
    }
  }
      
  if (oElement.value == "1") {
    if (confirm("Voulez-vous vraiment rétablir le dossier ?")) {
      oElement.value = "0";
      oForm.submit();
      return;
    }
  }
}

function printFormSejour() {
  var url = new Url;
  url.setModuleAction("dPplanningOp", "view_planning"); 
  url.addParam("sejour_id", $V(getForm("editSejour").sejour_id));
  url.popup(700, 500, "printSejour");
  return;
}

</script>

<form name="editSejour" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  {{if $conf.ssr.recusation.sejour_readonly}}<input type="hidden" name="_locked" value="1" />{{/if}}
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_sejour_ssr_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="annule" value="{{$sejour->annule|default:"0"}}" />
  <input type="hidden" name="type" value="ssr" />
  
  {{mb_key object=$sejour}}
  {{mb_field object=$sejour field=group_id hidden=1}}

  {{if !$sejour->annule}}
    <input type="hidden" name="recuse" value="{{if $conf.ssr.recusation.use_recuse && !$sejour->_id}}-1{{else}}{{$sejour->recuse}}{{/if}}"/>
  {{/if}}

  <input type="hidden" name="_bind_sejour" value="1" />

  <table class="form">
    <tr>
      {{if $sejour->_id}}
      <th class="title modify text" colspan="8">
        {{mb_include module=system template=inc_object_notes      object=$sejour}}
        {{mb_include module=system template=inc_object_idsante400 object=$sejour}}
        {{mb_include module=system template=inc_object_history    object=$sejour}}
  
        <a class="action" style="float: right;" title="Modifier uniquement le sejour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
          <img src="images/icons/edit.png" alt="modifier" />
        </a>
        {{tr}}CSejour-title-modify{{/tr}} {{$sejour}}
        {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
      </th>
      {{else}}
      <th class="title" colspan="8">
        <button type="button" class="search" style="float: left;" onclick="ProtocoleSelector.init()">
          {{tr}}button-COperation-choixProtocole{{/tr}}
        </button>
        
        {{tr}}CSejour-title-create{{/tr}} 
        {{if $sejour->_NDA}}
          pour le dossier
          {{mb_include module=planningOp template=inc_vw_numdos nda_obj=$sejour}}
        {{/if}}
      </th>
      {{/if}}
    </tr>
  
    {{if $sejour->annule}}
    <tr>
      <th class="category cancelled" colspan="6">
        {{tr}}CSejour-{{$sejour->recuse|ternary:"recuse":"annule"}}{{/tr}}
      </th>
    </tr>
    {{/if}}
    
    {{if !$modules.dPplanningOp->_can->edit}}
      {{if $sejour->_id}}
      <tr>
        <th>{{mb_label object=$sejour field=praticien_id}}</th>
        <td>{{mb_value object=$sejour field=praticien_id}}</td>
        <th>{{mb_label object=$sejour field=libelle}}</th>
        <td>{{mb_value object=$sejour field=libelle}}</td>
        <th>{{mb_label object=$sejour field=_duree_prevue}}</th>
        <td>{{mb_value object=$sejour field=_duree_prevue}} jours</td>
      </tr>
      {{else}}
      <tr>
        <td>
          <div class="small-warning">
            Vous ne pouvez pas créer de nouveaux séjours.
            <br>
            Merci d'en sélectionner un depuis la vue des séjours.
          </div>
        </td>
      </tr>
      {{/if}}
    {{else}}
      <tr>
        <th>{{mb_label object=$sejour field=praticien_id}}</th>
        <td>
          <select name="praticien_id" class="{{$sejour->_props.praticien_id}}" style="width: 15em" tabindex="1">
            <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
            {{foreach from=$prats item=_user}}
            <option value="{{$_user->_id}}" class="mediuser" 
              style="border-color: #{{$_user->_ref_function->color}}" {{if $_user->_id == $sejour->praticien_id}}selected="selected"{{/if}}>
              {{$_user->_view}}
            </option>
            {{/foreach}}
          </select>
        </td>
  
        <th>{{mb_label object=$sejour field=entree_prevue}}</th>
        <td colspan="2">
          {{mb_field object=$sejour field="entree_prevue" form="editSejour" tabindex="5" register=true canNull=false onchange="updateSortiePrevue();"}}
        </td>
      </tr>
    
      <tr>
        <th>
          <input type="hidden" name="patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$sejour->patient_id}}" />
          {{mb_label object=$sejour field="patient_id"}}
        </th>
        <td>
          <input type="text" name="patient_view" style="width: 15em" value="{{$patient->_view}}" tabindex="2" 
            {{if (!$sejour->_id || $app->user_type == 1) && !$conf.ssr.recusation.sejour_readonly}} 
            onclick="PatSelector.init()" 
            {{/if}}
            readonly="readonly" />
          {{if (!$sejour->_id || $app->user_type == 1) && !$conf.ssr.recusation.sejour_readonly}} 
            <button type="button" class="search notext" onclick="PatSelector.init()">
              {{tr}}Choose{{/tr}}
            </button>
          {{/if}}
          <button id="button-edit-patient" type="button" 
                  onclick="location.href='?m=dPpatients&amp;tab=vw_edit_patients&amp;patient_id='+this.form.patient_id.value" 
                  class="edit notext" {{if !$patient->_id || $conf.ssr.recusation.sejour_readonly}}style="display: none;"{{/if}}>
            {{tr}}Edit{{/tr}}
          </button>
          <script type="text/javascript">
            PatSelector.init = function(){
              this.sForm = "editSejour";
              this.sId   = "patient_id";
              this.sView = "patient_view";
              this.pop();
            }
          </script>
        </td>
        
        <th>{{mb_label object=$sejour field=sortie_prevue}}</th>
        <td colspan="2">{{mb_field object=$sejour field="sortie_prevue" form="editSejour"  tabindex="6" register=true canNull=false onchange="updateDureePrevue();"}}</td>
      </tr>
      
      <tr>
        <th>{{mb_label object=$sejour field=libelle}}</th>
        <td>{{mb_field object=$sejour field=libelle form=editSejour tabindex="3" style="width: 12em"}}</td>
        <th>{{mb_label object=$sejour field=_duree_prevue}}</th>
        <td>
          {{mb_field object=$sejour field="_duree_prevue" increment=true form=editSejour prop="num min|0" size=2 tabindex="7" onchange="updateSortiePrevue();" value=$sejour->sejour_id|ternary:$sejour->_duree_prevue:0}}
          nuits
        </td>
        <td id="dureeEst"></td>
      </tr>
      
      {{if !$dialog && !$conf.ssr.recusation.sejour_readonly}} 
      <tr>
        <td class="button" colspan="8">
          {{if $sejour->_id}}
            {{if $can->edit}}
              <button class="modify default" type="submit" tabindex="23">{{tr}}Save{{/tr}}</button>
              {{if $can->admin}}
              <button class="{{$sejour->annule|ternary:'change':'cancel'}}" type="button" tabindex="24" onclick="cancelSejourSSR();">
                 {{tr}}{{$sejour->annule|ternary:'Restore':'Cancel'}}{{/tr}}
              </button>
                <button class="trash" type="button" tabindex="25" onclick="confirmDeletion(this.form,{typeName:'le séjour ',objName:'{{$sejour->_view|smarty:nodefaults|JSAttribute}}'})">
                  {{tr}}Delete{{/tr}}
                </button>
              {{/if}}
            {{/if}}
            <button class="print" type="button" onclick="printFormSejour();">{{tr}}Print{{/tr}}</button>
          {{else}}
            <button class="submit default" tabindex="26" type="submit">{{tr}}Create{{/tr}}</button>
          {{/if}}
        </td>
      </tr>
      {{/if}}
    {{/if}}

  </table>
</form>
