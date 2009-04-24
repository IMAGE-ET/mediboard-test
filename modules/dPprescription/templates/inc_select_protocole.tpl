{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

 Protocoles de <strong>{{$praticien->_view}}</strong>
 <select name="pack_protocole_id" style="width: 80px;">
  <option value="">&mdash; Sélection</option>
  {{if $protocoles_praticien|@count || $packs_praticien|@count}}
   <optgroup label="Praticien">
    {{foreach from=$protocoles_praticien item=_protocole_praticien}}
    <option value="prot-{{$_protocole_praticien->_id}}">{{$_protocole_praticien->libelle}}</option>
    {{/foreach}}
   {{foreach from=$packs_praticien item=_pack_praticien}}
   <option value="pack-{{$_pack_praticien->_id}}" style="font-weight: bold">{{$_pack_praticien->_view}}</option>
   {{/foreach}}
   </optgroup>
  {{/if}}
  {{if $protocoles_function|@count || $packs_function|@count}}
    <optgroup label="Cabinet">
      {{foreach from=$protocoles_function item=_protocole_function}}
      <option value="prot-{{$_protocole_function->_id}}">{{$_protocole_function->libelle}}</option>
      {{/foreach}}
      {{foreach from=$packs_function item=_pack_function}}
     <option value="pack-{{$_pack_function->_id}}" style="font-weight: bold">{{$_pack_function->_view}}</option>
     {{/foreach}}
    </optgroup>
  {{/if}}
</select>