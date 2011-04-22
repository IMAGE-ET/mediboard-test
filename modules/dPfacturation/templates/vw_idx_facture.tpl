{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPfacturation
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<script type="text/javascript">
showFacture = function(facture_id){
  var url = new Url("dPfacturation", "ajax_edit_facture");//nom du module, nom du script php qui sera executé
  url.addParam("facture_id", facture_id);
  url.requestUpdate("vw_facture");
}

showListFacture = function(){
	  var url = new Url("dPfacturation", "ajax_list_facture");//nom du module, nom du script php qui sera executé
	  url.requestUpdate("vw_list_facture");
	}

Main.add(function(){
	{{*showFacture({{$facture_id}});*}}
	showListFacture();
})
</script>


{{mb_script module="system" script="object_selector"}}

<button type="button" class="new" onclick="showFacture('0')">{{tr}}CFacture-title-create{{/tr}}</button>
    
<table class="main">
	<tr>
		<td style="width: 40%" id="vw_list_facture">
		</td>
  	<td style="width: 40%" id="vw_facture">
    </td>   
	</tr>
</table>

 
