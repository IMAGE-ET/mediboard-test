<script>
  updateFreq = function(elt) {
    var _val = $V(elt);

    var form = getForm('editFrm');
    var selector = $(form._pause_repeat_time);
    selector.select("option").each(function(elt) {
      var val = $(elt).readAttribute('value');
      var _str = "";
      var minutes_total = val*_val;
      var _hour = Math.floor(minutes_total/60);
      var _minutes = minutes_total % 60;
      if (_hour > 0) {
        _str = _str+_hour+"h";
      }
      if (_minutes > 0) {
        _str = _str+" "+_minutes+" min";
      }

      elt.update(_str);
    });


    var hour = '{{tr}}hour{{/tr}}';
  };

  modifEtatDesistement = function(valeur){
    if(valeur != 0){
      $('remplacant_plage').setVisible(valeur);
      $$('.remplacement_plage').invoke('setVisible', valeur);
      $$('.retrocession').invoke('setVisible', valeur);
    }
    else{
      var form = getForm('editFrm');
      form.remplacant_id.value = '';
      if(form.pour_compte_id.value == ""){
        $('remplacant_plage').hide();
        $$('.remplacement_plage').invoke('hide');
        $$('.retrocession').invoke('hide');
      }
      else{
        $$('.remplacement_plage').invoke('hide');
      }
    }
  };

  extendPlage = function (plage_id, repetition_type, nb_repeat) {
    if (confirm("Prolonger la plage sur "+nb_repeat+" semaines de type "+ repetition_type)) {
      var _update_pause = $(getForm('editFrm')._update_pause);
      var url = new Url("cabinet", "controllers/do_extend_plage");
      url.addParam("plage_id", plage_id);
      url.addParam("_type_repeat", repetition_type);
      url.addParam("_repeat", nb_repeat);
      url.addParam("_update_pause", _update_pause.checked ? 1 : 0);
      url.addParam("_pause", $V(getForm('editFrm')._pause));
      url.addParam("_pause_repeat_time", $V(getForm('editFrm')._pause_repeat_time));
      url.requestUpdate("systemMsg");
    }

  };
  
  modifPourCompte = function(valeur){
    if(valeur != 0){
      $('remplacant_plage').setVisible(valeur);
      $$('.retrocession').invoke('setVisible', valeur);
    }
    else{
      var form = getForm('editFrm');
      if(form.desistee.value == 0){
        $('remplacant_plage').hide();
      }
    }
  };
  
  Main.add(function(){
    var form = getForm('editFrm');
    
    {{if !$can->admin && $plageSel->_id && !$plageSel->_canEdit}}
      makeReadOnly(form);
    {{/if}}


    updateFreq(form._freq);

    Calendar.regField(form.debut);
    Calendar.regField(form.fin  );

    Calendar.regField(form._pause);

    form._repeat.addSpinner({min: 0});
  });
</script>

<form name='editFrm' action='?m=dPcabinet' method='post' onsubmit="this._type_repeat.disabled = ''; return PlageConsultation.checkForm(this, {{$modal}});">
  <input type='hidden' name='m' value='dPcabinet' />
  <input type='hidden' name='dosql' value='do_plageconsult_multi_aed' />
  <input type='hidden' name='del' value='0' />
  <input type='hidden' name='modal' value='{{$modal}}'" />
  {{mb_key object=$plageSel}}

  <table class="form">
    {{if @$modules.3333tel->mod_active}}
      <tr>
        <td>
          {{mb_include module=3333tel template=inc_check_3333tel object=$plageSel}}
        </td>
      </tr>
    {{/if}}
    {{mb_include module=system template=inc_form_table_header object=$plageSel colspan=1}}
    <tr>
      <td>
        <fieldset>
          <legend>Informations sur la plage</legend>
          <table class="form">
            <tr>
              <th>{{mb_label object=$plageSel field="chir_id"}}</th>
              <td>
                <select name="chir_id" class="{{$plageSel->_props.chir_id}}" style="width: 15em;">
                  <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                  {{mb_include module=mediusers template=inc_options_mediuser list=$listChirs selected=$chirSel}}
                </select>
              </td>
              <th>{{mb_label object=$plageSel field="libelle"}}</th>
              <td>{{mb_field object=$plageSel field="libelle" style="width: 15em;"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$plageSel field="date"}}</th>
              <td>
                <select name="date" class="{{$plageSel->_props.date}}" style="width: 15em;">
                  <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                  {{foreach from=$listDaysSelect item=curr_day}}
                    <option value="{{$curr_day}}"
                      {{if ($curr_day == $plageSel->date) || (!$plageSel->_id && $curr_day == $debut)}} selected="selected" {{/if}}
                      {{if array_key_exists($curr_day, $holidays) && !$app->user_prefs.allow_plage_holiday}}disabled="disabled"{{/if}}
                      >
                      {{$curr_day|date_format:"%A"}} {{if array_key_exists($curr_day, $holidays)}}(férié){{/if}}
                    </option>
                  {{/foreach}}
                </select>
              </td>
              <th>{{mb_label object=$plageSel field="color"}}</th>
              <td>
                {{mb_field object=$plageSel field="color" form=editFrm}}
              </td>
            </tr>
            <tr>
              <th>{{mb_label object=$plageSel field="debut"}}</th>
              <td>{{mb_field object=$plageSel field="debut"}}</td>
              <th>* {{mb_label object=$plageSel field="locked"}}</th>
              <td>{{mb_field object=$plageSel field="locked" typeEnum="checkbox"}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$plageSel field="fin"}}</th>
              <td>{{mb_field object=$plageSel field="fin"}}</td>
              <th>* {{mb_label object=$plageSel field="pour_tiers"}}</th>
              <td>{{mb_field object=$plageSel field="pour_tiers" typeEnum=checkbox}}</td>
            </tr>
            <tr>
              <th>{{mb_label object=$plageSel field="_freq"}}</th>
              <td>
                <select name="_freq" onchange="updateFreq(this);">
                  <option value="05" {{if ($plageSel->_freq == "05")}} selected="selected" {{/if}}>05</option>
                  <option value="10" {{if ($plageSel->_freq == "10")}} selected="selected" {{/if}}>10</option>
                  <option value="15" {{if ($plageSel->_freq == "15") || (!$plageSel->_id)}} selected="selected" {{/if}}>15</option>
                  <option value="20" {{if ($plageSel->_freq == "20")}} selected="selected" {{/if}}>20</option>
                  <option value="30" {{if ($plageSel->_freq == "30")}} selected="selected" {{/if}}>30</option>
                  <option value="45" {{if ($plageSel->_freq == "45")}} selected="selected" {{/if}}>45</option>
                  <option value="60" {{if ($plageSel->_freq == "60")}} selected="selected" {{/if}}>60</option>
                </select> min
              </td>
              <th>{{mb_label object=$plageSel field="_skip_collisions"}}</th>
              <td>{{mb_field object=$plageSel field="_skip_collisions" typeEnum=checkbox}}</td>
            </tr>
            <tr>
              <td class="text button" colspan="4">
                {{if $plageSel->_affected}}
                  Déjà <strong>{{$plageSel->_affected}} consultations</strong> planifiées
                                                                               de <strong>{{$_firstconsult_time}}</strong> à <strong>{{$_lastconsult_time}}</strong>
                {{/if}}
                <input type='hidden' name='nbaffected' value='{{$plageSel->_affected}}' />
                <input type='hidden' name='_firstconsult_time' value='{{$_firstconsult_time}}' />
                <input type='hidden' name='_lastconsult_time' value='{{$_lastconsult_time}}' />
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend>
            <label><input type="checkbox" name="_update_pause" value="1" {{if $plageSel->_pause_id}}checked="checked"{{/if}} onchange="$(this).up(1).next(0).toggle();" /> avec une pause</label>
          </legend>
          <table class="form" {{if !$plageSel->_pause_id}}style="display: none"{{/if}}>
            <tr>
              <th>{{mb_label object=$plageSel field="_pause"}}</th>
              <td>{{mb_field object=$plageSel field="_pause"}}</td>
              <th>{{mb_label object=$plageSel field="_pause_repeat_time"}}</th>
              <td>
                <select name="_pause_repeat_time">
                {{foreach from=1|range:10 item=i}}
                  <option {{if $plageSel->_pause_repeat_time == $i}}selected="selected"{{/if}} value="{{$i}}">{{$i}}x</option>
                {{/foreach}}
                </select>
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend>Répétition</legend>
          <table class="form">
            <tr>
              <th><label for="_repeat" title="Nombre de semaines de répétition">Nombre de semaines</label></th>
              <td>
                <input type="text" size="2" name="_repeat" value="1"
                       onchange="this.form._type_repeat.disabled = this.value <= 1 ? 'disabled' : '';"
                       onKeyUp="this.form._type_repeat.disabled = this.value <= 1 ? 'disabled' : '';" />
                (max. 100)
              </td>
              <td rowspan="3" class="text">
                <div class="small-info">
                  Pour modifier plusieurs plages (nombre de semaines > 1),
                  veuillez <strong>ne pas changer les champs début et fin en même temps</strong>.
                  <br />
                  * Cette valeur ne sera pas propagée sur les plages suivantes.
                  <br/>
                  Si vous souhaitez propager les valeurs précédées par * sur plusieurs plages, veuillez cocher cette case:
                  {{mb_field object=$plageSel field="_propagation" typeEnum="checkbox"}}
                </div>
              </td>
            </tr>
            <tr>
              <th>{{mb_label object=$plageSel field="_type_repeat"}}</th>
              <td>{{mb_field object=$plageSel field="_type_repeat" style="width: 15em;" typeEnum="select" disabled="disabled"}}</td>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
    <tr>
      <td>
        <fieldset>
          <legend>Remplacements</legend>
          <table class="form">
            <tr>
              <th>{{mb_label object=$plageSel field="desistee"}}</th>
              <td>{{mb_field object=$plageSel field="desistee"  typeEnum="checkbox" onchange="modifEtatDesistement(this.value);" }}</td>
              <th>{{mb_label object=$plageSel field="pour_compte_id"}}</th>
              <td>
                <select name="pour_compte_id" style="width: 15em;"  onchange="modifPourCompte(this.value);">
                  <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                  {{mb_include module=mediusers template=inc_options_mediuser list=$listChirs selected=$plageSel->pour_compte_id disabled=$chirSel}}
                </select>
              </td>
            </tr>
            <tr id="remplacant_plage" {{if !$plageSel->desistee && !$plageSel->pour_compte_id}} style="display:none"{{/if}}>
              <th>
                <span class="remplacement_plage" {{if !$plageSel->desistee}}style="display:none;"{{/if}}>
                {{mb_label object=$plageSel field="remplacant_id"}}
                </span>
              </th>
              <td>
                <select name="remplacant_id" style="width: 15em;{{if !$plageSel->desistee}}display:none;{{/if}}" class="remplacement_plage">
                  <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
                  {{mb_include module=mediusers template=inc_options_mediuser list=$listChirs selected=$plageSel->remplacant_id }}
                </select>
              </td>
              <th>
                <span class="retrocession">
                  {{mb_label object=$plageSel field="pct_retrocession"}}
                </span>
              </th>
              <td>
                <span class="retrocession">
                  {{mb_field object=$plageSel field="pct_retrocession" size="2" increment=true form=editFrm  class="retrocession"}}
                </span>
              </td>
            </tr>
          </table>
        </fieldset>
      </td>
    </tr>
  </table>

  <table class="form">
    <tr>
      {{if !$plageSel->_id}}
        <td class="button" colspan="4">
          <button id="edit_plage_consult_button_create_new_plage" class="submit">{{tr}}Create{{/tr}}</button>
        </td>
      {{else}}
      <td class="button" colspan="4">
        <button type="submit" class="modify" id="edit_plage_consult_button_modify_plage">{{tr}}Modify{{/tr}}</button>
        <button class="trash" type='button'  id="edit_plage_consult_button_delete_plage"
          onclick="
              confirmDeletion(this.form, {
            typeName:'la plage de consultations du',
            objName:'{{$plageSel->date|date_format:$conf.longdate}}',
            {{if $modal}}
              ajax: 1
            {{else}}
              callback: function() {
                var form = getForm('editFrm');
                form._type_repeat.disabled = '';
                form.submit();
              }
            {{/if}}
              }
          {{if $modal}},
              {onComplete: Control.Modal.close}
            {{/if}})">
          {{tr}}Delete{{/tr}}
        </button>
        <button type="button" class="button add" onclick="extendPlage('{{$plageSel->_id}}', $V(this.form._type_repeat), $V(this.form._repeat) );">
          {{tr}}Extend{{/tr}}
        </button>
      </td>
      {{/if}}
    </tr>
  </table>
</form>