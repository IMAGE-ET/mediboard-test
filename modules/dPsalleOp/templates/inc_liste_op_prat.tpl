<script type="text/javascript">
Main.add(function () {
  Calendar.regField(getForm("selection").date, null, {noView: true});
});
</script>

<form action="?" name="selection" method="get">

<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="op" value="0" />

<table class="form">
  <tr>
    <th class="category" colspan="2">
      {{$date|date_format:$dPconfig.longdate}}
      <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
    </th>
  </tr>
  
  <tr>
    <th><label for="praticien_id" title="Praticien">Praticien</label></th>
    <td>
      <select name="praticien_id" onchange="this.form.submit()" style="width: 180px;">
        <option value="">&mdash; Aucun praticien</option>
        {{foreach from=$listPrats key=prat_id item=prat_view}}
        <option value="{{$prat_id}}" {{if $prat_id == $praticien->_id}} selected="selected" {{/if}}>
          {{$prat_view}}
        </option>
        {{/foreach}}
      </select><br />
      <input type="hidden" name="hide_finished" value="{{$hide_finished}}" onchange="this.form.submit()" />
      <label>
        <input type="checkbox" name="_hide_finished" {{if $hide_finished}}checked="checked"{{/if}} onclick="$V(this.form.hide_finished, this.checked ? 1 : 0)" />
        Cacher les opérations terminées 
      </label>
    </td>
  </tr>
</table>

</form>
      
{{include file="inc_details_op_prat.tpl"}}