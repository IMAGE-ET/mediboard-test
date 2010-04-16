<form name="addFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  {{mb_key object=$pack}}
  <input type="hidden" name="dosql" value="do_pack_aed" />
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="modeles" value="{{$pack->modeles|smarty:nodefaults|JSAttribute}}" />
  <label for="_new" title="{{tr}}CCompteRendu.select{{/tr}}" />
  <select name="_new" class="notNull ref" onchange="this.form.submit()">
    <option value="">&mdash; {{tr}}CCompteRendu.select{{/tr}}</option>
    <optgroup label="{{tr}}CCompteRendu-owned-by-user{{/tr}}">
      {{foreach from=$modeles.prat item=_modele}}
      <option value="{{$_modele->_id}}">{{$_modele->nom}}</option>
      {{/foreach}}
    </optgroup>
    <optgroup label="{{tr}}CCompteRendu-owned-by-function{{/tr}}">
      {{foreach from=$modeles.func item=_modele}}
      <option value="{{$_modele->_id}}">{{$_modele->nom}}</option>
      {{/foreach}}
    </optgroup>
    <optgroup label="{{tr}}CCompteRendu-owned-by-etablissment{{/tr}}">
      {{foreach from=$modeles.etab item=_modele}}
      <option value="{{$_modele->_id}}">{{$_modele->nom}}</option>
      {{/foreach}}
    </optgroup>
  </select>
</form>