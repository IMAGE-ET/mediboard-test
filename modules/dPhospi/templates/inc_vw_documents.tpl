<form name="newDocumentFrm" action="?m={{$m}}" method="post">
<table>
  <tr>
    <td>
      <select name="_choix_modele" onchange="if (this.value) Document.create(this.value, {{$sejour->_id}})" class="notNull ref">
        <option value="">&mdash; Choisir un modèle</option>
        <optgroup label="Modèles du praticien">
        {{foreach from=$listModelePrat item=curr_modele}}
          <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
        {{/foreach}}
        </optgroup>
        <optgroup label="Modèles du cabinet">
         {{foreach from=$listModeleFunc item=curr_modele}}
         <option value="{{$curr_modele->compte_rendu_id}}">{{$curr_modele->nom}}</option>
        {{/foreach}}
        </optgroup>
      </select>
      
      <select name="_choix_pack" onchange="if (this.value) DocumentPack.create(this.value, {{$sejour->_id}})">
        <option value="">&mdash; {{tr}}pack-choice{{/tr}}</option>
        {{foreach from=$packList item=curr_pack}}
          <option value="{{$curr_pack->pack_id}}">{{$curr_pack->nom}}</option>
        {{foreachelse}}
          <option value="">{{tr}}pack-none{{/tr}}</option>
        {{/foreach}}
      </select>
    </td>
  </tr>
</table>
</form>
<div id="document-{{$sejour->_id}}">
{{if $sejour->_ref_documents|@count}}
<table class="tbl">
  <tr id="sejour{{$sejour->_id}}-trigger">
    <th colspan="2">{{$sejour->_ref_documents|@count}} document(s)</th>
  </tr>
  <tbody class="sejourEffect" id="sejour{{$sejour->_id}}" style="display:none;">
  {{foreach from=$sejour->_ref_documents item=document}}
  <tr>
    <td>{{$document->nom}}</td>
    <td class="button">
      <form name="editDocumentFrm{{$document->compte_rendu_id}}" action="?m={{$m}}" method="post">
      <input type="hidden" name="m" value="dPcompteRendu" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="dosql" value="do_modele_aed" />
      <input type="hidden" name="object_id" value="{{$sejour->_id}}" />
      {{mb_field object=$document field="compte_rendu_id" hidden=1 prop=""}}
      <button class="edit notext" type="button" onclick="Document.edit({{$document->compte_rendu_id}})">
      </button>
      <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'le document',objName:'{{$document->nom|smarty:nodefaults|JSAttribute}}',ajax:1,target:'systemMsg'}, { onComplete: function() { reloadAfterSaveDoc({{$sejour->_id}}) } })" />
      </form>
    </td>
  </tr>
  {{/foreach}}
  </tbody>
</table>
{{/if}}
</div>

<script type="text/javascript">

// Initalisation du pairEffect
PairEffect.initGroup("sejourEffect", { 
  bStoreInCookie: true
});
        
</script>