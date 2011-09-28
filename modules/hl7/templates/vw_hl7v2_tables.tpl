{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  function addHL7v2TableDescription() {
	  var url = new Url("hl7", "ajax_add_hl7v2_table_description");
	  url.requestModal(400, 200);
  }
  
  function loadEntries(table_number, element) {
	  if (element)
	   element.up('tr').addUniqueClassName('selected');
	  var url = new Url("hl7", "ajax_refresh_table_entries");
	  url.addParam("table_number", table_number);
	  url.requestUpdate("entries");
  }

  function loadTables() {
    var url = new Url("hl7", "ajax_refresh_tables");
    url.requestUpdate("tables");
  }
  
  function changePage(page) {
	  $V(getForm('listFilter').page,page);
	}

  Main.add(function(){
    Control.Tabs.create("tables-tab", false, {
        afterChange: function(newContainer){
    	    loadEntries(newContainer.get('number'));
        }
    });
  });
</script>

<table class="main">
  <tr>
    <td style="width: 30%">  
      <button class="new" onclick="addHL7v2TableDescription()"> {{tr}}CHL7v2TableDescription-title-create{{/tr}} </button>
    </td>
    <td></td>
  </tr>
  <tr>
    <td id="tables">
      {{mb_include template=inc_list_hl7v2_tables}}
    </td>
    <td id="entries">
      {{mb_include template=inc_hl7v2_table_entries}}
    </td>
  </tr>
</table>