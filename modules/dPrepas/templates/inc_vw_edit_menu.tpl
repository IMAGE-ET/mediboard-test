<script type="text/javascript">
function pageMain() {
  regFieldCalendar("editMenu", "debut");
  regFieldCalendar("editMenu", "fin");
}
</script>
  <tr>
    <td class="halfPane">
      <a class="buttonnew" href="index.php?m={{$m}}&amp;tab=vw_edit_menu&amp;menu_id=0">
        Créer un nouveau menu
      </a>
      <table class="tbl">
        <tr>
          <th>Nom</th>
          <th>Type</th>
          <th>Plats</th>
        </tr>
        {{foreach from=$listMenus item=curr_menu}}
        <tr>
          <td>
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_menu&amp;menu_id={{$curr_menu->menu_id}}" title="Modifier le repas">
              {{$curr_menu->nom}}
            </a>
          </td>
          <td>
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_menu&amp;menu_id={{$curr_menu->menu_id}}" title="Modifier le repas">
              {{$curr_menu->_ref_typerepas->nom}}
            </a>
          </td>
          <td class="text">
            <a href="index.php?m={{$m}}&amp;tab=vw_edit_menu&amp;menu_id={{$curr_menu->menu_id}}" title="Modifier le repas">
              {{assign var="premier" value=1}}
              {{foreach from=$typePlats->_enums.type item=curr_typePlat}}
                {{if $curr_menu->$curr_typePlat}}
                  {{if $premier}}
                    {{assign var="premier" value=0}}
                  {{else}}
                    &mdash;
                  {{/if}}
                  {{$curr_menu->$curr_typePlat}}
                {{/if}}
              {{/foreach}}
            </a>
          </td>
        </tr>
        {{/foreach}}        
      </table>  
    </td>
    <td class="halfPane">
      <form name="editMenu" action="./index.php?m={{$m}}&amp;tab=vw_edit_menu" method="post" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPrepas" />
      <input type="hidden" name="dosql" value="do_menu_aed" />
	  <input type="hidden" name="menu_id" value="{{$menu->menu_id}}" />
	  <input type="hidden" name="group_id" value="{{if $menu->menu_id}}{{$menu->group_id}}{{else}}{{$g}}{{/if}}" />
      <input type="hidden" name="del" value="0" />
      <table class="form">
        <tr>
          {{if $menu->menu_id}}
          <th class="title modify" colspan="2">Modification du menu {{$menu->_view}}</th>
          {{else}}
          <th class="title" colspan="2">Création d'un menu</th>
          {{/if}}
        </tr>     
        <tr>
          <th><label for="nom" title="Nom du menu, obligatoire">Nom</label></th>
          <td><input name="nom" title="{{$menu->_props.nom}}" type="text" value="{{$menu->nom}}" /></td>
        </tr>
        <tr>
          <th><label for="typerepas" title="Type de repas">Type de repas</label></th>
          <td colspan="3">
            <select name="typerepas" title="{{$menu->_props.typerepas}}">
              {{foreach from=$listTypeRepas item=curr_typerepas}}
                <option value="{{$curr_typerepas->typerepas_id}}" {{if $menu->typerepas==$curr_typerepas->typerepas_id}}selected="selected"{{/if}}>
                  {{$curr_typerepas->nom}}
                </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="debut" title="Date de début">Début</label></th>
          <td class="date">
            <div id="editMenu_debut_da">{{if $menu->menu_id}}{{$menu->debut|date_format:"%d/%m/%Y"}}{{else}}{{$date_debut|date_format:"%d/%m/%Y"}}{{/if}}</div>
            <input type="hidden" name="debut" title="{{$menu->_props.debut}}" value="{{if $menu->menu_id}}{{$menu->debut}}{{else}}{{$date_debut}}{{/if}}" />
            <img id="editMenu_debut_trigger" src="./images/icons/calendar.gif" alt="calendar" title="Choisir une date de début" />
          </td>
        </tr>
        <tr>
          <th><label for="repetition" title="Répétition">Répétition</label></th>
          <td>
            1 sem. / 
            {{html_options name="repetition" options=$listRepeat title=$menu->_props.repetition selected=$menu->repetition}}
          </td>
        </tr>
        <tr>
          <th><label for="nb_repet" title="Nombre de répétition">Nombre de répétition</label></th>
          <td>
            <input size="3" name="nb_repet" title="{{$menu->_props.nb_repet}}" type="text" value="{{$menu->nb_repet}}" />
          </td>
        </tr>
        <tr>
          <th><label for="plat1" title="Plat n°1">1er Plat</label></th>
          <td><input name="plat1" title="{{$menu->_props.plat1}}" type="text" value="{{$menu->plat1}}" /></td>
        </tr>
        <tr>
          <th><label for="plat2" title="Plat n°2">2nd Plat</label></th>
          <td><input name="plat2" title="{{$menu->_props.plat2}}" type="text" value="{{$menu->plat2}}" /></td>
        </tr>
        <tr>
          <th><label for="plat3" title="Plat n°3">3ème Plat</label></th>
          <td><input name="plat3" title="{{$menu->_props.plat3}}" type="text" value="{{$menu->plat3}}" /></td>
        </tr>
        <tr>
          <th><label for="plat4" title="Plat n°4">4ème Plat</label></th>
          <td><input name="plat4" title="{{$menu->_props.plat4}}" type="text" value="{{$menu->plat4}}" /></td>
        </tr>
        <tr>
          <th><label for="plat5" title="Plat n°5">5ème Plat</label></th>
          <td><input name="plat5" title="{{$menu->_props.plat5}}" type="text" value="{{$menu->plat5}}" /></td>
        </tr>
        <tr>
          <th><label for="boisson" title="Boisson pour ce repas">Boisson</label></th>
          <td><input name="boisson" title="{{$menu->_props.boisson}}" type="text" value="{{$menu->boisson}}" /></td>
        </tr>
        <tr>
          <th><label for="pain" title="Pain pour ce repas">Pain</label></th>
          <td><input name="pain" title="{{$menu->_props.pain}}" type="text" value="{{$menu->pain}}" /></td>
        </tr>
        <tr>
          <th><label for="diabete_1" title="Repas adapté pour les diabétique">Diabétique</label></th>
          <td>
            <input name="diabete" value="1" type="radio" {{if $menu->diabete}} checked="checked" {{/if}} />
            <label for="diabete_1">Oui</label>
            <input name="diabete" value="0" type="radio" {{if !$menu->diabete || !$menu->menu_id}} checked="checked" {{/if}} />
            <label for="diabete_0">Non</label>
          </td>
        </tr>
        <tr>
          <th><label for="sans_sel_1" title="Repas sans sel">Sans sel</label></th>
          <td>
            <input name="sans_sel" value="1" type="radio" {{if $menu->sans_sel}} checked="checked" {{/if}} />
            <label for="sans_sel_1">Oui</label>
            <input name="sans_sel" value="0" type="radio" {{if !$menu->sans_sel || !$menu->menu_id}} checked="checked" {{/if}} />
            <label for="sans_sel_0">Non</label>
          </td>
        </tr>
        <tr>
          <th><label for="sans_residu_1" title="Repas sans résidu">Sans résidu</label></th>
          <td>
            <input name="sans_residu" value="1" type="radio" {{if $menu->sans_residu}} checked="checked" {{/if}} />
            <label for="sans_residu_1">Oui</label>
            <input name="sans_residu" value="0" type="radio" {{if !$menu->sans_residu || !$menu->menu_id}} checked="checked" {{/if}} />
            <label for="sans_residu_0">Non</label>
          </td>
        </tr>
        <tr>
          <th><label for="modif_1" title="Repas modifiable">Modifiable</label></th>
          <td>
            <input name="modif" value="1" type="radio" {{if $menu->modif || !$menu->menu_id}} checked="checked" {{/if}} />
            <label for="modif_1">Oui</label>
            <input name="modif" value="0" type="radio" {{if !$menu->modif && $menu->menu_id}} checked="checked" {{/if}} />
            <label for="modif_0">Non</label>
          </td>
        </tr>
        <tr>
          <td class="button" colspan="2">
            {{if $menu->menu_id}}
              <button class="submit" type="modify">Modifier</button>
              <button class="trash" type="button" onclick="confirmDeletion(this.form,{typeName:'le menu',objName:'{{$menu->_view|smarty:nodefaults|JSAttribute}}'})">Supprimer</button>
            {{else}}
              <button class="submit" type="submit">Créer</button>
            {{/if}}
          </td>
        </tr>  
      </table>
      </form>
    </td>
  </tr>