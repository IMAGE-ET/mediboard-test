{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
Main.add(function () {
  Control.Tabs.create('tabs-sejours', true).activeLink.onmouseup();
});
</script>

<ul id="tabs-sejours" class="control_tabs">
	{{foreach from=$counts key=mode item=_count}}
  <li>
  	<a {{if !$_count}} class="empty" {{/if}} href="#board-sejours-{{$mode}}" onmouseup="BoardSejours.updateTab('{{$mode}}');">
  		{{tr}}ssr-board-sejours-{{$mode}}{{/tr}}
			<small>({{$_count}})</small>
		</a>
	</li>
	{{/foreach}}
</ul>

<hr class="control_tabs" />

<label>
  <input name="hide_noevents" type="checkbox" {{if $hide_noevents}} checked="true" {{/if}} onclick="BoardSejours.update(this.checked)" />
  Masquer les séjours sans planification cette semaine
</label>

{{foreach from=$counts key=mode item=_count}}
<div style="display: none;" id="board-sejours-{{$mode}}">
</div>
{{/foreach}}
