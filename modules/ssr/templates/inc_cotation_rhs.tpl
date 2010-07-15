{{* $Id: vw_aed_rpu.tpl 7951 2010-02-01 10:44:08Z lryo $ *}}

{{*
  * @package Mediboard
  * @subpackage ssr
  * @version $Revision: 7951 $
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

{{if $bilan->_id && !$bilan->planification}} 
<div class="small-info">
  {{tr}}CBilanSSR-msg-cotation-off{{/tr}}
  <br />
  {{tr}}CBilanSSR-msg-planification-cf{{/tr}}
</div>
{{else}}

<table class="main">
  <tr>
    <td style="width: 0.1%">
    
<script type="text/javascript">
Main.add(Control.Tabs.create.curry('tabs-rhss', true));
</script>

<ul id="tabs-rhss" class="control_tabs_vertical" style="width: 14em;">
  {{foreach from=$rhss item=_rhs}}
  <li>
  	<a href="#cotation-{{if $_rhs->_id}}{{$_rhs->_id}}{{else}}{{$_rhs->date_monday}}{{/if}}" 
      {{if !$_rhs->_id}}class="empty"{{/if}}
      {{if !$_rhs->_in_bounds}}class="wrong"{{/if}}
			>
  		{{$_rhs}}
    <br />
		<small>
      du {{mb_value object=$_rhs field=date_monday}}
      au {{mb_value object=$_rhs field=_date_sunday}}
    </small>
		</a>
	</li>
  {{/foreach}}
</ul>

    </td>
		<td>
			
{{foreach from=$rhss item=_rhs}}
<div id="cotation-{{if $_rhs->_id}}{{$_rhs->_id}}{{else}}{{$_rhs->date_monday}}{{/if}}" style="display: none;">
  {{mb_include template=inc_edit_rhs rhs=$_rhs}}
</div>
{{/foreach}}

    </td>
  </tr>
</table>

{{/if}}