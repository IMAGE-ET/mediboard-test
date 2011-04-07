{{* $Id: configure.tpl 10842 2010-12-08 21:57:35Z MyttO $ *}}

{{*
 * @package Mediboard
 * @subpackage admin
 * @version $Revision: 10842 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<form name="listCriteresRechercheLDAP" action="?" method="get" onsubmit="return Url.update(this, 'search-results');">
  <input type="hidden" name="m" value="admin" />
  <input type="hidden" name="a" value="ajax_choose_filter_ldap" />
  <table class="form">
    <tr>
      <th class="title" colspan="6">{{tr}}CLDAP_search_criteria{{/tr}}</th>
    </tr>
    <tr>
      <th>{{mb_title class="CUser" field="user_username"}}</th>
      <td>
        <input type="text" name="user_username" value="" />
      </td>
      <th>{{mb_title class="CUser" field="user_first_name"}}</th>
      <td>
        <input type="text" name="user_first_name" value="" />
      </td>
      <th>{{mb_title class="CUser" field="user_last_name"}}</th>
      <td>
        <input type="text" name="user_last_name" value="" />
      </td>
    </tr>
    <tr>
      <td colspan="6" style="text-align: center">
        <button type="submit" class="search">{{tr}}Search{{/tr}}</button>
      </td>
    </tr>
  </table>
</form>

<div id="search-results">


</div>