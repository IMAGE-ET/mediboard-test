<form name="addFrm" action="?m={{$m}}" method="post"
      onsubmit="return onSubmitFormAjax(this,
          {onComplete: function() {
              updateAddEditPack({{$pack->_id}}, '{{$filter_class}}');
              reloadList({{$pack->_id}}, '{{$user_id}}');}})">
  {{mb_key object=$pack}}
  
  <input type="hidden" name="m" value="dPcompteRendu" />
  <input type="hidden" name="dosql" value="do_modele_to_pack_aed" />
  <input type="hidden" name="del" value="0" />
  
  <label for="modele_id" title="{{tr}}CCompteRendu.select{{/tr}}" />
  
  <select name="modele_id" class="notNull ref" onchange="this.form.onsubmit()" style="width: 20em;">
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