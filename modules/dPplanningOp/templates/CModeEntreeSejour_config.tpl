{{mb_default var=mode_class value=CModeEntreeSejour}}

<script type="text/javascript">
editModeEntreeSortie = function(mode_class, mode_id) {
  var url = new Url("planningOp", "edit_mode_entree_sortie_sejour");
  url.addParam("mode_class", mode_class);
  url.addParam("mode_id",    mode_id);
  url.requestModal(400, 400);
  return false;
}
</script>

<button class="new" type="button" onclick="editModeEntreeSortie('{{$mode_class}}', 0)">{{tr}}{{$mode_class}}-title-create{{/tr}}</button>

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
        <a href="#1" onclick="return editModeEntreeSortie('{{$mode_class}}', {{$_mode->_id}})">{{mb_value object=$_mode field=code}}</a>
      </td>
      <td>
        <a href="#1" onclick="return editModeEntreeSortie('{{$mode_class}}', {{$_mode->_id}})">{{mb_value object=$_mode field=libelle}}</a>
      </td>
      <td>{{mb_value object=$_mode field=mode}}</td>
      <td>{{mb_value object=$_mode field=actif}}</td>
    </tr>
  {{/foreach}}
</table>
