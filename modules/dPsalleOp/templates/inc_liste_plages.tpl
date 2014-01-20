<script>
Main.add(function () {
  Calendar.regField(getForm("selectSalle").date, null, {noView: true});
});
showLegend = function() {
  new Url("bloc", "legende").requestModal();
}
</script>

<form action="?" name="selectSalle" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="op" value="0" />
  <table class="form">
    <tr>
      <th class="title" colspan="2">
        {{$date|date_format:$conf.longdate}}
        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
      </th>
    </tr>
    <tr>
      <th>
        <label for="salle" title="Salle d'opération">Salle</label><br />
        <button type="button" onclick="showLegend()" class="search" style="float: left;">Légende</button>
      </th>
      <td>
        <select name="salle" onchange="this.form.submit()">
          <option value="">&mdash; {{tr}}CSalle.none{{/tr}}</option>
          {{foreach from=$listBlocs item=curr_bloc}}
          <optgroup label="{{$curr_bloc->nom}}">
            {{foreach from=$curr_bloc->_ref_salles item=curr_salle}}
            <option value="{{$curr_salle->_id}}" {{if $curr_salle->_id == $salle->_id}}selected="selected"{{/if}}>
              {{$curr_salle->nom}}
            </option>
            {{foreachelse}}
            <option value="" disabled="disabled">{{tr}}CSalle.none{{/tr}}</option>
            {{/foreach}}
          </optgroup>
          {{/foreach}}
        </select><br />
        <input type="hidden" name="hide_finished" value="{{$hide_finished}}" onchange="this.form.submit()" />
        <label>
          <input type="checkbox" name="_hide_finished" {{if $hide_finished}}checked="checked"{{/if}} onclick="$V(this.form.hide_finished, this.checked ? 1 : 0)" />
          Cacher les interv. terminées 
        </label>
      </td>
    </tr>
    {{if $salle->cheklist_man}}
      <tr>
        <td colspan="2" class="button">
          <input type="hidden" name="load_checklist" value="0"/>
          {{if $date_last_checklist|date_format:$conf.date != $date|date_format:$conf.date}}
            <button class="add" type="button" onclick="$V(this.form.load_checklist, 1); this.form.submit();">Validation de checklist</button>
          {{/if}}
          <div class="info">Dernière validation: {{$date_last_checklist|date_format:$conf.datetime}}</div>
        </td>
      </tr>
    {{/if}}
  </table>
</form>

{{include file="inc_details_plages.tpl"}}