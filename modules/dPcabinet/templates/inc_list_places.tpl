{{if $online && !$plage->locked}}

  <script>
    PlageConsult.setClose = function(time, multiples, elt) {
      if (multiples) {
        var plage_id  = $(elt).get("plageid");
        var date      = $(elt).get("date");
        var chir_id   = $(elt).get("chir");
        var chir_name   = $(elt).get("chir_name");
        window.parent.PlageConsultSelector.addOrRemoveConsult(
          plage_id,
          date,
          time,
          chir_id,
          chir_name);
      }
      // simple
      else {
        window.parent.PlageConsultSelector.set(time,
          "{{$plage->_id}}",
          "{{$plage->date|date_format:"%A %d/%m/%Y"}}",
          "{{$plage->chir_id}}");
        window.close();
        var form = window.parent.getForm(window.parent.PlageConsultSelector.sForm);
        if (Preferences.choosePatientAfterDate == 1 && !$V(form.patient_id) && !form._pause.checked) {
          window.parent.PatSelector.init();
        }
      }
    };

    PlageConsult.addPlaceBefore = function(plage_id) {
      var form = getForm("editPlage-"+plage_id);
      var page_id = form.up().id.split("-")[1]; //get parent
      var date = new Date();
      date.setHours({{$plage->debut|date_format:"%H"}});
      date.setMinutes({{$plage->debut|date_format:"%M"}} - {{$plage->freq|date_format:"%M"}});
      date.setSeconds({{$plage->debut|date_format:"%S"}});
      form.debut.value = printf('%02d:%02d:%02d',date.getHours(), date.getMinutes(), date.getSeconds());
      return onSubmitFormAjax(form, function() { PlageConsult.refreshPlage(page_id, "{{$multiple}}"); });
    };

    PlageConsult.addPlaceAfter = function(plage_id) {
      var form = getForm("editPlage-"+plage_id);
      var page_id = form.up().id.split("-")[1]; //get parent
      var date = new Date();
      date.setHours({{$plage->fin|date_format:"%H"}});
      date.setMinutes({{$plage->fin|date_format:"%M"}} + {{$plage->freq|date_format:"%M"}});
      date.setSeconds({{$plage->fin|date_format:"%S"}});
      form.fin.value = printf('%02d:%02d:%02d', date.getHours(), date.getMinutes(), date.getSeconds());
      return onSubmitFormAjax(form, function() { PlageConsult.refreshPlage(page_id, "{{$multiple}}"); });
    };
  </script>

  <form action="?m=dPcabinet" method="post" name="editPlage-{{$plage->_id}}" onsubmit="return checkForm(this);">
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

<table class="tbl" id="Places_{{$plage->_id}}">
  {{assign var=display_nb_consult value=$conf.dPcabinet.display_nb_consult}}
  {{if $plage->_id}}
    <tr>
      <th colspan="
      {{if !$multiple}}
        {{if $display_nb_consult}}5{{else}}3{{/if}}
      {{else}}
        {{if $display_nb_consult}}4{{else}}2{{/if}}
      {{/if}}">
        {{if $online}}
          {{mb_include module=system template=inc_object_notes object=$plage}}
        {{/if}}
        {{$plage->_ref_chir}}
        <br />
        {{if $app->user_prefs.viewFunctionPrats}}
          {{$plage->_ref_chir->_ref_function}}
          <br />
        {{/if}}
        {{if !$multiple}}Plage du {{$plage->date|date_format:$conf.longdate}}{{else}}{{$plage->date|date_format:"%a %d %b"}}{{/if}}
        de {{$plage->debut|date_format:$conf.time}}
        à {{$plage->fin|date_format:$conf.time}}
      </th>
    </tr>
    {{if $online && !$plage->locked}}
      <tr>
        <td class="button" colspan="{{if $display_nb_consult}}5{{else}}3{{/if}}">
          <button type="button" class="add singleclick" onclick="PlageConsult.addPlaceBefore('{{$plage->_id}}')" {{if !$plage->_canEdit}}disabled="disabled"{{/if}}>
            Avant
          </button>
          <button type="button" class="add singleclick" onclick="PlageConsult.addPlaceAfter('{{$plage->_id}}')" {{if !$plage->_canEdit}}disabled="disabled"{{/if}}>
            Après
          </button>
        </td>
      </tr>
    {{/if}}
    <tr>
      <th class="narrow" {{if $online && !$multiple}}rowspan="2"{{/if}}>Heure</th>
      <th {{if $online && !$multiple}}rowspan="2"{{/if}}>Patient</th>
      {{if $display_nb_consult != "none" && $online && !$multiple}}
        <th colspan="{{if $display_nb_consult == "cab"}}2{{else}}3{{/if}}" class="narrow">Occupation</th>
      {{/if}}
    </tr>
    {{if $online && !$multiple}}
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
          {{mb_include module=cabinet template=inc_icone_categorie_consult
            categorie=$_consultation->_ref_categorie
          }}
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
            {{if !$multiple}}
              <button type="button" class="tick" onclick="PlageConsult.setClose('{{$_place.time}}')">{{$_place.time|date_format:$conf.time}}</button>
            {{else}}
              <label>
                <input name="checkbox" type="radio" data-chir_name="{{$plage->_ref_chir}}" data-plageid="{{$plage->_id}}" data-date="{{$plage->date}}" data-chir="{{$plage->chir_id}}" onclick="PlageConsult.setClose('{{$_place.time}}',true, this)"> {{$_place.time|date_format:$conf.time}}
              </label>
            {{/if}}
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
              {{mb_include module=cabinet template=inc_icone_categorie_consult
                categorie=$categorie
                display_name=true
              }}
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
      {{if $online && !$multiple}}
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
        <div style="float: left;">
          {{$_consultation->heure|date_format:$conf.time}}
        </div>
        <div style="float: right;">
          {{if $_consultation->categorie_id}}
            {{mb_include module=cabinet template=inc_icone_categorie_consult
              categorie=$_consultation->_ref_categorie
            }}
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