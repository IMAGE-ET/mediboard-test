<!-- $Id$ -->

<table class="main">

<tr>
  <td class="halfPane">

    <a class="buttonnew" href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;salle_id=0"><strong>Créer une salle</strong></a>

    <table class="tbl">
      
    <tr>
      <th>liste des salles</th>
      <th>Etablissement</th>
    </tr>
    
    {{foreach from=$salles item=curr_salle}}
    <tr {{if $curr_salle->_id == $salleSel->_id}}class="selected"{{/if}}>
      <td><a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;salle_id={{$curr_salle->salle_id}}">{{$curr_salle->nom}}</a></td>
      <td>{{$curr_salle->_ref_group->text}}</td>
    </tr>
    {{/foreach}}
      
    </table>

  </td>
  
  <td class="halfPane">

    <form name="salle" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_salle_aed" />
    <input type="hidden" name="salle_id" value="{{$salleSel->salle_id}}" />
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {{if $salleSel->salle_id}}
        Modification de la salle &lsquo;{{$salleSel->nom}}&rsquo;
      {{else}}
        Création d'une salle
      {{/if}}
      </th>
    </tr>

    <tr>
      <th>{{mb_label object=$salleSel field="group_id"}}</th>
      <td>
        <select class="{{$salleSel->_props.group_id}}" name="group_id">
          <option value="">&mdash; Choisir un établissement</option>
          {{foreach from=$etablissements item=curr_etab}}
          <option value="{{$curr_etab->group_id}}" {{if ($salleSel->salle_id && $salleSel->group_id==$curr_etab->group_id) || (!$salleSel->salle_id && $g==$curr_etab->group_id)}} selected="selected"{{/if}}>{{$curr_etab->text}}</option>
          {{/foreach}}
        </select>
      </td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$salleSel field="nom"}}</th>
      <td>{{mb_field object=$salleSel field="nom"}}</td>
    </tr>
    
    <tr>
      <th>{{mb_label object=$salleSel field="stats"}}</th>
      <td>
        <input type="radio" name="stats" value="1" {{if $salleSel->stats}}checked="checked"{{/if}} />
        <label for="stats_1" title="La prendre en compte dans les statistiques">Oui</label>
        <input type="radio" name="stats" value="0" {{if !$salleSel->stats || !$salleSel->salle_id}}checked="checked"{{/if}} />
        <label for="stats_0" title="Ne pas la prendre en compte dans les statistiques">Non</label>
      </td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {{if $salleSel->salle_id}}
        <button class="submit" type="submit">Valider</button>
        <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'la salle',objName:'{{$salleSel->nom|smarty:nodefaults|JSAttribute}}'})">
          Supprimer
        </button>
        {{else}}
        <button type="submit" name="btnFuseAction" class="new">
          Créer
        </button>
        {{/if}}
      </td>
    </tr>

    </table>

    </form>
  </td>
</tr>

</table>
