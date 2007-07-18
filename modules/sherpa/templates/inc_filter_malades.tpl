      <form name="find" action="./index.php" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="new" value="1" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="3">Recherche d'un malade</th>
        </tr>
        <tr>
          <th><label for="nom" title="Nom du malade à rechercher, au moins les premières lettres">Nom</label></th>
          <td><input tabindex="1" type="text" name="nom" value="{{$nom|stripslashes}}" /></td>
 				</tr>
        <tr>
          <th><label for="prenom" title="Prénom du malade à rechercher, au moins les premières lettres">Prénom</label></th>
          <td><input tabindex="2" type="text" name="prenom" value="{{$prenom|stripslashes}}" /></td>
        </tr>
        <tr>
          <th colspan="1">
            <label for="check_naissance" title="Date de naissance du malade à rechercher">
              <input type="checkbox" name="check_naissance" onclick="affNaissance()" {{if $naissance == "on"}}checked="checked"{{/if}}/>
              <input type="hidden" name="naissance" {{if $naissance == "on"}}value="on"{{else}}value="off"{{/if}} />
              Date de naissance
            </label>
          </th>
          <td colspan="2">
            {{if $naissance == "on"}}
            {{html_select_date
                 time=$dateMal
                 start_year=1900
                 field_order=DMY
                 all_extra="style='display:inline;'"}}
             {{else}}
            {{html_select_date
                 time=$dateMal
                 start_year=1900
                 field_order=DMY
                 all_extra="style='display:none;'"}}
             {{/if}}
          </td>
        </tr>
        
        <tr>
          <td class="button" colspan="3">
            <button class="search" type="submit">Rechercher</button>
          </td>
        </tr>
      </table>
      </form>
      <table class="tbl">
        <tr>
          <th>Nom</th>
          <th>Prenom</th>
          <th>Date de naissance</th>
        </tr>

        {{assign var="href" value="index.php?m=sherpa&tab=view_malades&malnum="}}
        
        {{foreach from=$malades item=curr_malade}}
        <tr {{if $malade->_id == $curr_malade->_id}}class="selected"{{/if}}>
          <td class="text">
            <a href="{{$href}}{{$curr_malade->malnum}}">
              {{mb_value object=$curr_malade field="malnom"}}
            </a>
          </td>
          <td class="text"> 
            <a href="{{$href}}{{$curr_malade->malnum}}">
              {{mb_value object=$curr_malade field="malpre"}}
            </a>
          </td>
          <td class="text">
            <a href="{{$href}}{{$curr_malade->malnum}}">
              {{mb_value object=$curr_malade field="datnai"}}
            </a>
          </td>
        </tr>
        {{/foreach}}
      </table>
      </form>