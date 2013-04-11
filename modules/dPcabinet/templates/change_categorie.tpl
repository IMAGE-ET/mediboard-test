{{*
  * Changement de la catégorie d'une consultation
  *  
  * @category dPcabinet
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return onSubmitFormAjax(this, {onComplete: window.location.reload});"> 
  <input type="hidden" name="m" value="dPcabinet" />
  <input type="hidden" name="dosql" value="do_consultation_aed" />
  <input type="hidden" name="del" value="0" />
  {{mb_key object=$consult}}

  <table class="form">
    {{mb_include template="httpreq_view_list_categorie" 
      categorie_id=$consult->categorie_id 
      categories=$categories
      listCat=$listCat}}
    <tr>
      <td class="button" colspan="2">
        <button class="save" type="submit">{{tr}}Save{{/tr}}</button>
      </td>
    </tr>
  </table>

</form>
