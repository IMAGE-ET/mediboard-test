<form action="?" name="selectBloc" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="vw_reveil" />
  <span id="heure">{{$hour|date_format:$conf.time}}</span> - {{$date|date_format:$conf.longdate}}
  <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
  <select name="bloc_id" onchange="this.form.submit();">
    <option value="" disabled="disabled">&mdash; {{tr}}CBlocOperatoire.select{{/tr}}</option>
    {{foreach from=$blocs_list item=_bloc}}
    <option value="{{$_bloc->_id}}" {{if $_bloc->_id == $bloc->_id}}selected="selected"{{/if}}>
      {{$_bloc}}
    </option>
    {{foreachelse}}
    <option value="" disabled="disabled">{{tr}}CBlocOperatoire.none{{/tr}}</option>
    {{/foreach}}
  </select>
</form>

<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("selectBloc").date, null, {noView: true});
});
</script>
 