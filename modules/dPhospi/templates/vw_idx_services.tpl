<table class="main">

<tr>
  <td class="halfPane">

    <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;service_id=0" class="buttonnew"><strong>Créer un service</strong></a>

    <table class="tbl">
      
    <tr>
      <th colspan="2">Liste des services</th>
    </tr>
    
    <tr>
      <th>Intitulé</th>
      <th>Desccription</th>
    </tr>
    
	{{foreach from=$services item=curr_service}}
    <tr>
      <td><a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;service_id={{$curr_service->service_id}}">{{$curr_service->nom}}</a></td>
      <td class="text">{{$curr_service->description|nl2br}}</td>
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
        Création d'un service
      {{/if}}
      </th>
    </tr>

    <tr>
      <th><label for="nom" title="intitulé du service, obligatoire.">Intitulé</label></th>
      <td><input type="text" title="{{$serviceSel->_props.nom}}" name="nom" value="{{$serviceSel->nom}}" /></td>
    </tr>
    
    <tr>
      <th><label for="description" title="Description du service, responsabilités, lignes de conduite.">Description</label></th>
      <td><textarea name="description" rows="4">{{$serviceSel->description}}</textarea></td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {{if $serviceSel->service_id}}
        <button class="modify" type="submit">Valider</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le service ',objName:'{{$serviceSel->nom|escape:javascript}}'})">
          Supprimer
        </button>
        {{else}}
        <button class="submit" name="btnFuseAction" type="submit">Créer</button>
        {{/if}}
      </td>
    </tr>

    </table>
    
    </form>

  </td>
</tr>

</table>
