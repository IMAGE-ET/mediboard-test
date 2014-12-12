{{*
 * $Id$
 *  
 * @category Planif Séjour
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_default var=type value="souhait"}}

<input type="radio" style="display: none;" value=""
       name="liaisons_j[{{$prestation_id}}][{{$_date}}][{{$type}}][{{$liaison->_id}}][item_{{$type}}_id]"
       onclick="this.up('td').select('.sous_item').each(function(input) {
                 input.checked = false;
               });" />

{{foreach from=$_prestation->_ref_items item=_item}}
  {{if $_item->_refs_sous_items|@count && $type == "souhait"}}
    <fieldset style="display: inline-block;">
      <legend>
        <label>
          <input type="radio"
                 name="liaisons_j[{{$prestation_id}}][{{$_date}}][{{$type}}][{{$liaison->_id}}][item_{{$type}}_id]"
                 style="display: none;"
                 value="{{$_item->_id}}"
                 {{if ($type == "souhait" && $liaison->item_souhait_id == $_item->_id) ||
                      ($type == "realise" && $liaison->item_realise_id == $_item->_id)}}checked{{/if}} />
          {{$_item->nom}}
        </label>
      </legend>
      {{foreach from=$_item->_refs_sous_items item=_sous_item}}
        <label>
          <input type="radio" class="sous_item"
                 name="liaisons_j[{{$prestation_id}}][{{$_date}}][{{$type}}][{{$liaison->_id}}][sous_item_id]"
                 value="{{$_sous_item->_id}}"
                 onclick="switchToNewSousItem(this);"
                 {{if $liaison->sous_item_id == $_sous_item->_id && ($type == "souhait" || $liaison->_ref_item_realise->_id)
                   && (($type == "souhait" && $liaison->_ref_sous_item->item_prestation_id == $liaison->item_souhait_id) ||
                       ($type == "realise" && $liaison->_ref_sous_item->item_prestation_id == $liaison->item_realise_id))}}checked{{/if}} />
          {{$_sous_item->nom}}
        </label>
      {{/foreach}}
    </fieldset>
  {{else}}
    <label>
      <input type="radio"
             name="liaisons_j[{{$prestation_id}}][{{$_date}}][{{$type}}][{{$liaison->_id}}][item_{{$type}}_id]"
             value="{{$_item->_id}}"
             onclick="
               this.up('td').select('.sous_item').each(function(input) {
                 input.checked = false;
               });
             {{if $liaison->_id == "temp"}}
               switchToNew(this);
             {{/if}}
             {{if $type == "souhait" && $_prestation->desire}}
               autoRealiser(this);
             {{/if}}"
             {{if ($type == "souhait" && $liaison->item_souhait_id == $_item->_id) ||
                   ($type == "realise" && $liaison->item_realise_id == $_item->_id)}}checked{{/if}} />
      <span {{if $_item->color}}class="mediuser" style="border-left-color: #{{$_item->color}}"{{/if}}>{{$_item->nom}}</span>
    </label>
  {{/if}}
{{/foreach}}
