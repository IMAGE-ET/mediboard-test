{{mb_default var=mode_class value=CModeEntreeSejour}}

<script type="text/javascript">
editModeEntreeSortie = function(mode_class, mode_id) {
  new Url("planningOp", "edit_mode_entree_sortie_sejour")
    .addParam("mode_class", mode_class)
    .addParam("mode_id",    mode_id)
    .requestModal(400, 400);
  return false;
};
importModeEntreeSortie = function(mode_class) {
  new Url("planningOp", "import_mode_entree_sortie_sejour")
    .addParam("mode_class", mode_class)
    .popup(800, 600);
  return false;
}
</script>

<button class="new" type="button" onclick="editModeEntreeSortie('{{$mode_class}}', 0)">{{tr}}{{$mode_class}}-title-create{{/tr}}</button>
<button class="hslip" type="button" onclick="importModeEntreeSortie('{{$mode_class}}')">{{tr}}{{$mode_class}}-import{{/tr}}</button>
<table class="main tbl">
  <tr>
    <th>{{mb_title class=$mode_class field=code}}</th>
    <th>{{mb_title class=$mode_class field=libelle}}</th>
    <th>{{mb_title class=$mode_class field=mode}}</th>
    <th>{{mb_title class=$mode_class field=actif}}</th>
  </tr>

  {{foreach from=$list_modes item=_mode}}
    <tr>
      <td>
        <button type="button" class="edit notext" onclick="return editModeEntreeSortie('{{$mode_class}}', {{$_mode->_id}})">
          {{tr}}Edit{{/tr}}
        </button>
        {{mb_value object=$_mode field=code}}
      </td>
      <td>{{mb_value object=$_mode field=libelle}}</td>
      <td>{{mb_value object=$_mode field=mode}}</td>
      <td>
        <form name="editActif{{$_mode->_guid}}"  method="post" onsubmit="return onSubmitFormAjax(this)">
          {{mb_key object=$_mode}}
          {{mb_class object=$_mode}}
          {{mb_field object=$_mode field="actif" onchange=this.form.onsubmit()}}
        </form>
      </td>
    </tr>
  {{/foreach}}
</table>
