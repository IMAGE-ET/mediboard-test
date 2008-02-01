{{mb_include_script module="dPmedicament" script="medicament_selector"}}

<script type="text/javascript">

var Prescription = {
  close : function() {
    var url = new Url;
    url.setModuleTab("{{$m}}", "{{$tab}}");
    url.addParam("prescription_id", 0);
    url.addParam("object_class", "{{$prescription->object_class}}");
    url.addParam("object_id", {{$prescription->object_id}});
    url.redirect();
  },
  addLine: function(code) {
    var oForm = document.addLine;
    oForm.code_cip.value = code;
    submitFormAjax(oForm, 'systemMsg', { onComplete : Prescription.reload });
  },
  delLine: function(line_id) {
    var oForm = document.addLine;
    oForm.prescription_line_id.value = line_id;
    oForm.del.value = 1;
    submitFormAjax(oForm, 'systemMsg', { onComplete : Prescription.reload });
  },
  reload: function() {
    {{if $prescription->_id}}
    var urlPrescription = new Url;
    urlPrescription.setModuleAction("dPprescription", "httpreq_vw_prescription");
    urlPrescription.addParam("prescription_id", {{$prescription->_id}});
    urlPrescription.requestUpdate("prescription", { waitingText : null });
    {{/if}}
  },
  reloadAlertes: function() {
    {{if $prescription->_id}}
    var urlAlertes = new Url;
    urlAlertes.setModuleAction("dPprescription", "httpreq_alertes_icons");
    urlAlertes.addParam("prescription_id", {{$prescription->_id}});
    urlAlertes.requestUpdate("alertes", { waitingText : null });
    {{else}}
    alert('Pas de prescription en cours');
    {{/if}}
  }
};

// Visualisation du produit
function viewProduit(cip){
  var url = new Url;
  url.setModuleAction("dPmedicament", "vw_produit");
  url.addParam("CIP", cip);
  url.popup(840, 640, "Descriptif produit");
}

// UpdateFields de l'autocomplete
function updateFields(selected) {
  Element.cleanWhitespace(selected);
  dn = selected.childNodes;
  Prescription.addLine(dn[0].firstChild.nodeValue);
  $('searchProd_produit').value = "";
}

</script>

<table class="main">
{{if $prescription->object_id}}
  <tr>
    <td>
      {{if $prescription->_id}}
      <div id="alertes">
      {{include file="inc_alertes_icons.tpl"}}
      </div>
      {{/if}}
      <table class="tbl">
        <tr>
          <th class="title">
            {{$prescription->_ref_object->_ref_patient->_view}} -
            {{$prescription->_ref_object->_ref_patient->_naissance}}
            ({{$prescription->_ref_object->_ref_patient->_age}} ans)
          </th>
        </tr>
        <tr>
          <td class="text">
            {{assign var=dossier value=$prescription->_ref_object->_ref_patient->_ref_dossier_medical}}
            <strong>Addictions</strong>
						<ul>
						{{if $dossier->_ref_addictions}}
						  {{foreach from=$dossier->_ref_types_addiction key=curr_type item=list_addiction}}
						  {{if $list_addiction|@count}}
						  <li>
						    {{tr}}CAddiction.type.{{$curr_type}}{{/tr}}
						    {{foreach from=$list_addiction item=curr_addiction}}
						    <ul>
						      <li>
						        <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectViewHistory', params: { object_class: 'CAddiction', object_id: {{$curr_addiction->_id}} } })">
						          {{$curr_addiction->addiction}}
						        </span>
						      </li>
						    </ul>
						    {{/foreach}}
						  </li>
						  {{/if}}
						  {{/foreach}}
						  {{else}}
						  <li><em>Pas d'addictions</em></li>
						  {{/if}}
						</ul>
						<strong>Antécédents</strong>
						<ul>
						{{if $dossier->_ref_antecedents}}
						  {{foreach from=$dossier->_ref_antecedents key=curr_type item=list_antecedent}}
						  {{if $list_antecedent|@count}}
						  <li>
						    {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
						    {{foreach from=$list_antecedent item=curr_antecedent}}
						    <ul>
						      <li>
						        {{if $curr_antecedent->date}}
						          {{$curr_antecedent->date|date_format:"%d/%m/%Y"}} :
						        {{/if}}
						        <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectViewHistory', params: { object_class: 'CAntecedent', object_id: {{$curr_antecedent->_id}} } })">
						          {{$curr_antecedent->rques}}
						        </span>
						      </li>
						    </ul>
						    {{/foreach}}
						  </li>
						  {{/if}}
						  {{/foreach}}
						{{else}}
						  <li><em>Pas d'antécédents</em></li>
						{{/if}}
						</ul>
						<strong>Traitements</strong>
						<ul>
						  {{foreach from=$dossier->_ref_traitements item=curr_trmt}}
						  <li>
						    {{if $curr_trmt->fin}}
						      Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}} :
						    {{elseif $curr_trmt->debut}}
						      Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} :
						    {{/if}}
						    <span class="tooltip-trigger" onmouseover="ObjectTooltip.create(this, { mode: 'objectViewHistory', params: { object_class: 'CTraitement', object_id: {{$curr_trmt->_id}} } })">
						      {{$curr_trmt->traitement}}
						    </span>
						  </li>
						  {{foreachelse}}
						  <li><em>Pas de traitements</em></li>
						  {{/foreach}}
						</ul>
						<strong>Diagnostics</strong>
						<ul>
						  {{foreach from=$dossier->_ext_codes_cim item=curr_code}}
						  <li>
						    {{$curr_code->code}}: {{$curr_code->libelle}}
						  </li>
						  {{foreachelse}}
						  <li><em>Pas de diagnostic</em></li>
						  {{/foreach}}
						</ul>
          </td>
        </tr>
      </table>
      <table class="tbl">
        <tr>
          <th>Sejour</th>
        </tr>
        <tr>
          <td>{{$prescription->_ref_object->_ref_sejour->_view}}</td>
        </tr>
      </table>
      <table class="tbl">
        <tr>
          <th colspan="2">Liste des ordonnances</th>
        </tr>
        {{foreach from=$prescription->_ref_object->_ref_prescriptions item=curr_prescription}}
        <tr>
          <td>
            <button class="trash notext" onclick="alert('non fonctionnel')">
              {{tr}}Delete{{/tr}}
            </button>
          </td>
          <td class="text">
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;prescription_id={{$curr_prescription->_id}}" >
              {{$curr_prescription->_view}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
    </td>
    <td class="greedyPane">
      {{assign var=httpreq value=0}}
      <div id="prescription">
        {{include file="inc_vw_prescription.tpl"}}
      </div>
    </td>
  </tr>
{{else}}
  <tr>
    <td>Veuillez choisir un séjour ou une consultation (&object_class=CSejour&object_id=35976)</td>
  </tr>
{{/if}}
</table>