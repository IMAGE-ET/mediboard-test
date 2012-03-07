<table class="main">
  
<tr>
  <td class="halfPane">
    <a href="#" onclick="showInfrastructure('secteur_id', '0', 'infrastructure_secteur')" class="button new">
      {{tr}}CSecteur-title-create{{/tr}}
    </a>
    
    <!-- Liste des secteurs -->
    <table class="tbl">
      <tr>
        <th colspan="3" class="title">
          {{tr}}CSecteur.all{{/tr}}
        </th>
      </tr>
      <tr>
        <th>{{mb_title class=CSecteur field=nom}}</th>
        <th>{{mb_title class=CSecteur field=description}}</th>
      </tr>
  
      {{foreach from=$secteurs item=_secteur}}
      <tr {{if $_secteur->_id == $secteur->_id}} class="selected" {{/if}}>
        <td>
          <a href="#" onclick="showInfrastructure('secteur_id', '{{$_secteur->_id}}', 'infrastructure_secteur')">
            {{mb_value object=$_secteur field=nom}}
          </a>
        </td>
        <td class="text">{{mb_value object=$_secteur field=description}}</td>
      </tr>
      {{foreachelse}}
        <tr>
          <td class="empty" colspan="2">
            {{tr}}CSecteur.none{{/tr}}
          </td>
        </tr>
      {{/foreach}}
    </table>
  </td> 

  <td class="halfPane" id="infrastructure_secteur">
    {{mb_include module=hospi template=inc_vw_secteur}}
  </td>
</tr>

</table>
