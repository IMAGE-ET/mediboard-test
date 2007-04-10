<!-- $Id: inc_vw_patient.tpl 1738 2007-03-19 16:33:47Z maskas $ -->

{{assign var="trClass" value=""}}
{{if $patVitale}}
  {{if $_patient->nom == $patVitale->nom && $_patient->prenom == $patVitale->prenom && $_patient->naissance == $patVitale->naissance}}
  {{assign var="trClass" value="selected"}}
  {{/if}}
{{/if}}
<tr class="{{$trClass|default:'toto'}}">
  {{assign var="nbConsults" value=$_patient->_ref_consultations|@count}}
  <td rowspan="{{$nbConsults+1}}">
    {{$_patient->_view}}</td>
  <td>{{$_patient->_naissance}}</td>
  
  {{if $patVitale}}
  <td>{{mb_value object=$_patient field="matricule"}}</td>
  <td>{{mb_value object=$_patient field="adresse"}}</td>
  <td class="button" rowspan="{{$nbConsults+1}}">
    <button class="tick" type="button" onclick="Patient.selectAndUpdate({{$_patient->_id}})">
      {{tr}}Update and select{{/tr}}
    </button>
  </td>

  {{else}}
  <td>{{$_patient->tel}}</td>
  <td>{{$_patient->tel2}}</td>
  <td class="button" rowspan="{{$nbConsults+1}}">
    <button class="edit" type="button" onclick="Patient.edit({{$_patient->_id}})">
      {{tr}}Edit{{/tr}}
    </button>
    <button class="tick" type="button" onclick="Patient.select({{$_patient->_id}}, '{{$_patient->_view|smarty:nodefaults|JSAttribute}}')">
      {{tr}}Select{{/tr}}
    </button>
  </td>

  {{/if}}
</tr>

<!-- Consultations du jour -->
{{foreach from=$_patient->_ref_consultations item=_consult}}
<tr>
  <td colspan="3">
    Consultation aujourd'hui à {{mb_value object=$_consult field=heure}}
    avec Dr. {{$_consult->_ref_plageconsult->_ref_chir->_view}}
  </td>
</tr>
{{/foreach}}
