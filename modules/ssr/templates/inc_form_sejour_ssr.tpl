{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module=dPpatients script=pat_selector}}

<script type="text/javascript">
  
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
  $V(oForm.sortie_prevue, dDate.toDATETIME());
  oView = getForm('editSejour').sortie_prevue_da;
  $V(oView, dDate.toLocaleDateTime());
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
  var oElement = oForm._annule;
  
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

</script>

<form name="editSejour" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_sejour_ssr_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="annule" value="{{$sejour->annule|default:"0"}}" />
  <input type="hidden" name="type" value="ssr" />
	
  {{mb_key object=$sejour}}
  {{mb_field object=$sejour field=group_id hidden=1}}

  <input type="hidden" name="_bind_sejour" value="1" />

  <table class="form">
    <tr>
      {{if $sejour->_id}}
      <th class="title modify text" colspan="5">
        {{mb_include module=system template=inc_object_notes      object=$sejour}}
        {{mb_include module=system template=inc_object_idsante400 object=$sejour}}
        {{mb_include module=system template=inc_object_history    object=$sejour}}
  
        <a class="action" style="float: right;" title="Modifier uniquement le sejour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
          <img src="images/icons/edit.png" alt="modifier" />
        </a>
        {{tr}}CSejour-title-modify{{/tr}} {{$sejour}}
        {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}
      </th>
      {{else}}
      <th class="title" colspan="5">
        {{tr}}CSejour-title-create{{/tr}} 
        {{if $sejour->_num_dossier}}
          pour le dossier
          {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}
        {{/if}}
      </th>
      {{/if}}
    </tr>
  
    {{if $sejour->annule}}
    <tr>
      <th class="category cancelled" colspan="5">
      {{tr}}CSejour-annule{{/tr}}
      </th>
    </tr>
    {{/if}}
    
    <tr>
      <th>{{mb_label object=$sejour field=praticien_id}}</th>
      <td>
        <select name="praticien_id" class="{{$sejour->_props.praticien_id}}" tabindex="1">
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
      <td colspan="2">{{mb_field object=$sejour field="entree_prevue" form="editSejour" tabindex="4" register=true canNull=false onchange="updateDureePrevue();"}}</td>
    </tr>
  
    <tr>
      <th>
        <input type="hidden" name="patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$sejour->patient_id}}" />
        {{mb_label object=$sejour field="patient_id"}}
      </th>
      <td>
        <input type="text" name="patient_view" size="20" value="{{$patient->_view}}" tabindex="2" 
          {{if !$sejour->_id || $app->user_type == 1}} 
          ondblclick="PatSelector.init()" 
          {{/if}}
          readonly="readonly" />
        {{if !$sejour->_id || $app->user_type == 1}} 
          <button type="button" class="search" onclick="PatSelector.init()">
          	{{tr}}Choose{{/tr}}
					</button>
        {{/if}}
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
      <td>{{mb_field object=$sejour field=libelle form=editSejour tabindex="3"}}</td>
      <th>{{mb_label object=$sejour field=_duree_prevue}}</th>
      <td>
        <input type="text" name="_duree_prevue" class="num min|0" value="{{if $sejour->_id}}{{$sejour->_duree_prevue}}{{else}}0{{/if}}" tabindex="6" size="4" onchange="updateSortiePrevue()" />
        nuits
      </td>
      <td id="dureeEst"></td>
    </tr>
    
    {{if !$dialog}} 
    <tr>
      <td class="button" colspan="5">
        {{if $sejour->_id}}
          <button class="modify default" type="submit" tabindex="21">{{tr}}Save{{/tr}}</button>
          
          <button class="{{$sejour->annule|ternary:'change':'cancel'}}" type="button" tabindex="22" onclick="cancelSejourSSR();">
             {{tr}}{{$sejour->annule|ternary:'Restore':'Cancel'}}{{/tr}}
          </button>
          
          {{if $can->admin}}
            <button class="trash" type="button" tabindex="22" onclick="confirmDeletion(this.form,{typeName:'le séjour ',objName:'{{$sejour->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
          {{/if}}
              
        {{else}}
          <button class="submit default" tabindex="23" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
		{{/if}}
  </table>
</form>
