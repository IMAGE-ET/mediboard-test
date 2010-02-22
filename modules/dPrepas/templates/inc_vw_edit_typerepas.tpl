  <tr>
    <td class="halfPane">
      <a class="button new" href="?m={{$m}}&amp;tab=vw_edit_plats&amp;typerepas_id=0">
        Créer un nouveau type de repas
      </a>
      <table class="tbl">
        <tr>
          <th>Nom</th>
          <th>Début</th>
          <th>Fin</th>
        </tr>
        {{foreach from=$listTypeRepas item=curr_type}}
        <tr>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_plats&amp;typerepas_id={{$curr_type->typerepas_id}}" title="Modifier le type de plat">
              {{$curr_type->nom}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_plats&amp;typerepas_id={{$curr_type->typerepas_id}}" title="Modifier le type de plat">
              {{$curr_type->debut|date_format:$dPconfig.time}}
            </a>
          </td>
          <td>
            <a href="?m={{$m}}&amp;tab=vw_edit_plats&amp;typerepas_id={{$curr_type->typerepas_id}}" title="Modifier le type de plat">
              {{$curr_type->fin|date_format:$dPconfig.time}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>  
    </td>
    <td class="halfPane">
      <form name="editTypeRepas" action="?m={{$m}}&amp;tab=vw_edit_plats" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPrepas" />
      <input type="hidden" name="dosql" value="do_typerepas_aed" />
  	  <input type="hidden" name="typerepas_id" value="{{$typeRepas->typerepas_id}}" />
  	  <input type="hidden" name="group_id" value="{{if $typeRepas->typerepas_id}}{{$typeRepas->group_id}}{{else}}{{$g}}{{/if}}" />
      <input type="hidden" name="del" value="0" />
      
      <table class="form">
        <tr>
          {{if $typeRepas->typerepas_id}}
          <th class="title modify" colspan="2">Modification du type de repas {{$typeRepas->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'un type de repas</th>
          {{/if}}
        </tr>
        <tr>
	      <th>{{mb_label object=$typeRepas field="nom"}}</th>
          <td>{{mb_field object=$typeRepas field="nom"}}</td>
        </tr>
        <tr>
          <th>{{mb_label object=$typeRepas field="_debut"}}</th>
          <td>
            {{html_options name="_debut" options=$listHours class="num" selected=$typeRepas->_debut}}
            h
          </td>
        </tr>
        <tr>
          <th>{{mb_label object=$typeRepas field="_fin"}}</th>
          <td>
            {{html_options name="_fin" options=$listHours class="num moreThan|_debut" selected=$typeRepas->_fin}}
            h
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $typeRepas->typerepas_id}}
              <button class="submit" type="submit">Modifier</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le type de repas',objName:'{{$typeRepas->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{else}}
              <button class="submit" type="submit">Créer</button>
            {{/if}}
          </td>
        </tr>  
      </table>
      
      </form>
    </td>
  </tr>