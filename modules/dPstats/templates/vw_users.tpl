{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstats
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

Main.add(function () {
  var form1 = getForm("users"),
      form2 = getForm("Form1");
  Calendar.regField(form1.debutlog);
  Calendar.regField(form1.finlog);
  Calendar.regField(form2.debutact);
  Calendar.regField(form2.finact);
});

</script>

<table class="main">
  <tr>
    <td>
      <form name="users" action="?" method="get" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="4" class="category">Activité des utilisateurs</th>
        </tr>
        <tr>
          <th><label for="debutlog" title="Date de début">Début:</label></th>
          <td>
            <input type="hidden" name="debutlog" class="notNull date" value="{{$debutlog}}" />
          </td>
          <th><label for="user_id" title="Utilisateur">Utilisateur</label></th>
          <td>
            <select name="user_id">
            {{foreach from=$listUsers item=curr_user}}
              <option value={{$curr_user->user_id}} {{if $curr_user->user_id == $user_id}}selected="selected"{{/if}}>
                {{$curr_user->_view}}
              </option>
            {{/foreach}}
            </select>
          </td>
        </tr>
        <tr>
          <th><label for="finlog" title="Date de fin">Fin:</label></th>
          <td>
            <input type="hidden" name="finlog" class="notNull date moreEquals|debutlog" value="{{$finlog}}" />
          </td>
          <td colspan="2" />
        </tr>
        <tr>
          <td colspan="4" class="button"><button class="search" type="submit">Afficher</button></td>
        </tr>
        <tr>
          <td colspan="4" class="button">
            <img alt="Activité utilisateur" src='?m=dPstats&amp;a=graph_users&amp;suppressHeaders=1&amp;debut={{$debutlog}}&amp;fin={{$finlog}}&amp;user_id={{$user_id}}' />
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td>
      <form name="Form1" action="?" method="get" onsubmit="return checkForm(this)">
      <input type="hidden" name="m" value="dPstats" />
      <table class="form">
        <tr>
          <th colspan="2" class="category">Autre graph</th>
        </tr>
        <tr>
          <th><label for="debutact" title="Date de début">Début:</label></th>
          <td>
            <input type="hidden" name="debutact" class="notNull date" value="{{$debutact}}" />
          </td>
        </tr>
        <tr>
          <th><label for="finact" title="Date de fin">Fin:</label></th>
          <td>
            <input type="hidden" name="finact" class="notNull date moreEquals|debutact" value="{{$finact}}" />
          </td>
        </tr>
        <tr>
          <td colspan="2" class="button"><button class="search" type="submit">Afficher</button></td>
        </tr>
        <tr>
          <td colspan="2" class="button">
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
</table>