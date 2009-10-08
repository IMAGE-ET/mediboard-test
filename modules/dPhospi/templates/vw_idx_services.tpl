<table class="main">
<tr>
  <td class="halfPane">
    <a href="?m={{$m}}&amp;tab={{$tab}}&amp;service_id=0" class="button new">
      Créer un service
    </a>
    
    <!-- Liste des services -->
    <table class="tbl">
    <tr><th colspan="3">Liste des services</th></tr>
    <tr>
      <th>Intitulé</th>
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
  	<!-- Formulaire d'un service -->
    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_service_aed" />
    <input type="hidden" name="service_id" value="{{$serviceSel->_id}}" />
    <input type="hidden" name="del" value="0" />

    <table class="form">
    <tr>
      {{if $serviceSel->_id}}
      <th class="title modify" colspan="2">
		    {{mb_include module=system template=inc_object_idsante400 object=$serviceSel}}
		    {{mb_include module=system template=inc_object_history object=$serviceSel}}
        Modification du service &lsquo;{{$serviceSel->_view}}&rsquo;
      </th>
      {{else}}
      <th class="title" colspan="2">
        Création d'un service
      </th>
      {{/if}}
    </tr>

    <tr>
      <th>{{mb_label object=$serviceSel field="group_id"}}</th>
      <td>
        <select class="{{$serviceSel->_props.group_id}}" name="group_id">
          <option>&mdash; Choisir un établissement</option>
          {{foreach from=$etablissements item=curr_etab}}
          <option value="{{$curr_etab->group_id}}" {{if ($serviceSel->_id && $serviceSel->group_id==$curr_etab->_id) || (!$serviceSel->_id && $g==$curr_etab->_id)}} selected="selected"{{/if}}>{{$curr_etab->text}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>

    <tr>
      <th>{{mb_label object=$serviceSel field=nom}}</th>
      <td>{{mb_field object=$serviceSel field=nom}}</td>
    </tr>       

    <tr>
      <th>{{mb_label object=$serviceSel field=urgence}}</th>
      <td>{{mb_field object=$serviceSel field=urgence}}</td>
    </tr>    

    <tr>
      <th>{{mb_label object=$serviceSel field=description}}</th>
      <td>{{mb_field object=$serviceSel field=description}}</td>
    </tr>    

    <tr>
      <td class="button" colspan="2">
        {{if $serviceSel->service_id}}
        <button class="modify" type="submit">Valider</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le service ',objName:'{{$serviceSel->nom|smarty:nodefaults|JSAttribute}}'})">
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
