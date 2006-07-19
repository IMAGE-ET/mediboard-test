<!-- $Id$ -->
<script type="text/javascript">
function createPat(){
  var form = document.frmSelector;
  var url = new Url();
  url.setModuleAction("dPpatients", "vw_edit_patients");
  url.addParam("patient_id", "0");
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

<form action="index.php" target="_self" name="frmSelector" method="get">

<input type="hidden" name="m" value="dPpatients" />
<input type="hidden" name="a" value="pat_selector" />
<input type="hidden" name="dialog" value="1" />

<table class="form">

<tr>
  <th class="category" colspan="3">Critères de tri</th>
</tr>

<tr>
  <th>Nom:</th>
  <td><input name="name" value="{{$name}}" size="30" /></td>
  <td></td>
</tr>
<tr>
  <th>Prénom:</th>
  <td><input name="firstName" value="{{$firstName}}" size="30" /></td>
  <td><button class="search" type="submit">rechercher</button></td>
</tr>

<tr>
  <th class="category" colspan="3">Choisissez un patient dans la liste</th>
</tr>

</table>

<table class="tbl">
<tr>
  <th align="center">Patient</th>
  <th align="center">Date de naissance</th>
  <th align="center">Telephone</th>
  <th align="center">Mobile</th>
  <th align="center">Selectionner</th>
</tr>
{{foreach from=$list item=curr_patient}}
<tr>
  <td>{{$curr_patient->_view}}</td>
  <td>{{$curr_patient->_naissance}}</td>
  <td>{{$curr_patient->tel}}</td>
  <td>{{$curr_patient->tel2}}</td>
  <td class="button"><button type="button" onclick="setClose({{$curr_patient->patient_id}}, '{{$curr_patient->_view|escape:javascript}}')">selectionner</button></td>
</tr>
{{/foreach}}
</table>

<table class="form">

<tr>
  <td class="button" colspan="2">
    <button class="submit" type="button" onclick="createPat()">Créer un patient</button>
    <button class="cancel" type="button" onclick="window.close()">Annuler</button>
  </td>
</tr>

</table>

</form>
