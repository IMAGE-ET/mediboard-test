{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage mediusers
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

function popUpdateMediusers() {
  var url = new Url("mediusers", "update_mediusers_csv");
  url.popup(800, 600, "Modification des utilisateurs");
  return false;
}
 
</script>

<h2>Actions de maintenances</h2>

<table class="tbl">
  <tr>
    <th class="narrow">{{tr}}Action{{/tr}}</th>
    <th >{{tr}}Status{{/tr}}</th>
  </tr>
  
  <tr>
    <td>
      <button class="hslip" onclick="return popUpdateMediusers();">
        {{tr}}Update-Mediusers-CSV{{/tr}}</button>
      </button>
    </td>
    <td>
    </td>
  </tr>
</table>