{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<a href="?m={{$m}}&amp;tab={{$tab}}&amp;category_prescription_id=0&amp;element_prescription_id=0" class="button new">
  Créer une catégorie
</a>
<ul class="control_tabs" id="categories_tab">
{{foreach from=$categories key=chapitre item=_categories}}
   <li>
    <a href="#div_{{$chapitre}}">
      {{tr}}CCategoryPrescription.chapitre.{{$chapitre}}{{/tr}} 
      <small>({{$_categories|@count}} - {{$countElements.$chapitre}})</small>
    </a>
  </li>
{{/foreach}}
</ul>
<hr class="control_tabs" />

{{foreach from=$categories key=chapitre item=_categories}}
  <div id="div_{{$chapitre}}" style="display: none;">
    <table class="tbl" ">
      <tr>
        <th>{{mb_label class=CCategoryPrescription field=nom}}</th>
        <th>{{mb_label class=CCategoryPrescription field=group_id}}</th>
        <th></th>
      </tr>
      {{foreach from=$_categories item=_cat}}
        <tr {{if $category->_id == $_cat->_id}}class="selected"{{/if}} >
          <td>
            <a href="?m={{$m}}&amp;tab={{$tab}}&amp;category_prescription_id={{$_cat->_id}}&amp;element_prescription_id=0">
              {{$_cat->nom}} ({{$_cat->_count_elements_prescription}})
            </a>
          </td>
          <td>
            {{if $_cat->group_id}}
              {{$_cat->_ref_group->_view}}
            {{else}}
              Tous
            {{/if}}
          </td>
          <td style="width: 1em; {{if $_cat->color}}background-color: #{{$_cat->color}}{{/if}}">
            
          </td>
        </tr>
      {{/foreach}}
    </table>
  </div>
  <script type="text/javascript">ViewPort.SetAvlHeight('div_{{$chapitre}}', 0.30);</script>
{{/foreach}}
