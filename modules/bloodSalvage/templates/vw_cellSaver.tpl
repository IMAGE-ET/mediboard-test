{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<table class="main">
<tr>
  <td class="halfPane">
    <table class="tbl">
      <tr>
        <th class="title" colspan="4">{{tr}}CCellSaver{{/tr}}</th>
      </tr>
      <tr>
        <th>{{tr}}CCellSaver.marque{{/tr}}</th>
        <th>{{tr}}CCellSaver.modele{{/tr}}</th>
      </tr>
      {{foreach from=$cell_saver_list key=id item=cs}}
      <tr>
      <td><a href="?m={{$m}}&amp;tab=vw_cellSaver&amp;cell_saver_id={{$cs->_id}}" title="Voir ou modifier le cell saver">
      {{mb_value object=$cs field=marque}}
      </a>
      </td>
      <td><a href="?m={{$m}}&amp;tab=vw_cellSaver&amp;cell_saver_id={{$cs->_id}}" title="Voir ou modifier le cell saver">
      {{mb_value object=$cs field=modele}}
      </a>
      </td>
      </tr> 
      {{foreachelse}}
      <tr>
      <td colspan="3">
      <i>{{tr}}CCellSaver.none{{/tr}}</i>
      </td>
      </tr>
      {{/foreach}}
    </table>
  </td>
  <td class="halfPane">
	  <a class="button new" href="?m={{$m}}&amp;tab=vw_cellSaver&amp;cell_saver_id=0">{{tr}}CCellSaver.create{{/tr}}</a>
	  <form name="edit_cellSaver" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">
		  <input type="hidden" name="dosql" value="do_cellSaver_aed" />
		  <input type="hidden" name="cell_saver_id" value="{{$cell_saver->_id}}" />
		  <input type="hidden" name="del" value="0" />
		  <table class="form">
		    <tr>
		      {{if $cell_saver->_id}}
		      <th class="title modify" colspan="2">{{tr}}CCellSaver.modify{{/tr}} {{$cell_saver->_view}}</th>
		      {{else}}
		      <th class="title" colspan="2">{{tr}}CCellSaver.create{{/tr}}</th>
		      {{/if}}
		    </tr>   
		    <tr>
		      <th>{{mb_label object=$cell_saver field="marque"}}</th>
		      <td>{{mb_field object=$cell_saver size=32 field="marque"}}</td>
		    </tr>
		    <tr>
		      <th>{{mb_label object=$cell_saver field="modele"}}</th>
		      <td>{{mb_field object=$cell_saver size=32 field="modele"}}</td>
		    </tr>
        <tr>
		      <td class="button" colspan="4">
		        {{if $cell_saver->_id}}
		        <button class="modify" type="submit">{{tr}}Modify{{/tr}}</button>
		        <button type="button" class="trash" onclick="confirmDeletion(this.form,{typeName:'',objName:'{{$cell_saver->_view|smarty:nodefaults|JSAttribute}}'})">
		          {{tr}}Delete{{/tr}}
		        </button>
		        {{else}}
		        <button class="submit" type="submit">{{tr}}Create{{/tr}}</button>
		        {{/if}}
		      </td>
		    </tr>
		  </table>
	  </form>
  </td>
</tr>
</table>