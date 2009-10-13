{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPplanningOp" script="ccam_selector"}}

{{if $prat->_id}}

<script type="text/javascript">
var graphs = {{$graphs|@json}};
Main.add(function(){
	graphs.each(function(g, i){
		Flotr.draw($('graph-'+i), g.series, g.options);
	});
});
</script>

<form name="filters" action="?" method="get" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="dPboard" />
<input type="hidden" name="_chir" value="{{$prat->_id}}" />
<input type="hidden" name="_class_name" value="" />

<table class="main form">

  <tr>
    <th colspan="4" class="category">Statistiques cliniques</th>
  </tr>

  <tr>
    <td>{{mb_label object=$filterSejour field="_date_min_stat"}}</td>
    <td class="date">{{mb_field object=$filterSejour field="_date_min_stat" form="filters" register=true canNull="false"}} </td>
    <td>{{mb_label object=$filterSejour field="_date_max_stat"}}</td>
    <td class="date">{{mb_field object=$filterSejour field="_date_max_stat" form="filters" register=true canNull="false"}} </td>
  </tr>

  <tr>
    <td>{{mb_label object=$filterSejour field="type"}}</td>
    <td>
      <select name="type">
        <option value="">&mdash; Tous les types d'hospi</option>
        <option value="1" {{if $filterSejour->type == "1"}}selected="selected"{{/if}}>Hospi complètes + ambu</option>
        {{foreach from=$filterSejour->_specs.type->_locales key=key_hospi item=curr_hospi}}
        <option value="{{$key_hospi}}" {{if $key_hospi == $filterSejour->type}}selected="selected"{{/if}}>
          {{$curr_hospi}}
        </option>
        {{/foreach}}
      </select>
    </td>
    <td>{{mb_label object=$filterOperation field="codes_ccam"}}</td>
    <td>
      {{mb_field object=$filterOperation field="codes_ccam" canNull="true" size="20"}}
      <button class="search notext" type="button" onclick="CCAMSelector.init()">{{tr}}Search{{/tr}}</button>   
      <script type="text/javascript">
        CCAMSelector.init = function(){
          this.sForm = "filters";
          this.sView = "codes_ccam";
          this.sChir = "_chir";
          this.sClass = "_class_name";
          this.pop();
        }
      </script>
    </td>
  </tr>

  <tr>
    <td colspan="4" class="button">
      <button type="submit" class="search">Afficher</button>
    </td>
  </tr>
  
</table>

</form>

{{foreach from=$graphs item=graph key=key}}
	<div style="width: 480px; height: 350px; float: left; margin: 1em;" id="graph-{{$key}}"></div>
{{/foreach}}

{{/if}}