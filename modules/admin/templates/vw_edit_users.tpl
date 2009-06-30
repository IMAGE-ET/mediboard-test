{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
  <tr>
    <td class="halfPane">
      <a class="button new" href="?m={{$m}}&amp;user_id=0">
        Créer un nouvel utilisateur
      </a>
      {{include file="inc_list_users.tpl"}}
    </td>
    <td class="halfPane">
      {{if $can->edit}}
      {{include file="inc_edit_user.tpl"}}
      {{else}}
      {{include file="inc_vw_user.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>