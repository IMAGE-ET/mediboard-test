      <form name="find" action="./index.php" method="get">

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="2">Recherche d'un utilisateur</th>
        </tr>
  
        <tr>
          <th><label for="user_last_name" title="Nom de l'utilisateur à rechercher, au moins les premières lettres">Nom</label></th>
          <td><input tabindex="1" type="text" name="user_last_name" value="{{$user_last_name}}" /></td>
        </tr>
        
        <tr>
          <th><label for="user_first_name" title="Prénom de l'utilisateur à rechercher, au moins les premières lettres">Prénom</label></th>
          <td><input tabindex="2" type="text" name="user_first_name" value="{{$user_first_name}}" /></td>
        </tr>
        
        <tr>
          <th><label for="user_type" title="Type de l'utilisateur">Type</label></th>
          <td>
            <select name="user_type">
              {{foreach from=$utypes key=curr_key item=type}}
              <option value="{{$curr_key}}" {{if $curr_key == $user_type}}selected="selected"{{/if}}>{{$type}}</option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        
        <tr>
          <td class="button" colspan="2"><button class="search" type="submit">Rechercher</button></td>
        </tr>
      </table>
      </form>
      <table class="tbl">
        <tr>
          <th>Utilisateur</th>
          <th>Type</th>
        </tr>

        {{foreach from=$users item=curr_user}}
        <tr>
          <td class="text">
            <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;user_id={{$curr_user->user_id}}">
              {{$curr_user->_view}}
            </a>
          </td>
          <td class="text">
            {{assign var="type" value=$curr_user->user_type}}
            <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;user_id={{$curr_user->user_id}}">
              {{$utypes.$type}}
            </a>
          </td>
        </tr>
        {{/foreach}}
        
      </table>