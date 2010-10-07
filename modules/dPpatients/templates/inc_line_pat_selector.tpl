<!-- $Id$ -->

{{assign var="trClass" value=""}}
{{if $patVitale}}
  {{if $_patient->nom == $patVitale->nom && $_patient->prenom == $patVitale->prenom && $_patient->naissance == $patVitale->naissance}}
  {{assign var="trClass" value="selected"}}
  {{/if}}
{{/if}}

<tr class="{{$trClass}}">
  {{assign var="nbConsults" value=$_patient->_ref_consultations|@count}}
  {{assign var="nbSejours" value=$_patient->_ref_sejours|@count}}
  {{assign var="rowspan" value=$nbConsults+$nbSejours+1}}
  <td rowspan="{{$rowspan}}">
    <div style="float: right;">
      {{mb_include module=system template=inc_object_notes object=$_patient}}
    </div>
    
    {{if $patVitale && $_patient->_id == $patVitale->_id}}
      <img style="float: right;" src="images/icons/carte_vitale.png" title="Bénéficiaire associé à la carte Vitale" />
    {{/if}}
    
    {{if $_patient->fin_amo && $_patient->fin_amo < $smarty.now|date_format:"%Y-%m-%d"}}
      <img style="float: right;" src="images/icons/warning.png" title="Période de droits terminée ({{mb_value object=$_patient field=fin_amo}})" />
    {{/if}}
    
    <span style="margin-right: 16px;">{{$_patient}}</span>
  </td>
  <td>{{mb_value object=$_patient field="naissance"}}</td>
  
  {{if $patVitale}}
  <td>{{mb_value object=$_patient field="matricule"}}</td>
  <td>{{mb_value object=$_patient field="adresse"}}</td>
  <td class="button" rowspan="{{$rowspan}}">
    {{if $can->edit}}
    <button class="edit" type="button" onclick="Patient.edit({{$_patient->_id}})">
      {{tr}}Update{{/tr}} avec Vitale
    </button>
    {{/if}}
    <button class="tick" type="button" onclick="Patient.select({{$_patient->_id}}, '{{$_patient->_view|smarty:nodefaults|JSAttribute}}')">
      {{tr}}Select{{/tr}}
    </button>
  </td>

  {{else}}
  <td>
  	{{mb_value object=$_patient field=tel}}
		{{if $_patient->tel2}} 
      <br />{{mb_value object=$_patient field=tel2}}
		{{/if}}
	</td>
  <td class="text" style="color: #666;">
    <small>
      <span style="white-space: nowrap;">{{$_patient->adresse|spancate:30}} - </span>
      <span style="white-space: nowrap;">{{$_patient->cp}} {{$_patient->ville|spancate:20}}</span>
    </small>
	</td>
  <td class="button" rowspan="{{$rowspan}}" style="white-space: nowrap;">
    {{if $can->edit}}
    <button class="edit" type="button" onclick="Patient.edit({{$_patient->_id}})">
      {{tr}}Edit{{/tr}}
    </button>
    {{/if}}
    <button class="tick" type="button" onclick="Patient.select({{$_patient->_id}}, '{{$_patient->_view|smarty:nodefaults|JSAttribute}}')">
      {{tr}}Select{{/tr}}
    </button>
  </td>

  {{/if}}
</tr>

<!-- Consultations du jour -->
{{foreach from=$_patient->_ref_consultations item=_consult}}
<tr>
  <td colspan="3" class="text">
    Consult. aujourd'hui à {{mb_value object=$_consult field=heure}}
    avec le Dr {{$_consult->_ref_plageconsult->_ref_chir->_view}}
  </td>
</tr>
{{/foreach}}

<!-- Admissions du jour -->
{{foreach from=$_patient->_ref_sejours item=_sejour}}
<tr>
  <td colspan="3" class="text">
    Admission aujourd'hui à {{mb_value object=$_sejour field=entree_prevue format="%Hh%M"}}
    pour le Dr {{$_sejour->_ref_praticien->_view}}
  </td>
</tr>
{{/foreach}}
