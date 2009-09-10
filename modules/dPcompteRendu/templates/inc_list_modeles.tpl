<form name="addFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_pack_aed" />
  {{mb_field object=$pack field="pack_id" hidden=1 prop=""}}
  <input type="hidden" name="del" value="0" />
  <input type="hidden" name="modeles" value="{{$pack->modeles|smarty:nodefaults|JSAttribute}}" />
  <label for="_new" title="Veuillez choisir un modèle" />
  <select name="_new" class="notNull ref" onchange="this.form.submit()">
    <option value="">&mdash; Choisir un modèle</option>
    <optgroup label="Modèles de l'utilisateur">
      {{foreach from=$modeles.prat item=_modele}}
      <option value="{{$_modele->_id}}">{{$_modele->nom}}</option>
      {{/foreach}}
    </optgroup>
    <optgroup label="Modèles de la fonction">
      {{foreach from=$modeles.func item=_modele}}
      <option value="{{$_modele->_id}}">{{$_modele->nom}}</option>
      {{/foreach}}
    </optgroup>
  </select>
</form>