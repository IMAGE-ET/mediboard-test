<table class="main">

<tr>
  <td class="halfPane">

    <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;service_id=0" class="buttonnew"><strong>Cr�er un service</strong></a>

    <table class="tbl">
      
    <tr>
      <th colspan="3">Liste des services</th>
    </tr>
    
    <tr>
      <th>Intitul�</th>
      <th>Description</th>
      <th>Etablissement</th>
    </tr>
    
	{{foreach from=$services item=curr_service}}
    <tr {{if $curr_service->_id == $serviceSel->_id}}class="selected"{{/if}}>
      <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;service_id={{$curr_service->_id}}">{{$curr_service->nom}}</a></td>
      <td class="text">{{$curr_service->description|nl2br}}</td>
      <td>{{$curr_service->_ref_group->text}}</td>
    </tr>
    {{/foreach}}
      
    </table>

  </td>
  
  <td class="halfPane">

    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_service_aed" />
    <input type="hidden" name="service_id" value="{{$serviceSel->service_id}}" />
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {{if $serviceSel->service_id}}
        Modification du service &lsquo;{{$serviceSel->nom}}&rsquo;
      {{else}}
        Cr�ation d'un service
      {{/if}}
      </th>
    </tr>

    <tr>
      <th><label for="group_id" title="Etablissement du service. Obligatoire">Etablissement</label></th>
      <td>
        <select class="{{$serviceSel->_props.group_id}}" name="group_id">
          <option>&mdash; Choisir un �tablissement</option>
          {{foreach from=$etablissements item=curr_etab}}
          <option value="{{$curr_etab->group_id}}" {{if ($serviceSel->_id && $serviceSel->group_id==$curr_etab->_id) || (!$serviceSel->_id && $g==$curr_etab->_id)}} selected="selected"{{/if}}>{{$curr_etab->text}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th><label for="nom" title="intitul� du service, obligatoire.">Intitul�</label></th>
      <td><input type="text" class="{{$serviceSel->_props.nom}}" name="nom" value="{{$serviceSel->nom}}" /></td>
    </tr>
        
    <tr>
      <th><label for="description" title="Description du service, responsabilit�s, lignes de conduite.">Description</label></th>
      <td><textarea name="description" rows="4">{{$serviceSel->description}}</textarea></td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {{if $serviceSel->service_id}}
        <button class="modify" type="submit">Valider</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le service ',objName:'{{$serviceSel->nom|smarty:nodefaults|JSAttribute}}'})">
          Supprimer
        </button>
        {{else}}
        <button class="submit" name="btnFuseAction" type="submit">Cr�er</button>
        {{/if}}
      </td>
    </tr>

    </table>
    
    </form>

  </td>
</tr>

</table>
