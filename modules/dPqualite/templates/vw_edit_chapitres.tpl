<table class="main">
  <tr>
    <td class="halfPane" rowspan="3">
    
      <form name="FrmTypeVue" action="?m={{$m}}" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <label for="typeVue">Classification</label>
      <select name="typeVue" onchange="this.form.submit();">
        <option value="0" {{if $typeVue == 0}}selected="selected"{{/if}}>Gestion des Chapitres</option>
        <option value="1" {{if $typeVue == 1}}selected="selected"{{/if}}>Gestion des Thèmes</option>
      </select>
      </form><br />

      <a class="buttonnew" href="index.php?m=dPqualite&amp;tab=vw_edit_classification&amp;doc_chapitre_id=0">
        Créer un nouveau Chapitre
      </a>
      <table class="tbl">
        <tr>
          <th>Nom</th>
          <th>Code</th>
        </tr>
        {{foreach from=$listChapitres item=curr_chapitre}}
        <tr>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_edit_classification&amp;doc_chapitre_id={{$curr_chapitre->doc_chapitre_id}}" title="Modifier le chapitre">
              {{$curr_chapitre->nom}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m=dPqualite&amp;tab=vw_edit_classification&amp;doc_chapitre_id={{$curr_chapitre->doc_chapitre_id}}" title="Modifier le chapitre">
              {{$curr_chapitre->code}}
            </a>
          </td>
        </tr>
        {{/foreach}}        
      </table>
    </td>
    <td class="halfPane">
      <form name="editChapitre" action="./index.php?m={{$m}}" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="dosql" value="do_chapitre_aed" />
	  <input type="hidden" name="doc_chapitre_id" value="{{$chapitre->doc_chapitre_id}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $chapitre->doc_chapitre_id}}
          <th class="title" colspan="2" style="color:#f00;">Modification du chapitre: {{$chapitre->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'un chapitre</th>
          {{/if}}
        </tr>   
        <tr>
          <th><label for="nom" title="Nom du chapitre, obligatoire">Nom</label></th>
          <td><input name="nom" title="{{$chapitre->_props.nom}}" type="text" value="{{$chapitre->nom}}" /></td>
        </tr>
        <tr>
          <th><label for="code" title="Code du Chapitre, numérique">Code</label></th>
          <td><input name="code" title="{{$chapitre->_props.code}}" type="text" value="{{$chapitre->code}}" /></td>
        </tr>
        <tr>
          <td class="button" colspan="2">            
            {{if $chapitre->doc_chapitre_id}}
              <button class="modify" type="submit">Modifier</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le chapitre',objName:'{{$chapitre->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{else}}
              <button class="submit" type="submit">Créer</button>
            {{/if}}
          </td>
        </tr>        
      </table>
      </form>
    </td>
  </tr>
</table>