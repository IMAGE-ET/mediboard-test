<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_idsante400_aed" />
<input type="hidden" name="id_sante400_id" value="{{$idSante400->_id}}" />
<input type="hidden" name="del" value="0" />

<table class="form">

<tr>
  <th class="category" colspan="2">
  {{if $idSante400->_id}}
    <a style="float:right;" href="javascript:view_log('CIDSante400',{{$idSante400->_id}})">
      <img src="images/history.gif" alt="historique" />
    </a>
    Modification de l'ID400 &lsquo;{{$idSante400->_view}}&rsquo;
  {{else}}
    Création d'un ID400
  {{/if}}
  </th>
  
  <tr>
    <th>
      <label for="object_class" title="Classe de l'object ciblé par Sante400">Classe</label>
    </th>
    <td>
      <select name="object_class" title="{{$idSante400->_props.object_class}}">
        <option value="">&mdash; Choisir une classe</option>
        {{foreach from=$listClasses|smarty:nodefaults item=curr_class}}
        <option value="{{$curr_class}}" {{if $curr_class == $idSante400->object_class}}selected="selected"{{/if}}>
          {{$curr_class}}
        </option>
        {{/foreach}}
      </select>
    </td>
  </tr>

  <tr>
    <th>
      <label for="object_id" title="Identifiant de l'object">Objet</label>
    </th>
    <td>
      <input name="object_id" title="ref" value="{{$idSante400->object_id}}" />
      <button class="search" type="button" onclick="popObject(this)">Chercher</button>
      {{if $idSante400->_id}}
      <br />
      {{$idSante400->_ref_object->_view}}
      {{/if}}      
    </td>
  </tr>

  <tr>
    <th>
      <label for="id400" title="Identifiant Santé 400 de l'objet">ID400</label>
    </th>
    <td>
      <input name="id400" title="{{$idSante400->_props.id400}}" value="{{$idSante400->id400}}" />
    </td>
  </tr>

  <tr>
    <th>
      <label for="tag" title="Etiquette (sémantique) de l'identifiant">Etiquette</label>
    </th>
    <td>
      <input name="tag" title="{{$idSante400->_props.tag}}" value="{{$idSante400->tag}}" />
    </td>
  </tr>

        
  <tr>
    <td class="button" colspan="2">
    {{if $idSante400->_id}}
      <button class="modify" type="submit">Valider</button>
      <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'l\'identifiant',objName:'{{$idSante400->_view|smarty:nodefaults|JSAttribute}}'})">
        Supprimer
      </button>
    {{else}}
      <button class="submit" type="submit" name="btnFuseAction">Créer</button>
    {{/if}}
    </td>
  </tr>

</table>

</form>
