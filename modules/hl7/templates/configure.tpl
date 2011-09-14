{{* $Id: configure.tpl 10085 2010-09-16 09:20:46Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10085 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}



<script type="text/javascript">
  Main.add(Control.Tabs.create.curry('tabs-configure', true));
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#config-source">{{tr}}config-hl7v2-source{{/tr}}</a></li>
</ul>

<hr class="control_tabs" />

<div id="config-source" style="display: none;">
  <h2>Paramètres par défaut du serveur FTP pour HL7 v.2</h2>

  <table class="form">  
    <tr>
      <th class="category">
        {{tr}}config-exchange-source{{/tr}}
      </th>
    </tr>
    <tr>
      <td> {{mb_include module=system template=inc_config_exchange_source source=$hl7v2_source}} </td>
    </tr>
  </table>
</div>

