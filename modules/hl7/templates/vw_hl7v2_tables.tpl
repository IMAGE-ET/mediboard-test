{{* $Id: configure.tpl 6341 2009-05-21 11:52:48Z mytto $ *}}

{{*
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 6341 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script>
  function loadEntries(table_number, element) {
	  if (element)
	   element.up('tr').addUniqueClassName('selected');
	  var url = new Url("hl7", "ajax_refresh_table_entries");
	  url.addParam("table_number", table_number);
	  url.requestUpdate("entries");
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
    <td style="width: 40%">   
      <form name="listFilter" action="?m={{$m}}" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        <input type="hidden" name="page" value="{{$page}}" onchange="this.form.submit()"/>
           
        {{if $total_tables != 0}}
          {{mb_include module=system template=inc_pagination total=$total_tables current=$page change_page='changePage'}}
        {{/if}}
      </form>
      
       {{mb_include template=inc_list_hl7v2_tables}}
    </td>
    <td id="entries">
      {{mb_include template=inc_hl7v2_table_entries}}
    </td>
  </tr>
</table>