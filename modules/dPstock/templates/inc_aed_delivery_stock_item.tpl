{{* $Id$ *}}

{{*
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

<tr>
  <td>{{$stock->_view}}</td>
  <td id="stock-{{$stock->_id}}-bargraph">{{include file="inc_bargraph.tpl"}}</td>
  <td>
    {{if $ajax}}
    <script type="text/javascript">
      prepareForm(document.forms['form-delivery-stock-{{$stock->_id}}']);
    </script>
    {{/if}}
    <form name="form-delivery-stock-{{$stock->_id}}" action="?" method="post">
      <input type="hidden" name="m" value="{{$m}}" />
      <input type="hidden" name="tab" value="{{$tab}}" />
      <input type="hidden" name="dosql" value="do_delivery_aed" />
      <input type="hidden" name="del" value="0" />
      <input type="hidden" name="stock_id" value="{{$stock->_id}}" />
      <input type="hidden" name="service_id" value="" />
      <input type="hidden" name="date_dispensation" value="now" />
      <input type="hidden" name="_deliver" value="1" />
      
      {{assign var=id value=$stock->_id}} 
      {{mb_field object=$stock field=quantity form="form-delivery-stock-$id" increment=1 size=3 value=1}}
      
      <button type="button" class="remove" onclick="deliver(this.form, 1);">Sortie</button>
      <button type="button" class="add" onclick="deliver(this.form, -1);">Retour</button>

      <input type="text" name="code" value="" />
    </form>
  </td>
</tr>
