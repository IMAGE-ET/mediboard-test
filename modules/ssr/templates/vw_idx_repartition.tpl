{{* $Id:$ *}}

{{*
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: 7948 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">

showLegende = function(){
  var url = new Url();
	url.setModuleAction("ssr", "vw_legende");
	url.popup(300, 200, "Legende");
}


</script>

{{mb_include_script module=ssr script=repartition}}

<form name="Edit-CBilanSSR" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="ssr" />
<input type="hidden" name="dosql" value="do_bilan_ssr_aed" />
<input type="hidden" name="del" value="0" />

{{mb_key object=$bilan}}
{{mb_field object=$bilan field=sejour_id hidden=1}}
{{mb_field object=$bilan field=technicien_id hidden=1}}

</form>

<table class="main">
  <tr>
		<th colspan="2">
			<big>Planning du {{$date|date_format:$conf.date}}</big>
			<form name="selDate" action="?" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="{{$actionType}}" value="vw_idx_repartition" />
				<input type="hidden" name="dialog" value="{{$dialog}}" />
				<input type="hidden" name="readonly" value="{{$readonly}}" />

        <input type="hidden" name="date" class="date" value="{{$date}}" onchange="this.form.submit()" />
        <script type="text/javascript">
				Main.add(Calendar.regField.curry(getForm("selDate").date, null, {noView: true}));
        </script>

			</form>

			<button type="button" class="search" style="float: right;" onclick="showLegende();">Legende</button>
		</th>
	</tr>
	
  <tr>
    <td>
    	<div id="plateaux">
        <script type="text/javascript">
        ViewPort.SetAvlHeight('plateaux', 1);
			  // Debugage du scroll de la div de la liste des prescriptions
			  Position.includeScrollOffsets = true;
			  Event.observe('plateaux', 'scroll', function(event) { Position.prepare(); });
        </script>

	      {{if $conf.ssr.repartition.show_tabs}} 
	         
	      <script type="text/javascript">
	      Main.add(Control.Tabs.create.curry('tabs-plateaux', true));
	      </script>
	    
	      <ul id="tabs-plateaux" class="control_tabs">
	        {{foreach from=$plateaux item=_plateau}}
	        <li>
	          <a href="#{{$_plateau->_guid}}">
	            {{$_plateau}}
	          </a>
	        </li>
	        {{/foreach}}
	      </ul>
	      
	      <hr class="control_tabs" />
	      {{/if}}
	
	
	      {{foreach from=$plateaux item=_plateau}}
	      {{mb_include template=inc_repartition_plateau plateau=$_plateau}}
	      {{foreachelse}}
	      <div class="small-warning">
	        {{tr}}CGroups-back-plateaux_techniques.empty{{/tr}}
	      </div>
	      {{/foreach}}
    	</div>
    </td>

    <td style="width: 160px;">
		  <div id="non-repartis">
	      <script type="text/javascript">
	      ViewPort.SetAvlHeight('non-repartis', 1);
	      </script>
	      {{mb_include template=inc_sejours_non_affectes}}
		  </div>
    </td>
  </tr>
  
</table>
