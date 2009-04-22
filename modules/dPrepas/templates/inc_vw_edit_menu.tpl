<tr>
  <td class="halfPane">
    <a class="button new" href="?m={{$m}}&amp;tab=vw_edit_menu&amp;menu_id=0">
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
          <a href="?m={{$m}}&amp;tab=vw_edit_menu&amp;menu_id={{$curr_menu->menu_id}}" title="Modifier le repas">
            {{$curr_menu->nom}}
          </a>
        </td>
        <td>
          <a href="?m={{$m}}&amp;tab=vw_edit_menu&amp;menu_id={{$curr_menu->menu_id}}" title="Modifier le repas">
            {{$curr_menu->_ref_typerepas->nom}}
          </a>
        </td>
        <td class="text">
          <a href="?m={{$m}}&amp;tab=vw_edit_menu&amp;menu_id={{$curr_menu->menu_id}}" title="Modifier le repas">
            {{assign var="premier" value=1}}
            {{foreach from=$typePlats->_specs.type->_list item=curr_typePlat}}
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
    <form name="editMenu" action="?m={{$m}}&amp;tab=vw_edit_menu" method="post" onsubmit="return checkForm(this)">
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
        <th>{{mb_label object=$menu field="nom"}}</th>
        <td>{{mb_field object=$menu field="nom"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="typerepas"}}</th>
        <td colspan="3">
          <select name="typerepas" class="{{$menu->_props.typerepas}}">
            {{foreach from=$listTypeRepas item=curr_typerepas}}
              <option value="{{$curr_typerepas->typerepas_id}}" {{if $menu->typerepas==$curr_typerepas->typerepas_id}}selected="selected"{{/if}}>
                {{$curr_typerepas->nom}}
              </option>
            {{/foreach}}
          </select>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="debut"}}</th>
        <td class="date">{{mb_field object=$menu field="debut" form="editMenu" register=true}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="repetition"}}</th>
        <td>
          1 sem. / 
          {{html_options name="repetition" options=$listRepeat class=$menu->_props.repetition selected=$menu->repetition}}
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="nb_repet"}}</th>
        <td>{{mb_field object=$menu field="nb_repet"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="plat1"}}</th>
        <td>{{mb_field object=$menu field="plat1"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="plat2"}}</th>
        <td>{{mb_field object=$menu field="plat2"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="plat3"}}</th>
        <td>{{mb_field object=$menu field="plat3"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="plat4"}}</th>
        <td>{{mb_field object=$menu field="plat4"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="plat5"}}</th>
        <td>{{mb_field object=$menu field="plat5"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="boisson"}}</th>
        <td>{{mb_field object=$menu field="boisson"}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="pain"}}</th>
        <td>{{mb_field object=$menu field="pain"}}</td>
       </tr>
      <tr>
        <th>{{mb_label object=$menu field="diabete"}}</th>
        <td>
          <input name="diabete" value="1" type="radio" {{if $menu->diabete}} checked="checked" {{/if}} />
          <label for="diabete_1">Oui</label>
          <input name="diabete" value="0" type="radio" {{if !$menu->diabete || !$menu->menu_id}} checked="checked" {{/if}} />
          <label for="diabete_0">Non</label>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="sans_sel"}}</th>
        <td>
          <input name="sans_sel" value="1" type="radio" {{if $menu->sans_sel}} checked="checked" {{/if}} />
          <label for="sans_sel_1">Oui</label>
          <input name="sans_sel" value="0" type="radio" {{if !$menu->sans_sel || !$menu->menu_id}} checked="checked" {{/if}} />
          <label for="sans_sel_0">Non</label>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="sans_residu"}}</th>
        <td>
          <input name="sans_residu" value="1" type="radio" {{if $menu->sans_residu}} checked="checked" {{/if}} />
          <label for="sans_residu_1">Oui</label>
          <input name="sans_residu" value="0" type="radio" {{if !$menu->sans_residu || !$menu->menu_id}} checked="checked" {{/if}} />
          <label for="sans_residu_0">Non</label>
        </td>
      </tr>
      <tr>
        <th>{{mb_label object=$menu field="modif"}}</th>
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