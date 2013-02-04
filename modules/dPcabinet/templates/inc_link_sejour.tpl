{{*
  * Relier une consultation à un séjour
  *  
  * @category dPcabinet
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<script type="">
  mapSejour = function(sejour_id) {
    var form = getForm('editFrm');
    $V(form.sejour_id, sejour_id);
    form.submit();
  }
</script>

<table>
  {{foreach from=$sejours item=_sejour}}
    <tr>
      <td class="narrow">
        <button type="button" class="big tick" onclick="mapSejour({{$_sejour->_id}})">
          {{$_sejour}} <br />
          {{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_sejour->_ref_praticien}}
        </button>
      </td>
    </tr>
  {{/foreach}}
</table>
