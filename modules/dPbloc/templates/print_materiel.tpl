{{* $Id$ *}}

{{*
  * @package Mediboard
  * @subpackage dPbloc
  * @version $Revision$
  * @author SARL OpenXtrem
  * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
  *}}

<h1>
	 {{mb_label class=COperation field=materiel}} 
	 du {{mb_value object=$filter field=_date_min}} 
	 au {{mb_value object=$filter field=_date_max}} 
</h1>

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-commande_mat', true);
});
</script>

<ul id="tabs-commande_mat" class="control_tabs">
  {{foreach from=$operations key=commande_mat item=_operations}}
  <li>
    {{assign var=op_count value=$_operations|@count}}
  	<a href="#commande_mat_{{$commande_mat}}" {{if !$op_count}}class="empty"{{/if}}>
			{{tr}}COperation.commande_mat.{{$commande_mat}}{{/tr}} 
      <small>({{$op_count}})</small>
		</a>
	</li>
  {{/foreach}}
</ul>

<hr class="control_tabs" />

{{foreach from=$operations key=commande_mat item=_operations}}
{{mb_include template=inc_list_materiel}}
{{/foreach}}
