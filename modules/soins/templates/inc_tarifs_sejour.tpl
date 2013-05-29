{{*
 * $Id$
 *  
 * @category soins
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

<form name="selectTarif" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: window.tabLoaders.Actes.curry({{$sejour->_id}}, {{$sejour->praticien_id}}, '')});">
  {{mb_class object=$sejour}}
  {{mb_key object=$sejour}}
  <input type="hidden" name="_bind_tarif" value="1"/>
  <input type="hidden" name="_delete_actes" value="0"/>
  <input type="hidden" name="_datetime" value="{{$sejour->_datetime}}">

  <table class="form">
    <tr>
      <th><label for="_tarif_id">Tarif</label></th>
      <td>
        <select name="_tarif_id" class="str" onchange="this.form.onsubmit();">
          <option value="" selected="selected">&mdash; {{tr}}Choose{{/tr}}</option>
          {{if $tarifs.user|@count}}
            <optgroup label="Tarifs praticien">
              {{foreach from=$tarifs.user item=_tarif}}
                <option value="{{$_tarif->_id}}" {{if $_tarif->_precode_ready}}class="checked"{{/if}}>{{$_tarif}}</option>
              {{/foreach}}
            </optgroup>
          {{/if}}
          {{if $tarifs.func|@count}}
            <optgroup label="Tarifs cabinet">
              {{foreach from=$tarifs.func item=_tarif}}
                <option value="{{$_tarif->_id}}" {{if $_tarif->_precode_ready}}class="checked"{{/if}}>{{$_tarif}}</option>
              {{/foreach}}
            </optgroup>
          {{/if}}
          {{if $conf.dPcabinet.Tarifs.show_tarifs_etab && $tarifs.group|@count}}
            <optgroup label="Tarifs établissement">
              {{foreach from=$tarifs.group item=_tarif}}
                <option value="{{$_tarif->_id}}" {{if $_tarif->_precode_ready}}class="checked"{{/if}}>{{$_tarif}}</option>
              {{/foreach}}
            </optgroup>
          {{/if}}
        </select>
      </td>
    </tr>
  </table>
</form>