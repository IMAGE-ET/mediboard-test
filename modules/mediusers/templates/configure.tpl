{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage mediusers
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
addPerms = function() {
  var url = new Url("mediusers", "ajax_add_user_function_group_perms");
  url.requestUpdate("resultDroits");
}
</script>

<form name="editConfigMediusers" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">
  <input type="hidden" name="dosql" value="do_configure" />
  <input type="hidden" name="m" value="system" />
  <table class="form">    
    <tr>
      <th class="category" colspan="10">{{tr}}config-{{$m}}{{/tr}}</th>
    </tr>

    {{mb_include module=system template=inc_config_str var=tag_mediuser}}
      
    <tr>
      <td class="button" colspan="10">
        <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
    
    <tr>
      <th class="category" colspan="10">Ajout des droits utilisateurs sur sa fonction et son groupe</th>
    </tr>
    <tr>
      <td class="button"><button type="button" onclick="addPerms();">Ajouter les droits</button></td>
      <td colspan="9" id="resultDroits"></td>
    </tr>
  </table>
</form>