  <tr>
    <td class="halfPane">
      <a class="button new" href="?m={{$m}}&amp;tab=vw_edit_plats&amp;plat_id=0">
        Créer un nouveau plat
      </a>
      <table class="tbl">
        <tr>
          <th>Nom</th>
          <th>Remplacement</th>
          <th>Type</th>
        </tr>
        {{foreach from=$listPlats item=curr_plat}}
        <tr>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_plats&amp;plat_id={{$curr_plat->plat_id}}" title="Modifier le plat">
              {{$curr_plat->nom}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_plats&amp;plat_id={{$curr_plat->plat_id}}" title="Modifier le plat">
              {{tr}}CPlat.type.{{$curr_plat->type}}{{/tr}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_plats&amp;plat_id={{$curr_plat->plat_id}}" title="Modifier le plat">
              {{assign var="keyrepas" value=$curr_plat->typerepas}}
              {{$listTypeRepas.$keyrepas->nom}}
            </a>
          </td>
        </tr>
        {{/foreach}}        
      </table>  
    </td>
    <td class="halfPane">
      <form name="editPlat" action="?m={{$m}}&amp;tab=vw_edit_plats" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPrepas" />
      <input type="hidden" name="dosql" value="do_plat_aed" />
	  <input type="hidden" name="plat_id" value="{{$plat->plat_id}}" />
	  <input type="hidden" name="group_id" value="{{if $plat->plat_id}}{{$plat->group_id}}{{else}}{{$g}}{{/if}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $plat->plat_id}}
          <th class="title modify" colspan="2">Modification du plat {{$plat->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'un plat</th>
          {{/if}}
        </tr>
        
        <tr>
          <th>{{mb_label object=$plat field="nom"}}</th>
          <td>{{mb_field object=$plat field="nom"}}</td>
        </tr>
        
        <tr>
          <th>{{mb_label object=$plat field="type"}}</th>
          <td colspan="3">{{mb_field object=$plat field="type"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$plat field="typerepas"}}</th>
          <td colspan="3">
            <select name="typerepas" class="{{$plat->_props.typerepas}}">
              {{foreach from=$listTypeRepas item=curr_typerepas}}
                <option value="{{$curr_typerepas->typerepas_id}}" {{if $plat->typerepas==$curr_typerepas->typerepas_id}}selected="selected"{{/if}}>
                  {{$curr_typerepas->nom}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $plat->plat_id}}
              <button class="submit" type="submit">Modifier</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le plat',objName:'{{$plat->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{else}}
              <button class="submit" type="submit">Créer</button>
            {{/if}}
          </td>
        </tr>  
      </table>
      </form>
    </td>
  </tr>