<form name="Choice_lit" action="" method="post" onsubmit="return ChoiceLit.finish(this.lit_id.value, 1);">
  <input type="hidden" name="chambre_id" value="{{$chambre->_id}}"/>
  <table class = "main">
    <tr>
      <th class="title" colspan="2">Affecation de {{$patient->_view}}</th>
    </tr>
    <tr>
      <td>{{tr}}CChambre{{/tr}}</td>
      <td>{{mb_value object=$chambre field=nom}}</td></td>
    </tr>
    <tr>
      <td>{{mb_title class=CLit field=nom}}</td>
      <td>
        <select name="lit_id">
          <option value="-1">&mdash; Choisir un lit</option>
          {{foreach from=$chambre->_ref_lits item=_lit}}
          {{assign var=lit_id value=$_lit->_id}}
          <option value="{{$lit_id}}" {{if isset($affectations.$lit_id|smarty:nodefaults) && $affectations.$lit_id}}disabled{{/if}}>
            {{$_lit->nom}}
          </option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <td class="button" colspan="2">
        <button type="button" class="submit" onclick="return ChoiceLit.finish(this.form.lit_id.value, 1);">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>