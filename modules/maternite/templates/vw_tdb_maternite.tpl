{{*
 * $Id$
 *
 * Tableau de bord de la maternité
 *
 * @package    Mediboard
 * @subpackage Maternite
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 *}}

{{mb_script module="cabinet" script="edit_consultation"}}
{{mb_script module="planningOp" script="operation"}}

{{mb_script module="soins" script="plan_soins"}}

{{if "dPprescription"|module_active}}
  {{mb_script module="prescription" script="prescription"}}
  {{mb_script module="prescription" script="element_selector"}}
{{/if}}

{{if "dPmedicament"|module_active}}
  {{mb_script module="medicament" script="medicament_selector"}}
  {{mb_script module="medicament" script="equivalent_selector"}}
{{/if}}

{{if "messagerie"|module_active}}
  {{mb_script module="messagerie" script="UserEmail"}}
{{/if}}

<script type="text/javascript">

  Consultation.useModal();
  Operation.useModal();

  initListGrossesses = function() {
    var url = new Url("maternite", "ajax_tdb_grossesses");
    url.addParam("date", '{{$date_tdb}}');
    url.periodicalUpdate("grossesses", { frequency: 120 } );
  };

  initListConsultations = function() {
    var url = new Url("maternite", "ajax_tdb_consultations");
    url.addParam("date", '{{$date_tdb}}');
    url.periodicalUpdate("consultations", { frequency: 120 } );
  };

  initListHospitalisations = function() {
    var url = new Url("maternite", "ajax_tdb_hospitalisations");
    url.addParam("date", '{{$date_tdb}}');
    url.periodicalUpdate("hospitalisations", { frequency: 120 } );
  };

  initListAccouchements = function() {
    var url = new Url("maternite", "ajax_tdb_accouchements");
    url.addParam("date", '{{$date_tdb}}');
    url.periodicalUpdate("accouchements", { frequency: 120 } );
  };

  Main.add(function () {
    initListGrossesses();
    initListConsultations();
    initListHospitalisations();
    initListAccouchements();
    ViewPort.SetAvlHeight("grossesses"      , 0.5);
    ViewPort.SetAvlHeight("consultations"   , 0.5);
    ViewPort.SetAvlHeight("hospitalisations", 1.0);
    ViewPort.SetAvlHeight("accouchements"   , 1.0);
    Calendar.regField(getForm("changeDate").date_tdb, null, {noView: true});
  });

</script>

<table class="main">
  <tr>
    <th colspan="2">
      <a id="vw_day_date_a" href="?m={{$m}}&amp;tab={{$tab}}&amp;date_tdb={{$prec}}">&lt;&lt;&lt;</a>
      <form name="changeDate" action="?m={{$m}}" method="get">
        <input type="hidden" name="m" value="{{$m}}" />
        <input type="hidden" name="tab" value="{{$tab}}" />
        {{$date_tdb|date_format:$conf.longdate}}
        <input type="hidden" name="date_tdb" class="date" value="{{$date_tdb}}" onchange="this.form.submit()" />
      </form>
      <a href="?m={{$m}}&amp;tab={{$tab}}&amp;date_tdb={{$suiv}}">&gt;&gt;&gt;</a>
    </th>
  </tr>

  <tbody class="viewported">
  <tr>
    <!--  Grossesses en cours -->
    <td id="vw_grossesses_td" class="viewport" style="width: 50%">
      <div id="grossesses"></div>
    </td>

    <!-- Consultations -->
    <td id="vw_day_consultations_td" class="viewport" style="width: 50%">
      <div id="consultations"></div>
    </td>
  </tr>

  <tr>
    <!-- Hospitalisations -->
    <td id="vw_day_hospitalisations_td" class="viewport" style="width: 50%">
      <div id="hospitalisations" style="overflow: auto"></div>
    </td>

    <!-- Accouchements -->
    <td id="vw_day_accouchements_td" class="viewport" style="width: 50%">
      <div id="accouchements" style="overflow: auto"></div>
    </td>
  </tr>
  </tbody>
</table>