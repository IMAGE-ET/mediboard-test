{{if $online && $plage->_id}}
<script type="text/javascript">

PlageConsult.setClose = function(time) {
  window.parent.PlageConsultSelector.set(time,
    "{{$plage->_id}}",
    "{{$plage->date|date_format:"%A %d/%m/%Y"}}",
    "{{$plage->chir_id}}");
  window.close();
  var form = window.parent.getForm(window.parent.PlageConsultSelector.sForm);
  if (Preferences.choosePatientAfterDate == 1 && !$V(form.patient_id) && !form._pause.checked) {
    window.parent.PatSelector.init();
  }
};
PlageConsult.addPlaceBefore = function(plage_id) {
  var oForm = getForm("editPlage");
  var date = new Date();
  date.setHours({{$plage->debut|date_format:"%H"}});
  date.setMinutes({{$plage->debut|date_format:"%M"}} - {{$plage->freq|date_format:"%M"}});
  date.setSeconds({{$plage->debut|date_format:"%S"}});
  oForm.debut.value = printf('%02d:%02d:%02d',date.getHours(), date.getMinutes(), date.getSeconds());
  submitFormAjax(oForm, "systemMsg", { onComplete: function() { PlageConsult.refreshPlage(); } });
};
PlageConsult.addPlaceAfter = function(plage_id) {
  var oForm = getForm("editPlage");
  var date = new Date();
  date.setHours({{$plage->fin|date_format:"%H"}});
  date.setMinutes({{$plage->fin|date_format:"%M"}} + {{$plage->freq|date_format:"%M"}});
  date.setSeconds({{$plage->fin|date_format:"%S"}});
  oForm.fin.value = printf('%02d:%02d:%02d', date.getHours(), date.getMinutes(), date.getSeconds());
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
  {{assign var=display_nb_consult value=$conf.dPcabinet.display_nb_consult}}
  {{if $plage->_id}}
  <tr>
    <th colspan="{{if $display_nb_consult}}5{{else}}3{{/if}}">
      {{if $online}}
        {{mb_include module=system template=inc_object_notes object=$plage}}
      {{/if}}
      Dr {{$plage->_ref_chir->_view}}
      <br />
      {{if $app->user_prefs.viewFunctionPrats}}
        {{$plage->_ref_chir->_ref_function->_view}}
        <br />
      {{/if}}
      Plage du {{$plage->date|date_format:$conf.longdate}} de {{$plage->debut|date_format:$conf.time}} à {{$plage->fin|date_format:$conf.time}}
    </th>
  </tr>
  {{if $online && !$plage->locked}}
  <tr>
    <td class="button" colspan="{{if $display_nb_consult}}5{{else}}3{{/if}}">
      <button type="button" class="add singleclick" onclick="PlageConsult.addPlaceBefore()" {{if !$plage->_canEdit}}disabled="disabled"{{/if}}>
        Avant
      </button>
      <button type="button" class="add singleclick" onclick="PlageConsult.addPlaceAfter()" {{if !$plage->_canEdit}}disabled="disabled"{{/if}}>
        Après
      </button>
    </td>
  </tr>
  {{/if}}
  <tr>
    <th class="narrow" {{if $online}}rowspan="2"{{/if}}>Heure</th>
    <th {{if $online}}rowspan="2"{{/if}}>Patient</th>
    {{if $display_nb_consult != "none" && $online}}
      <th colspan="{{if $display_nb_consult == "cab"}}2{{else}}3{{/if}}" class="narrow">Occupation</th>
    {{/if}}
  </tr>
  {{if $online}}
    <tr>
      {{if $display_nb_consult == "cab" || $display_nb_consult == "etab"}}
        <th>Cab.</th>
      {{/if}}
      {{if $display_nb_consult == "etab"}}
        <th>Etab.</th>
      {{/if}}
    </tr>
  {{/if}}
  {{else}}
  <tr>
    <th colspan="{{if $display_nb_consult}}5{{else}}3{{/if}}">Pas de plage le {{$date|date_format:$conf.longdate}}</th>
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
      {{elseif $_consultation->derniere}}
        {{assign var="style" value="style='background: #faf;'"}}
      {{else}} 
        {{assign var="style" value=""}}
      {{/if}}
      <div {{$style|smarty:nodefaults}}>
        {{$_consultation->patient_id|ternary:$_consultation->_ref_patient:"[PAUSE]"}}
        {{if $_consultation->duree > 1}}
          x{{$_consultation->duree}}
        {{/if}}
        {{if $_consultation->motif}}
          <div class="compact">
            {{$_consultation->motif|spancate}}
          </div>
        {{/if}}
      </div>
    </td>
    <td {{if $display_nb_consult}}colspan="3"{{/if}}></td>
  </tr>
  {{/foreach}}
  {{foreach from=$listPlace item=_place}}
  <tr>
    <td>
      <div style="float:left">
        {{assign var=count_places value=$_place.consultations|@count}}
        {{if $online && !$plage->locked && ($conf.dPcabinet.CConsultation.surbooking_readonly || $plage->_canEdit || $count_places == 0)}}
          <button type="button" class="tick" onclick="PlageConsult.setClose('{{$_place.time}}')">{{$_place.time|date_format:$conf.time}}</button>
        {{else}}
          {{$_place.time|date_format:$conf.time}}
        {{/if}}
      </div>
    </td>
    <td class="text">
      {{foreach from=$_place.consultations item=_consultation}}
      
      {{if !$_consultation->patient_id}}
        {{assign var="style" value="style='background: #ffa;'"}}
      {{elseif $_consultation->premiere}}
        {{assign var="style" value="style='background: #faa;'"}}
      {{elseif $_consultation->derniere}}
        {{assign var="style" value="style='background: #faf;'"}}
      {{else}} 
        {{assign var="style" value=""}}
      {{/if}}
      <div {{$style|smarty:nodefaults}}>
        {{$_consultation->patient_id|ternary:$_consultation->_ref_patient:"[PAUSE]"}}
        {{if $_consultation->duree > 1}}
          x{{$_consultation->duree}}
        {{/if}}
        {{assign var=categorie value=$_consultation->_ref_categorie}}
        {{if $categorie->_id}}
          <div class="compact">
            <img src="./modules/dPcabinet/images/categories/{{$categorie->nom_icone}}" alt="{{$categorie->nom_categorie}}" title="{{$categorie->nom_categorie}}" />
            {{$categorie->nom_categorie|spancate}}
          </div>
        {{/if}}
        {{if $_consultation->motif}}
          <div class="compact">
            {{$_consultation->motif|spancate}}
          </div>
        {{/if}}
        {{if $_consultation->rques}}
          <div class="compact">
            {{$_consultation->rques|spancate}}
          </div>
        {{/if}}
      </div>
      {{/foreach}}
    </td>
    {{if $online}}
      {{assign var=time value=$_place.time}}
      {{if $display_nb_consult == "cab" || $display_nb_consult == "etab"}}
        <td>
          {{mb_include module=cabinet template=inc_vw_jeton nb=$utilisation_func.$time quotas=$quotas}}
        </td>
      {{/if}}
      {{if $display_nb_consult == "etab"}}
        <td>
          {{mb_include module=cabinet template=inc_vw_jeton nb=$utilisation_etab.$time}}
        </td>
      {{/if}}
    {{/if}}
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
      {{elseif $_consultation->derniere}}
        {{assign var="style" value="style='background: #faf;'"}}
      {{else}} 
        {{assign var="style" value=""}}
      {{/if}}
      <div {{$style|smarty:nodefaults}}>
        {{$_consultation->patient_id|ternary:$_consultation->_ref_patient:"[PAUSE]"}}
        {{if $_consultation->duree > 1}}
          x{{$_consultation->duree}}
        {{/if}}
        {{if $_consultation->motif}}
          <div class="compact">
            {{$_consultation->motif|spancate}}
          </div>
        {{/if}}
      </div>
    </td>
    <td {{if $display_nb_consult}}colspan="3"{{/if}}></td>
  </tr>
  {{/foreach}}
</table>