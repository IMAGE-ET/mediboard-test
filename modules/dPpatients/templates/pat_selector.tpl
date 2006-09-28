<!-- $Id$ -->
<script type="text/javascript">
function createEditPat(id_sel){
  var form = document.frmSelector;
  var url = new Url();
  url.setModuleAction("dPpatients", "vw_edit_patients");
  url.addParam("patient_id", id_sel);
  url.addParam("dialog", "1");
  url.addElement(form.name);
  url.addElement(form.firstName);
  url.redirect();
}

function setClose(key, val){
  window.opener.setPat(key,val);
  window.close();
}
</script>

<form action="index.php" name="frmSelector" method="get">

<input type="hidden" name="m" value="dPpatients" />
<input type="hidden" name="a" value="pat_selector" />
<input type="hidden" name="dialog" value="1" />

<table class="form">

<tr>
  <th class="category" colspan="3">Critères de tri</th>
</tr>

<tr>
  <th><label for="name" title="Nom du patient à rechercher, au moins les premières lettres">Nom</label></th>
  <td><input name="name" value="{{$name|stripslashes}}" size="30" /></td>
  <td></td>
</tr>
<tr>
  <th><label for="firstName" title="Prénom du patient à rechercher, au moins les premières lettres">Prénom</label></th>
  <td><input name="firstName" value="{{$firstName|stripslashes}}" size="30" /></td>
  <td><button class="search" type="submit">Rechercher</button></td>
</tr>

<tr>
  <th class="category" colspan="3">Choisissez un patient dans la liste</th>
</tr>

</table>

<table class="tbl">
<tr>
  <th align="center">Patient</th>
  <th align="center">Date de naissance</th>
  <th align="center">Téléphone</th>
  <th align="center">Mobile</th>
  <th align="center">Sélectionner</th>
</tr>
{{foreach from=$list item=curr_patient}}
<tr>
  <td><a href="javascript:createEditPat({{$curr_patient->patient_id}})" class="buttonedit notext" style="float:left;"></a>{{$curr_patient->_view}}</td>
  <td>{{$curr_patient->_naissance}}</td>
  <td>{{$curr_patient->tel}}</td>
  <td>{{$curr_patient->tel2}}</td>
  <td class="button">
    <button class="tick" type="button" onclick="setClose({{$curr_patient->patient_id}}, '{{$curr_patient->_view|smarty:nodefaults|JSAttribute}}')">Sélectionner</button>
  </td>
</tr>
{{/foreach}}
</table>

<table class="form">

<tr>
  <td class="button" colspan="2">
    <button class="submit" type="button" onclick="createEditPat(0)">Créer un patient</button>
    <button class="cancel" type="button" onclick="window.close()">Annuler</button>
  </td>
</tr>

</table>

</form>
