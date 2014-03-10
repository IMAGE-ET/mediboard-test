<table class="tbl">
  <tr>
    <th colspan="2">
      Association des documents aux modèles respectifs
    </th>
  </tr>
  <tr>
    <td style="vertical-align: top;">
      <select name="modele_id">
        {{foreach from=$modeles item=_modele}}
          <option value="{{$_modele->_id}}">{{$_modele->nom}}</option>
        {{/foreach}}
      </select>
      <button type="button" name="button_restore" class="tick notext" onclick="restoreModeleId($V(this.form.modele_id), this.form.do_it.checked ? 1 : 0, this.form.auto.checked)"></button>
      <label>
        <input type="checkbox" name="auto"/> Auto
      </label>
      <label>
        <input type="checkbox" name="do_it"/> Réel
      </label>
    </td>
    <td id="result_restore" class="text"></td>
  </tr>
</table>