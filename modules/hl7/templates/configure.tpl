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
	
	function importHL7v2Tables(){
	  var url = new Url("hl7", "ajax_import_hl7v2_tables");
	  url.requestUpdate("import-log");
	}
</script>

<ul id="tabs-configure" class="control_tabs">
  <li><a href="#config-source">{{tr}}config-hl7v2-source{{/tr}}</a></li>
  <li><a href="#config-hl7v2-tables">{{tr}}config-hl7v2-tables{{/tr}}</a></li>
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

<div id="config-hl7v2-tables" style="display: none;">
  <h2>Paramètres des tables HL7</h2>

  {{mb_include module=system template=configure_dsn dsn=hl7v2}}
 
  <table class="main tbl">
  	<tr>
  		<th class="title" colspan="2">
  			Import des tables
  		</th>
 	  <tr>
 		  <td><button onclick="importHL7v2Tables()" class="change">{{tr}}Import{{/tr}}</button></td>
		  <td id="import-log"></td>
 	  </tr>
  </table>
</div>
