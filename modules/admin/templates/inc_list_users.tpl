      <form name="find" action="./index.php" method="get">

      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      
      <table class="form">
        <tr>
          <th class="category" colspan="4">Recherche d'un utilisateur</th>
        </tr>
  
        <tr>
          <th><label for="user_last_name" title="Nom de l'utilisateur à rechercher, au moins les premières lettres">Nom</label></th>
          <td><input tabindex="1" type="text" name="user_last_name" value="{{$user_last_name}}" /></td>
          <th><label for="user_first_name" title="Nom d'utilisateur (login) à rechercher, au moins les premières lettres">Nom d'utilisateur</label></th>
          <td><input tabindex="3" type="text" name="user_username" value="{{$user_username}}" /></td>
        </tr>
        
        <tr>
          <th><label for="user_first_name" title="Prénom de l'utilisateur à rechercher, au moins les premières lettres">Prénom</label></th>
          <td><input tabindex="2" type="text" name="user_first_name" value="{{$user_first_name}}" /></td>
          <th><label for="user_type" title="Type de l'utilisateur">Type</label></th>
          <td>
            <select tabindex="4" name="user_type">
              {{foreach from=$utypes key=curr_key item=type}}
              <option value="{{$curr_key}}" {{if $curr_key == $user_type}}selected="selected"{{/if}}>{{$type}}</option>
              {{/foreach}}
            </select>
          </td>
        </tr>
        
        <tr>
          <td class="button" colspan="4"><button class="search" type="submit">Rechercher</button></td>
        </tr>
      </table>
      </form>
      <table class="tbl">
        <tr>
          <th>Login</th>
          <th>Utilisateur</th>
          <th>Type</th>
          <th colspan="2">Administration</th>
        </tr>

        {{foreach from=$users item=curr_user}}
        <tr>
          <td>
            <a href="index.php?m={{$m}}&amp;tab={{$tab}}&amp;user_id={{$curr_user->user_id}}">
              {{$curr_user->user_username}}
            </a>
            
          </td>
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
          <td>
            <a class="buttonsearch" href="index.php?m={{$m}}&amp;tab=edit_perms&amp;user_id={{$curr_user->user_id}}">Droits</a>
          </td>
          <td>
            <a class="buttonsearch" href="index.php?m={{$m}}&amp;tab=edit_prefs&amp;user_id={{$curr_user->user_id}}">Préférences</a>
          </td>
          <td>
          </td>
        </tr>
        {{/foreach}}
        
      </table>