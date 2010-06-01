{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<hr />
<table class="form">
 <tr>
   <th class="title">Duplication d'élément</th>
 </tr>
 <tr>
   <td>
     <form name="duplicationElts" action="?m={{$m}}" method="post">
       <input type="hidden" name="m" value="dPprescription" />
       <input type="hidden" name="dosql" value="do_duplicate_cat_elements_aed" />
       <input type="hidden" name="category_prescription_id" value="{{$category_prescription->_id}}" />
       Dupliquer les elements de <strong>{{$category_prescription->_view}}</strong>
			 <br/> vers
       <select name="category_dest_id">
         <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
         {{foreach from=$categories key=chapitre item=_categories}}
         {{if $categories}}
         <optgroup label="{{tr}}CCategoryPrescription.chapitre.{{$chapitre}}{{/tr}}">
           {{foreach from=$_categories item=_category}}
           <option value="{{$_category->_id}}" {{if $_category->_id == $category_prescription->_id}}disabled="disabled"{{/if}}>{{$_category->nom}}</option>
           {{/foreach}}
         </optgroup>
         {{/if}}
         {{/foreach}}
       </select>
       <button type="submit" class="submit" onclick="if(this.form.category_dest_id.value) { this.form.submit(); }">Valider</button>
      </form>
   </td>
 </tr>
</table>