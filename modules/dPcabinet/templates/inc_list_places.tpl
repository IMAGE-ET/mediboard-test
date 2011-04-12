{{if $online && $plage->_id}}
<script type="text/javascript">

PlageConsult.setClose = function(time) {
  window.parent.PlageConsultSelector.set(time,
    "{{$plage->_id}}",
    "{{$plage->date|date_format:"%A %d/%m/%Y"}}",
    "{{$plage->freq}}",
    "{{$plage->chir_id}}",
    "{{$plage->_ref_chir->_view|smarty:nodefaults|escape:"javascript"}}");
  window.parent.Control.Modal.close();
};
PlageConsult.addPlaceBefore = function(plage_id) {
  var oForm = getForm("editPlage");
  var date = new Date();
  date.setHours({{$plage->debut|date_format:"%H"}});
  date.setMinutes({{$plage->debut|date_format:"%M"}} - {{$plage->freq|date_format:"%M"}});
  date.setSeconds({{$plage->debut|date_format:"%S"}});
  oForm.debut.value = date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
  submitFormAjax(oForm, "systemMsg", { onComplete: function() { PlageConsult.refreshPlage(); } });
};
PlageConsult.addPlaceAfter = function(plage_id) {
  var oForm = getForm("editPlage");
  var date = new Date();
  date.setHours({{$plage->fin|date_format:"%H"}});
  date.setMinutes({{$plage->fin|date_format:"%M"}} + {{$plage->freq|date_format:"%M"}});
  date.setSeconds({{$plage->fin|date_format:"%S"}});
  oForm.fin.value = date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
  submitFormAjax(oForm, "systemMsg", { onComplete: function() { PlageConsult.refreshPlage(); } });
};
{{/if}}

</script>
{{if $online && !$plage->locked}}
<form action="?m=dPcabinet" method="post" name="editPlage" onsubmit="return checkForm(this);">
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_plageconsult_aed" />
  <input type="hidden" name="plageconsult_id" value="{{$plage->_id}}" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="debut" value="{{$plage->debut}}" />
  <input type="hidden" name="fin" value="{{$plage->fin}}" />
  <input type="hidden" name="chir_id" value="{{$plage->chir_id}}" />
  <input type="hidden" name="_repeat" value="1" />
</form>
{{/if}}
<table class="tbl">
  {{if $plage->_id}}
  <tr>
    <th colspan="3">
      {{if $online}}
        {{mb_include module=system template=inc_object_notes object=$plage}}
      {{/if}}
      Dr {{$plage->_ref_chir->_view}}
      <br />
      Plage du {{$plage->date|date_format:$conf.longdate}} de {{$plage->debut|date_format:$conf.time}} � {{$plage->fin|date_format:$conf.time}}
    </th>
  </tr>
  {{if $online && !$plage->locked}}
  <tr>
    <td class="button" colspan="3">
      <button type="button" class="add singleclick" onclick="PlageConsult.addPlaceBefore()">
        Avant
      </button>
      <button type="button" class="add singleclick" onclick="PlageConsult.addPlaceAfter()">
        Apr�s
      </button>
    </td>
  </tr>
  {{/if}}
  <tr>
    <th>Heure</th>
    <th>Patient</th>
    <th>Dur�e</th>
  </tr>
  {{else}}
  <tr>
    <th colspan="3">Pas de plage le {{$date|date_format:$conf.longdate}}</th>
  </tr>
  {{/if}}
  {{foreach from=$listBefore item =_consultation}}
  <tr>
    <td>
      <div style="float:left">
        {{$_consultation->heure|date_format:$conf.time}}
      </div>
      <div style="float:right">
        {{if $_consultation->categorie_id}}
          <img src="./modules/dPcabinet/images/categories/{{$_consultation->_ref_categorie->nom_icone}}" alt="{{$_consultation->_ref_categorie->nom_categorie}}" title="{{$_consultation->_ref_categorie->nom_categorie}}" />
        {{/if}}
      </div>
    </td>
    <td>
      {{if !$_consultation->patient_id}}
        {{assign var="style" value="style='background: #ffa;'"}}
      {{elseif $_consultation->premiere}}
        {{assign var="style" value="style='background: #faa;'"}}
      {{else}} 
        {{assign var="style" value=""}}
      {{/if}}
      <div {{$style|smarty:nodefaults}}>
        {{if !$_consultation->patient_id}}
          [PAUSE]
          {{if $_consultation->motif}}
          ({{$_consultation->motif|truncate:"20"}})
          {{/if}}
        {{else}}
          {{$_consultation->_ref_patient->_view}}
          {{if $_consultation->motif}}
          ({{$_consultation->motif|truncate:"20"}})
          {{/if}}
        {{/if}}
      </div>
    </td>
    <td>
      {{if !$_consultation->patient_id}}
        {{assign var="style" value="style='background: #ffa;'"}}
      {{elseif $_consultation->premiere}}
        {{assign var="style" value="style='background: #faa;'"}}
      {{else}} 
        {{assign var="style" value=""}}
      {{/if}}
      <div {{$style|smarty:nodefaults}}>
        {{$_consultation->duree}}
      </div>
    </td>
  </tr>
  {{/foreach}}
  {{foreach from=$listPlace item=_place}}
  <tr>
    <td>
      <div style="float:left">
        {{if $online && !$plage->locked}}
          <button type="button" class="tick" onclick="PlageConsult.setClose('{{$_place.time}}')">{{$_place.time|date_format:$conf.time}}</button>
        {{else}}
          {{$_place.time|date_format:$conf.time}}
        {{/if}}
      </div>
      <div style="float:right">
      {{foreach from=$_place.consultations item=_consultation}}
			  {{if $_consultation->categorie_id}}
          <img src="./modules/dPcabinet/images/categories/{{$_consultation->_ref_categorie->nom_icone}}" alt="{{$_consultation->_ref_categorie->nom_categorie}}" title="{{$_consultation->_ref_categorie->nom_categorie}}" />
				{{/if}}
      {{/foreach}}
      </div>
    </td>
    <td class="text">
      {{foreach from=$_place.consultations item=_consultation}}
      
      {{if !$_consultation->patient_id}}
        {{assign var="style" value="style='background: #ffa;'"}}
      {{elseif $_consultation->premiere}}
        {{assign var="style" value="style='background: #faa;'"}}
      {{else}} 
        {{assign var="style" value=""}}
      {{/if}}
      <div {{$style|smarty:nodefaults}}>
        {{if !$_consultation->patient_id}}
          [PAUSE]
          {{if $_consultation->motif}}
          ({{$_consultation->motif|truncate:"20"}})
          {{/if}}
        {{else}}
          {{$_consultation->_ref_patient->_view}}
          {{if $_consultation->motif}}
          ({{$_consultation->motif|truncate:"20"}})
          {{/if}}
        {{/if}}
      </div>
      {{/foreach}}
    </td>
    <td>
      {{foreach from=$_place.consultations item=_consultation}}
        {{if !$_consultation->patient_id}}
          {{assign var="style" value="style='background: #ffa;'"}}
        {{elseif $_consultation->premiere}}
          {{assign var="style" value="style='background: #faa;'"}}
        {{else}} 
          {{assign var="style" value=""}}
        {{/if}}
        <div {{$style|smarty:nodefaults}}>
          {{$_consultation->duree}}
        </div>
      {{/foreach}}
    </td>
  </tr>
  {{/foreach}}
  {{foreach from=$listAfter item =_consultation}}
  <tr>
    <td>
      <div style="float:left">
        {{$_consultation->heure|date_format:$conf.time}}
      </div>
      <div style="float:right">
        {{if $_consultation->categorie_id}}
          <img src="./modules/dPcabinet/images/categories/{{$_consultation->_ref_categorie->nom_icone}}" alt="{{$_consultation->_ref_categorie->nom_categorie}}" title="{{$_consultation->_ref_categorie->nom_categorie}}" />
        {{/if}}
      </div>
    </td>
    <td>
      {{if !$_consultation->patient_id}}
        {{assign var="style" value="style='background: #ffa;'"}}
      {{elseif $_consultation->premiere}}
        {{assign var="style" value="style='background: #faa;'"}}
      {{else}} 
        {{assign var="style" value=""}}
      {{/if}}
      <div {{$style|smarty:nodefaults}}>
        {{if !$_consultation->patient_id}}
          [PAUSE]
          {{if $_consultation->motif}}
          ({{$_consultation->motif|truncate:"20"}})
          {{/if}}
        {{else}}
          {{$_consultation->_ref_patient->_view}}
          {{if $_consultation->motif}}
          ({{$_consultation->motif|truncate:"20"}})
          {{/if}}
        {{/if}}
      </div>
    </td>
    <td>
      {{if !$_consultation->patient_id}}
        {{assign var="style" value="style='background: #ffa;'"}}
      {{elseif $_consultation->premiere}}
        {{assign var="style" value="style='background: #faa;'"}}
      {{else}} 
        {{assign var="style" value=""}}
      {{/if}}
      <div {{$style|smarty:nodefaults}}>
        {{$_consultation->duree}}
      </div>
    </td>
  </tr>
  {{/foreach}}
</table>