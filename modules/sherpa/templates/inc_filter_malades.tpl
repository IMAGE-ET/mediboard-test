      <form name="find" action="./index.php" method="get">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="new" value="1" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="3">Recherche d'un malade</th>
        </tr>
        <tr>
          <th>{{mb_label object=$filter field="malnom"}}</th>
      		<td>{{mb_field object=$filter field="malnom"}}</td>
 				</tr>
        <tr>
          <th>{{mb_label object=$filter field="malpre"}}</th>
      		<td>{{mb_field object=$filter field="malpre"}}</td>
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
          <th>{{mb_label object=$filter field="malnom"}}</th>
          <th>{{mb_label object=$filter field="malpre"}}</th>
          <th>{{mb_label object=$malade field="datnai"}}</th>
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