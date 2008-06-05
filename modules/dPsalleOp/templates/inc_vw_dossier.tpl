<!-- Dossier Médical -->
{{include file=../../dPpatients/templates/CDossierMedical_complete.tpl object=$dossier_medical}}

<!-- Production de documents -->
<hr />
<form name="newDocumentFrm" action="?m={{$m}}" method="post">

<select name="_choix_modele" onchange="if (this.value) Document.create(this.value, {{$selOp->_id}})">
  <option value="">&mdash; Choisir un modèle</option>
  <optgroup label="Opération">
    {{foreach from=$crList item=curr_cr}}
      <option value="{{$curr_cr->compte_rendu_id}}">{{$curr_cr->nom}}</option>
    {{/foreach}}
  </optgroup>
  <optgroup label="Hospitalisation">
    {{foreach from=$hospiList item=curr_hospi}}
    <option value="{{$curr_hospi->compte_rendu_id}}">{{$curr_hospi->nom}}</option>
    {{/foreach}}
  </optgroup>
</select>

<select name="_choix_pack" onchange="if (this.value) DocumentPack.create(this.value, {{$selOp->_id}})">
  <option value="">&mdash; {{tr}}pack-choice{{/tr}}</option>
  {{foreach from=$packList item=curr_pack}}
    <option value="{{$curr_pack->pack_id}}">{{$curr_pack->nom}}</option>
  {{foreachelse}}
    <option value="">{{tr}}pack-none{{/tr}}</option>
  {{/foreach}}
</select>

</form>
 
<!-- Affichage de la liste des documents de l'operation -->
<div id="documents">
</div>
