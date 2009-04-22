<table class="main">
<tr>
  <td class="halfPane">
    <a href="?m={{$m}}&amp;tab={{$tab}}&amp;prestation_id=0" class="button new">
      Créer une prestation
    </a>
    <table class="tbl">
    <tr>
      <th colspan="3">Liste des prestations</th>
    </tr>
    <tr>
      <th>Niveau de prestation</th>
      <th>Description</th>
      <th>Etablissement</th>
    </tr>
	{{foreach from=$prestations item=_prestation}}
    <tr {{if $_prestation->_id == $prestation->_id}}class="selected"{{/if}}>
      <td><a href="?m={{$m}}&amp;tab={{$tab}}&amp;prestation_id={{$_prestation->_id}}">{{$_prestation->nom}}</a></td>
      <td class="text">{{$_prestation->description|nl2br}}</td>
      <td>{{$_prestation->_ref_group->text}}</td>
    </tr>
    {{/foreach}}
    </table>
  </td> 
  <td class="halfPane">
    <form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
    <input type="hidden" name="dosql" value="do_prestation_aed" />
    <input type="hidden" name="prestation_id" value="{{$prestation->_id}}" />
    <input type="hidden" name="del" value="0" />
    <table class="form">
    <tr>
      {{if $prestation->_id}}
      <th class="title modify" colspan="2">
        <div class="idsante400" id="{{$prestation->_class_name}}-{{$prestation->_id}}"></div>
        <a style="float:right;" href="#nothing" onclick="view_log('{{$prestation->_class_name}}',{{$prestation->_id}})">
        <img src="images/icons/history.gif" alt="historique" title="Voir l'historique" />
        </a>
        Modification de la prestation &lsquo;{{$prestation->nom}}&rsquo;
      </th>
      {{else}}
      <th class="title" colspan="2">
        Création d'une prestation
      </th>
      {{/if}}
    </tr>
    <tr>
      <th>{{mb_label object=$prestation field="group_id"}}</th>
      <td>
        <select class="{{$prestation->_props.group_id}}" name="group_id">
          <option>&mdash; Choisir un établissement</option>
          {{foreach from=$etablissements item=curr_etab}}
          <option value="{{$curr_etab->group_id}}" {{if ($prestation->_id && $prestation->group_id==$curr_etab->_id) || (!$prestation->_id && $g==$curr_etab->_id)}} selected="selected"{{/if}}>{{$curr_etab->text}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$prestation field="nom"}}</th>
      <td>{{mb_field object=$prestation field="nom"}}</td>
    </tr>       
    <tr>
      <th>{{mb_label object=$prestation field="description"}}</th>
      <td>{{mb_field object=$prestation field="description"}}</td>
    </tr>    
    <tr>
      <td class="button" colspan="2">
        {{if $prestation->_id}}
        <button class="modify" type="submit">Valider</button>
        <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'la prestation ',objName:'{{$prestation->nom|smarty:nodefaults|JSAttribute}}'})">
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
