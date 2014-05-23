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


{{mb_default var=view_full_history value=0}}
{{mb_default var=total_aat value=0}}

{{if $view_full_history}}
  <script type="text/javascript">
    changePage = function(page) {
      var url = new Url('ameli', 'ajax_get_aat_history');
      url.addParam('patient_id', {{$patient_id}});
      url.addParam('page', page);
      url.addParam('header', 0);
      url.requestUpdate('aat_history');
    }
  </script>
{{/if}}

<table class="tbl">
  <tr>
    <th class="title" colspan="4">
      {{if !$view_full_history}}
        <button type="button" class="search notext" style="float: right;" title="{{tr}}CAvisArretTravail-get_full_history{{/tr}}" onclick="getFullAATHistory({{$arret_travail->patient_id}});"></button>
      {{/if}}
      {{tr}}CAvisArretTravail-history{{/tr}}
    </th>
  </tr>
  {{if $view_full_history}}
  <tbody id="aat_history">
  {{/if}}
  {{mb_include module=ameli template=inc_aat_history}}
  {{if $view_full_history}}
    </tbody>
  {{/if}}
</table>