{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPpatients" script="pat_selector"}}
{{mb_include_script module="dPplanningOp" script="cim10_selector"}}
{{mb_include_script module="dPmedicament" script="medicament_selector"}}
{{mb_include_script module="dPmedicament" script="equivalent_selector"}}

<script type="text/javascript">
  
function updateSortiePrevue() {
  var oForm = document.editFicheAutonomie;
    
  if (!oForm._duree_prevue.value) {
    $V(oForm._duree_prevue, 0);
  }
  
  var sDate = oForm._entree.value;
  if (!sDate) {
    return;
  }
  
  // Add days
  var dDate = Date.fromDATETIME(sDate);
  var nDuree = parseInt(oForm._duree_prevue.value, 10);
    
  dDate.addDays(nDuree);

  // Update fields
  $V(oForm._sortie, dDate.toDATETIME());
  oView = getForm('editFicheAutonomie')._sortie_da;
  $V(oView, dDate.toLocaleDateTime());
}

function updateDureePrevue() {
  var oForm = document.editFicheAutonomie;
  
  if(oForm._entree.value) {
    var dEntree = Date.fromDATETIME(oForm._entree.value);
    var dSortie = Date.fromDATETIME(oForm._sortie.value);
    var iSecondsDelta = dSortie - dEntree;
    var iDaysDelta = iSecondsDelta / (24 * 60 * 60 * 1000);
    oForm._duree_prevue.value = Math.floor(iDaysDelta);
  }
}

function reloadDiagnostic(sejour_id, modeDAS) {
  var url = new Url("dPsalleOp", "httpreq_diagnostic_principal");
  url.addParam("sejour_id", sejour_id);
  url.addParam("modeDAS", modeDAS);
  url.requestUpdate("cim");
}

{{if $can_view_dossier_medical}}
function loadAntecedents(sejour_id){
  var url = new Url("dPcabinet","httpreq_vw_antecedents");
  url.addParam("sejour_id", sejour_id);
  url.requestUpdate('antecedents')
}
{{/if}}

function cancelFicheAutonomie() {
  var oForm = document.editFicheAutonomie;
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

Main.add(function () {
var tab_actes = Control.Tabs.create('tab-fiche-autonomie', false);

{{if $can_view_dossier_medical && $sejour->_id}}
  loadAntecedents({{$sejour->_id}});
{{/if}}
});
</script>

<form name="editFicheAutonomie" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="m" value="ssr" />
  <input type="hidden" name="dosql" value="do_fiche_autonomie_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="fiche_autonomie_id" value="{{$fiche_autonomie->_id}}" />
  <input type="hidden" name="sejour_id" value="{{$sejour->_id}}" />
  <input type="hidden" name="_annule" value="{{$fiche_autonomie->_annule|default:"0"}}" />
  
  <input type="hidden" name="_bind_sejour" value="1" />
  <a class="button new" href="?m=ssr&amp;tab=vw_aed_fiche_autonomie&amp;fiche_autonomie_id=0">
    Ajouter un patient
  </a>
  <table class="form">
    <tr>
      {{if $fiche_autonomie->_id}}
      <th class="title modify" colspan="5">
        {{mb_include module=system template=inc_object_notes      object=$fiche_autonomie}}
        {{mb_include module=system template=inc_object_idsante400 object=$fiche_autonomie}}
        {{mb_include module=system template=inc_object_history    object=$fiche_autonomie}}
  
        <a class="action" style="float: right;" title="Modifier uniquement le sejour" href="?m=dPplanningOp&amp;tab=vw_edit_sejour&amp;sejour_id={{$sejour->_id}}">
          <img src="images/icons/edit.png" alt="modifier" />
        </a>
        {{tr}}CFicheAutonomie-title-modify{{/tr}} {{$fiche_autonomie}}
        {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}
      </th>
      {{else}}
      <th class="title" colspan="5">
        {{tr}}CFicheAutonomie-title-create{{/tr}} 
        {{if $sejour->_num_dossier}}
          pour le dossier
          {{mb_include module=dPplanningOp template=inc_vw_numdos num_dossier=$sejour->_num_dossier}}
        {{/if}}
      </th>
      {{/if}}
    </tr>
  
    {{if $fiche_autonomie->_annule}}
    <tr>
      <th class="category cancelled" colspan="5">
      {{tr}}CFicheAutonomie-_annule{{/tr}}
      </th>
    </tr>
    {{/if}}
    
    <tr>
      <th>
        {{mb_label object=$fiche_autonomie field="_group_id"}}
      </th>
      <td>
        <select class="{{$fiche_autonomie->_props._group_id}}" name="_group_id">
        {{foreach from=$etablissements item=_etab}}
          <option value="{{$_etab->group_id}}" {{if ($fiche_autonomie->sejour_id && $fiche_autonomie->_group_id == $_etab->group_id) || 
          (!$fiche_autonomie->sejour_id && $g == $_etab->group_id)}} selected="selected"{{/if}}>
            {{$_etab->text}}
          </option>
        {{/foreach}}
        </select>
      </td>

      <th>{{mb_label object=$fiche_autonomie field="_entree"}}</th>
      <td colspan="2">{{mb_field object=$fiche_autonomie field="_entree" form="editFicheAutonomie" register=true canNull=false onchange="updateDureePrevue();"}}</td>
    </tr>
  
    <tr>
      <th>{{mb_label object=$fiche_autonomie field="_praticien_id"}}</th>
      <td>
        <select name="_praticien_id" class="{{$fiche_autonomie->_props._praticien_id}}">
          <option value="">&mdash; Choisir un praticien</option>
          {{foreach from=$listPrats item=_user}}
          <option value="{{$_user->_id}}" class="mediuser" 
            style="border-color: #{{$_user->_ref_function->color}}" {{if $_user->_id == $fiche_autonomie->_praticien_id}}selected="selected"{{/if}}>
            {{$_user->_view}}
          </option>
          {{/foreach}}
        </select>
      </td>
      
      <th>{{mb_label object=$fiche_autonomie field="_duree_prevue"}}</th>
      <td>
        <input type="text" name="_duree_prevue" class="num min|0" value="{{if $fiche_autonomie->sejour_id}}{{$fiche_autonomie->_duree_prevue}}{{else}}0{{/if}}" size="4" onchange="updateSortiePrevue()" />
        nuits
      </td>
      <td id="dureeEst"> </td>
    </tr>
    
    <tr>
      <th>
        <input type="hidden" name="_patient_id" class="{{$sejour->_props.patient_id}}" ondblclick="PatSelector.init()" value="{{$fiche_autonomie->_patient_id}}" />
        {{mb_label object=$fiche_autonomie field="_patient_id"}}
      </th>
      <td>
        <input type="text" name="_patient_view" size="20" value="{{$patient->_view}}" 
          {{if !$sejour->_id || $app->user_type == 1}} 
            ondblclick="PatSelector.init()" 
          {{/if}}
        readonly="readonly" />
        {{if !$sejour->_id || $app->user_type == 1}} 
          <button type="button" class="search" onclick="PatSelector.init()">Choisir un patient</button>
        {{/if}}
        <script type="text/javascript">
          PatSelector.init = function(){
            this.sForm = "editFicheAutonomie";
            this.sId   = "_patient_id";
            this.sView = "_patient_view";
            this.pop();
          }
        </script>
      </td>
      
      <th>{{mb_label object=$fiche_autonomie field="_sortie"}}</th>
      <td colspan="2">{{mb_field object=$fiche_autonomie field="_sortie" form="editFicheAutonomie" register=true canNull=false onchange="updateDureePrevue();"}}</td>
    </tr>
    
    <tr>
      <td class="button" colspan="5">
        {{if $fiche_autonomie->_id}}
          <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
          {{mb_ternary var=annule_text test=$sejour->annule value="Rétablir" other="Annuler"}}
          {{mb_ternary var=annule_class test=$sejour->annule value="change" other="cancel"}}
          
          <button class="{{$annule_class}}" type="button" onclick="cancelFicheAutonomie();">
            {{$annule_text}}
          </button>
          
          {{if $can->admin}}
            <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la fiche d\'autonomie ',objName:'{{$fiche_autonomie->_view|smarty:nodefaults|JSAttribute}}'})">
              {{tr}}Delete{{/tr}}
            </button>
          {{/if}}
              
        {{else}}
          <button class="submit" name="btnFuseAction" type="submit">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>

{{if $fiche_autonomie->_id && $can->edit}}
<ul id="tab-fiche-autonomie" class="control_tabs">
  {{if $can_view_dossier_medical}}
  <li><a href="#antecedents">{{tr}}CAntecedent{{/tr}}</a></li>
  {{/if}} 
  <li><a href="#autonomie">{{tr}}CFicheAutonomie{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />  

{{if $can_view_dossier_medical}}
<div id="antecedents" style="display: none;">
  <div class="small-info">
    Veuillez sélectionner un séjour dans la liste de gauche pour pouvoir
    consulter et modifier les antécédents du patient concerné.
  </div>
</div>
{{/if}}

<div id="autonomie" style="display: none;">
  <form name="editFicheAutonomieDetails" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this);">
    <input type="hidden" name="m" value="ssr" />
    <input type="hidden" name="fiche_autonomie_id" value="{{$fiche_autonomie->_id}}" />
    <input type="hidden" name="dosql" value="do_fiche_autonomie_aed" />
    <input type="hidden" name="del" value="0" />
    
    <table class="form">
      <tr>
        <th class="category" rowspan="12" style="width:0.1%">{{tr}}CFicheAutonomie-autonomie-perso{{/tr}}</th>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="alimentation"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="alimentation" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="toilette"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="toilette" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="habillage_haut"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="habillage_haut" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="habillage_bas"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="habillage_bas" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="utilisation_toilette"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="utilisation_toilette" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="transfert_lit"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="transfert_lit" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="locomotion"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="locomotion" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="locomotion_materiel"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="locomotion_materiel" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="escalier"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="escalier" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="pansement"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="pansement" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>" default=""}}</td>
        <td colspan="2"></td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="escarre"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="escarre" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>" default=""}}</td>
        <td colspan="2"></td>
      </tr>
      <tr>
        <td colspan="6"></td>
      </tr>
      <tr>
        <th class="category" rowspan="5">{{tr}}CFicheAutonomie-capacite_relationnelle{{/tr}}</th>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="comprehension"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="comprehension" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
        <td></td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="expression"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="expression" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
        <td></td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="memoire"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="memoire" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
        <td></td>
      </tr>
      <tr>
        <th>{{mb_label object=$fiche_autonomie field="resolution_pb"}}</th>
        <td>{{mb_field object=$fiche_autonomie field="resolution_pb" typeEnum="radio" onchange="this.form.onsubmit();" separator="</td><td>"}}</td>
        <td></td>
      </tr>
      <tr>
        <td colspan="6"></td>
      </tr>
      <tr>
        <th class="category">{{mb_label object=$fiche_autonomie field="etat_psychique"}}</th>
        <td colspan="5">{{mb_field object=$fiche_autonomie field="etat_psychique" onchange="this.form.onsubmit();"}}</td>
      </tr>
      <tr>
        <td colspan="6"></td>
      </tr>
      <tr>
        <th class="category">{{mb_label object=$fiche_autonomie field="devenir_envisage"}}</th>
        <th>
            Domicile <input type="radio" name="_devenir_envisage" value="1" {{if !$fiche_autonomie->devenir_envisage}}checked="checked"{{/if}} onchange="$V(this.form.devenir_envisage,''); $('devenir').hide(); this.form.onsubmit();"/>
            Autres <input type="radio" name="_devenir_envisage" value="0" {{if $fiche_autonomie->devenir_envisage}}checked="checked"{{/if}} onchange="$('devenir').show();"/>
        </th>
        <td colspan="4">
          <div id="devenir" {{if !$fiche_autonomie->devenir_envisage}}style="display: none"{{/if}}>
            {{mb_field object=$fiche_autonomie field="devenir_envisage" onchange="this.form.onsubmit();"}}
          </div>
        </td>
      </tr>
    </table>
  </form>
</div>
{{/if}}