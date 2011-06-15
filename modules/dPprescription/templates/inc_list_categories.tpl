{{* $Id: $ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<a href="#1" onclick="onSelectCategory('0');" class="button new">
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

<script type="text/javascript">

Main.add( function(){
  var tab = Control.Tabs.create('categories_tab', true);
  tab.setActiveTab("div_{{$category_prescription->chapitre}}");
});

</script>

{{foreach from=$categories key=chapitre item=_categories}}
  <div id="div_{{$chapitre}}" style="display: none;">
    <table class="tbl">
      <tr>
        <th>{{mb_label class=CCategoryPrescription field=nom}}</th>
        <th>{{mb_label class=CCategoryPrescription field=group_id}}</th>
				<th class="narrow">Eléments</th>
        <th></th>
      </tr>
      {{foreach from=$_categories item=_cat}}
        <tr {{if $category_prescription->_id == $_cat->_id}}class="selected"{{/if}} >
          <td>
            <a href="#1" onclick="onSelectCategory('{{$_cat->_id}}', this.up('tr'));">
              {{$_cat->nom}}
            </a>
          </td>
          <td>
            {{if $_cat->group_id}}
              {{$_cat->_ref_group->_view}}
            {{else}}
              Tous
            {{/if}}
          </td>
					<td style="text-align: right;">{{$_cat->_count_elements_prescription}}</td>
          <td style="width: 1em; {{if $_cat->color}}background-color: #{{$_cat->color}}{{/if}}">
          </td>
        </tr>
      {{/foreach}}
    </table>
  </div>
  <script type="text/javascript">ViewPort.SetAvlHeight('div_{{$chapitre}}', 0.30);</script>
{{/foreach}}