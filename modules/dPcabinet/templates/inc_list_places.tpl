{{if $online && !$plage->locked}}

  <script>
    addPlaceBefore = function(plage_id, slot_id, consult_id) {
      var form = getForm("editPlage-"+plage_id+"-"+slot_id);
      var date = new Date();
      date.setHours({{$plage->debut|date_format:"%H"}});
      date.setMinutes({{$plage->debut|date_format:"%M"}} - {{$plage->freq|date_format:"%M"}});
      date.setSeconds({{$plage->debut|date_format:"%S"}});
      form.debut.value = printf('%02d:%02d:%02d',date.getHours(), date.getMinutes(), date.getSeconds());
      return onSubmitFormAjax(form, function() { RDVmultiples.refreshSlot(slot_id, plage_id, consult_id, "{{$multiple}}"); });
    };

    addPlaceAfter = function(plage_id, slot_id, consult_id) {
      var form = getForm("editPlage-"+plage_id+"-"+slot_id);
      var date = new Date();
      date.setHours({{$plage->fin|date_format:"%H"}});
      date.setMinutes({{$plage->fin|date_format:"%M"}} + {{$plage->freq|date_format:"%M"}});
      date.setSeconds({{$plage->fin|date_format:"%S"}});
      form.fin.value = printf('%02d:%02d:%02d', date.getHours(), date.getMinutes(), date.getSeconds());
      return onSubmitFormAjax(form, function() {RDVmultiples.refreshSlot(slot_id, plage_id, consult_id, "{{$multiple}}"); });
    };

    Main.add(function() {
      //multiple edit init
      {{if $consultation->_id}}
        var dom = $("Places_{{$plage->_id}}");
        var consult_target = dom.up().up().down("input[name='consult_id']");
        $V(consult_target,'{{$consultation->_id}}');
      {{/if}}
      });
  </script>

  <form action="?m=dPcabinet" method="post" name="editPlage-{{$plage->_id}}-{{$slot_id}}" onsubmit="return checkForm(this);">
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
        {{if $consultation->_id}}<img src="http://localhost/mediboard/style/mediboard/images/buttons/edit.png" alt="" />{{/if}}
        {{if !$multiple}}
          Plage du {{$plage->date|date_format:$conf.longdate}}
          de {{$plage->debut|date_format:$conf.time}}
          à {{$plage->fin|date_format:$conf.time}}
        {{else}}
          {{$plage->date|date_format:"%a %d %b"}}
        {{/if}}
      </th>
    </tr>
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
      {{if $display_nb_consult}}<td colspan="3"></td>{{/if}}
    </tr>
  {{/foreach}}
  {{if $online && !$plage->locked}}
  <tr>
    <td class="button" colspan="{{if $display_nb_consult}}4{{else}}3{{/if}}">
      <button type="button" class="up singleclick" onclick="addPlaceBefore('{{$plage->_id}}', '{{$slot_id}}' ,'{{$consultation->_id}}')" {{if !$plage->_canEdit}}disabled="disabled"{{/if}}>
        Ajouter Avant
      </button>
    </td>
  </tr>
  {{/if}}
  {{foreach from=$listPlace item=_place}}
    {{assign var=count_places value=$_place.consultations|@count}}
    <tr {{if ($_place.time == $consultation->heure)}}class="selected"{{/if}}>
      <td>
        {{if $count_places> 1}}
          <img src="style/mediboard/images/icons/small-warning.png" alt="SURB" title="surbooking : {{$count_places}} patients" style="float:right;"/>
        {{/if}}
        <div style="float:left">
          <label>
            {{if $online && !$plage->locked && ($conf.dPcabinet.CConsultation.surbooking_readonly || $plage->_canEdit || $count_places == 0)}}
              {{if !$multiple}}
                <button type="button" class="tick validPlage"
              {{else}}
                <input type="radio" class="validPlage" name="checkbox-{{$plage->_id}}-{{$slot_id}}" {{if $_place.time == $consultation->heure}}checked="checked"{{/if}}
              {{/if}}
                data-consult_id="{{$consultation->_id}}"
                data-chir_name="{{$plage->_ref_chir}}"
                data-plageid="{{$plage->_id}}"
                data-date="{{$plage->date}}"
                data-chir_id="{{$plage->chir_id}}"
                data-time="{{$_place.time}}"
                data-slot_d="{{$_place.time}}"
              {{if !$multiple}}
                  >
              {{else}}
                  />
              {{/if}}
              {{$_place.time|date_format:$conf.time}}
              {{if !$multiple}}
                </button>
              {{/if}}
            {{else}} <!-- not online or locked or surbooking -->
              {{$_place.time|date_format:$conf.time}}
            {{/if}}
          </label>
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
            {{$_consultation->patient_id|ternary:$_consultation->_ref_patient:"[PAUSE]"}} {{if $_consultation->annule}}<span style="color:red;">(ANNULE)</span>{{/if}}
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
  {{if $online && !$plage->locked}}
    <tr>
      <td class="button" colspan="{{if $display_nb_consult}}5{{else}}3{{/if}}">
        <button type="button" class="down singleclick" onclick="addPlaceAfter('{{$plage->_id}}', '{{$slot_id}}' ,'{{$consultation->_id}}')" {{if !$plage->_canEdit}}disabled="disabled"{{/if}}>
          Ajouter Après
        </button>
      </td>
    </tr>
  {{/if}}
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