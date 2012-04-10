<style>
#comp-list button {
  margin: -1px;
}

#comp-list > tr .operand-b {
  visibility: hidden;
}

#comp-list > tr[data-comp='between'] .operand-b {
  visibility: visible;
}
</style>

<script type="text/javascript">
ValueFilter = {
  newRow: function() {
    var row = $("comp-list").down(".inactive");

    if (row) {
      row.show().removeClassName("inactive");
    }
  }
};
</script>

<table class="main tbl">
  <tr>
    <th class="category">
      {{$concept}}
    </th>
  </tr>
  <tr>
    <td>
      {{$spec}}
    </td>
  </tr>

  <tbody id="comp-list">
    {{foreach from=0|range:20 item=i}}
    {{unique_id var=uid}}
    <tr {{if $i > 0}} style="display: none;" class="inactive" {{/if}} data-comp="eq" id="{{$uid}}">
      <td>
        <button type="button" class="trash notext" onclick="this.up('tr').remove()">{{tr}}Delete{{/tr}}</button>

        <select name="cv[{{$concept->_id}}][{{$i}}][comp]" style="width: 5em;" onchange="this.up('tr').writeAttribute('data-comp', $V(this))">
          <option value="eq">=</option>
          {{if !$spec instanceof CBoolSpec}}
            <option value="lte">&lt;=</option>
            <option value="gte">&gt;=</option>
            <option value="contains">Contient</option>
            <option value="begins">Commence par</option>
            <option value="ends">Finit par</option> 
            <option value="between">Entre X et Y</option>
          {{/if}}
        </select>

        {{if $spec instanceof CBoolSpec}}
          <label><input type="radio" name="cv[{{$concept->_id}}][{{$i}}][value]" value="1" checked /> {{tr}}Yes{{/tr}}</label>
          <label><input type="radio" name="cv[{{$concept->_id}}][{{$i}}][value]" value="0" /> {{tr}}No{{/tr}}</label>
        {{elseif $spec instanceof CNumSpec ||
                 $spec instanceof CFloatSpec}}
          <script>
            Main.add(function(){
              $("{{$uid}}").select("input").invoke("addSpinner");
            });
          </script>
          <span class="operand-a">
            <input type="text" name="cv[{{$concept->_id}}][{{$i}}][value]"   size="4" class="{{$spec}}" />
          </span>
          <span class="operand-b">
            <input type="text" name="cv[{{$concept->_id}}][{{$i}}][value-b]" size="4" class="{{$spec}}" />
          </span>
        {{elseif $spec instanceof CDateSpec ||
                 $spec instanceof CDateTimeSpec ||
                 $spec instanceof CTimeSpec}}
          <script>
            Main.add(function(){
              $("{{$uid}}").select("input").each(Calendar.regField);
            });
          </script>
          <span class="operand-a">
            <input type="hidden" name="cv[{{$concept->_id}}][{{$i}}][value]"   class="{{$spec}}" />
          </span>
          <span class="operand-b">
            <input type="hidden" name="cv[{{$concept->_id}}][{{$i}}][value-b]" class="{{$spec}}" />
          </span>
        {{elseif $spec instanceof CEnumSpec}}
          <fieldset>
            {{foreach from=$spec->_locales item=_locale key=_key}}
              <label><input type="radio" name="cv[{{$concept->_id}}][{{$i}}][value]"   class="{{$spec}}" value="{{$_key}}" /> {{$_locale}}</label><br />
            {{/foreach}}
          </fieldset>
        {{else}}
          <input type="text" name="cv[{{$concept->_id}}][{{$i}}][value]" />
        {{/if}}
      </td>
    </tr>
    {{/foreach}}
  </tbody>

  {{if !$spec instanceof CBoolSpec}}
    <tr>
      <td>
        <button type="button" class="add notext" onclick="ValueFilter.newRow()" style="margin: -1px;"></button>
      </td>
    </tr>
  {{/if}}
</table>
