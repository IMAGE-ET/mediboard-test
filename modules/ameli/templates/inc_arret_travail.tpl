{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<script type="text/javascript">
  editArretTravail = function(consult_id) {
    var url = new Url('ameli', 'ajax_edit_arret_travail');
    url.addParam('consult_id', consult_id);
    url.requestModal(null, null, {onClose: function() {
      loadArretTravail();
    }});
  };
</script>

<fieldset>
  <legend>{{tr}}CAvisArretTravail{{/tr}}</legend>

  {{if $arret_travail->_id}}
    <table class="form">
      <tr>
        <th>{{mb_label object=$arret_travail field=motif_id}}</th>
        <td>{{mb_value object=$arret_travail field=libelle_motif}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$arret_travail field=type}}</th>
        <td>{{mb_value object=$arret_travail field=type}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$arret_travail field=debut}}</th>
        <td>{{mb_value object=$arret_travail field=debut}}</td>
      </tr>
      <tr>
        <th>{{mb_label object=$arret_travail field=fin}}</th>
        <td>{{mb_value object=$arret_travail field=fin}}</td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center;">
          <button type="button" class="edit" onclick="editArretTravail('{{$consult_id}}');">{{tr}}CAvisArretTravail-action-modify{{/tr}}</button>
        </td>
      </tr>
    </table>
  {{else}}
    <button type="button" class="new" onclick="editArretTravail('{{$consult_id}}');">{{tr}}CAvisArretTravail-action-create{{/tr}}</button>
  {{/if}}
</fieldset>
