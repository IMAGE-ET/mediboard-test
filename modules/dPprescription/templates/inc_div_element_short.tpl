{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{if is_array($prescription->_ref_lines_elements_comments) && array_key_exists($element, $prescription->_ref_lines_elements_comments)}}
  {{assign var=lines value=$prescription->_ref_lines_elements_comments.$element}}
  <!-- Parcours des elements de type $element -->
  {{foreach from=$lines item=lines_cat key=category_id}}
	  {{assign var=category value=$categories.$element.$category_id}}
	  <!-- Elements d'une categorie-->
	 
    <table class="tbl">
    <tr>
	   <th class="title" colspan="9">{{$category->_view}}</th>
	  </tr>
	  {{foreach from=$lines_cat.element item=line_element}}
       {{include file="../../dPprescription/templates/inc_vw_line_pack.tpl" line=$line_element}}			
	  {{/foreach}}
	  
	  </table>
	  {{if array_key_exists("comment", $lines_cat)}}
	  <!-- Commentaires d'une categorie -->
	  <table class="tbl">
  	  {{if $lines_cat.comment|@count}}
  	  <tr>
  	    <th colspan="9" class="element">Commentaires</th>
  	  </tr>
  	  {{/if}}
  	  {{foreach from=$lines_cat.comment item=line_comment}}
        {{include file="../../dPprescription/templates/inc_vw_line_pack.tpl" line=$line_comment}}
  	  {{/foreach}}
	  </table>
	  {{/if}}
  {{/foreach}}
{{else}}
  <div class="small-info"> 
     Il n'y a aucun élément de type "{{tr}}CCategoryPrescription.chapitre.{{$element}}{{/tr}}" dans cette prescription.
  </div>
{{/if}}