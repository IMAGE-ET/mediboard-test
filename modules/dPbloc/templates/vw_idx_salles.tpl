<!-- $Id$ -->

<table class="main">

<tr>
  <td class="halfPane">

    <a class="buttonnew" href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;salle_id=0"><strong>Créer une salle</strong></a>

    <table class="tbl">
      
    <tr>
      <th>liste des salles</th>
    </tr>
    
    {{foreach from=$salles item=curr_salle}}
    <tr>
      <td><a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;salle_id={{$curr_salle->id}}">{{$curr_salle->nom}}</a></td>
    </tr>
    {{/foreach}}
      
    </table>

  </td>
  
  <td class="halfPane">

    <form name="salle" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

    <input type="hidden" name="dosql" value="do_salle_aed" />
    <input type="hidden" name="id" value="{{$salleSel->id}}" />
    <input type="hidden" name="del" value="0" />

    <table class="form">

    <tr>
      <th class="category" colspan="2">
      {{if $salleSel->id}}
        Modification de la salle &lsquo;{{$salleSel->nom}}&rsquo;
      {{else}}
        Création d'une salle
      {{/if}}
      </th>
    </tr>

    <tr>
      <th><label for="nom" title="Intitulé de la salle. Obligatoire">Intitulé</label></th>
      <td><input type="text" title="{{$salleSel->_props.nom}}" name="nom" value="{{$salleSel->nom}}" /></td>
    </tr>
    
    <tr>
      <th><label for="stats_0" title="Prendre ou non en compte la salle dans les statistiques">Stats</label></th>
      <td>
        <input type="radio" name="stats" value="1" {{if $salleSel->stats}}checked="checked"{{/if}} />
        <label for="stats_1" title="La prendre en compte dans les statistiques">Oui</label>
        <input type="radio" name="stats" value="0" {{if !$salleSel->stats || !$salleSel->id}}checked="checked"{{/if}} />
        <label for="stats_0" title="Ne pas la prendre en compte dans les statistiques">Non</label>
      </td>
    </tr>
    
    <tr>
      <td class="button" colspan="2">
        {{if $salleSel->id}}
        <button class="submit" type="submit">Valider</button>
        <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'la salle',objName:'{{$salleSel->nom|escape:javascript}}'})">
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
