<script>
  showLegend = function() {
    new Url("bloc", "legende").requestModal();
  }
  savePref = function(form) {
    var formPref = getForm('editPrefSalles');
    var formsalle = getForm('selectSalle');
    var salle_id = $V(form.default_salle_id);

    var default_salle_id_elt = formPref.elements['pref[default_salles_id]'];
    var default_salle_id = $V(default_salle_id_elt).evalJSON();
    default_salle_id.g{{$group_id}} = salle_id;
    $V(default_salle_id_elt, Object.toJSON(default_salle_id));
    return onSubmitFormAjax(formPref, function() {
      Control.Modal.close();
      $V(formsalle.salle, salle_id);
    });
  }
  Main.add(function () {
    Calendar.regField(getForm("selectSalle").date, null, {noView: true});
  });

  EditCheckList = {
    url: null,
    edit: function(salle_id, date, type) {
      var url = new Url('salleOp', 'ajax_edit_checklist');
      url.addParam('date'    , date);
      url.addParam('salle_id', salle_id);
      url.addParam('bloc_id', 0);
      url.addParam('type', type);
      url.requestModal();
      url.modalObject.observe("afterClose", function(){
        location.reload();
      });
    }
  };
</script>

<form action="?" name="selectSalle" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="operation_id" value="0" />
  <table class="form">
    <tr>
      <th class="title" colspan="2">
        {{$date|date_format:$conf.longdate}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </th>
    </tr>
    <tr>
      <th>
        <label for="salle" title="Salle d'opération">
          <button type="button" class="search notext" title="Salle par défaut" onclick="Modal.open('select_default_salle', { showClose: true, title: 'Salle par défaut' })"></button>
          Salle
        </label><br />
        <button type="button" onclick="showLegend()" class="search" style="float: left;">Légende</button>
      </th>
      <td>
        <select name="salle" onchange="this.form.submit()">
          <option value="">&mdash; {{tr}}CSalle.none{{/tr}}</option>
          {{foreach from=$listBlocs item=curr_bloc}}
          <optgroup label="{{$curr_bloc->nom}}">
            {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
            <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $salle->_id}}selected{{/if}}>
              {{$curr_salle->nom}}
            </option>
            {{foreachelse}}
            <option value="" disabled>{{tr}}CSalle.none{{/tr}}</option>
            {{/foreach}}
          </optgroup>
          {{/foreach}}
        </select><br />
        <input type="hidden" name="hide_finished" value="{{$hide_finished}}" onchange="this.form.submit()" />
        <label>
          <input type="checkbox" name="_hide_finished" {{if $hide_finished}}checked{{/if}} onclick="$V(this.form.hide_finished, this.checked ? 1 : 0)" />
          Cacher les interv. terminées 
        </label>

        <div id="select_default_salle" style="display: none;">
          <table class="form">
            <tr>
              <td style="text-align: center;">
                <select name="default_salle_id">
                  <option value="">&mdash; {{tr}}Choose{{/tr}}</option>

                  {{foreach from=$listBlocs item=curr_bloc}}
                    <optgroup label="{{$curr_bloc->nom}}">
                      {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
                        <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $default_salle_id}}selected="selected"{{/if}}>
                          {{$curr_salle->nom}}
                        </option>
                      {{/foreach}}
                    </optgroup>
                  {{/foreach}}
                </select>
              </td>
            </tr>
            <tr>
              <td class="button">
                <button type="button" class="submit" onclick="savePref(this.form);">{{tr}}Save{{/tr}}</button>
              </td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
    {{if $salle->cheklist_man}}
      <tr>
        <td colspan="2" class="button">
          {{if $date_last_checklist|date_format:$conf.date != $date|date_format:$conf.date}}
            <button class="checklist" type="button" onclick="loadOperation(null, null, 1)">Validation de checklist</button>
          {{/if}}
          <div class="info">Dernière validation: {{$date_last_checklist|date_format:$conf.datetime}}</div>
        </td>
      </tr>
    {{/if}}

    {{if $require_check_list_close}}
      <tr>
        <td colspan="2" class="button">
          <button class="checklist" type="button" onclick="EditCheckList.edit('{{$salle->_id}}', '{{$date}}', 'fermeture_salle');">Fermeture de salle</button>
          {{if $date_close_checklist}}
            <div class="info">Dernière validation: {{$date_close_checklist|date_format:$conf.datetime}}</div>
          {{/if}}
        </td>
      </tr>
    {{/if}}
  </table>
</form>

<form name="editPrefSalles" method="post">
  <input type="hidden" name="m" value="admin" />
  <input type="hidden" name="dosql" value="do_preference_aed" />
  <input type="hidden" name="user_id" value="{{$app->user_id}}" />
  <input type="hidden" name="pref[default_salles_id]" value="{{$app->user_prefs.default_salles_id}}" />
</form>

{{mb_include module="salleOp" template="inc_details_plages"}}