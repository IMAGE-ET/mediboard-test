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
{{mb_script module="dPpatients" script="pat_selector"}}
{{mb_script module="planningOp" script="operation"}}
{{mb_script module="planningOp" script="protocole_selector"}}
{{mb_script module="planningOp" script="cim10_selector"}}
{{mb_script module="planningOp" script="ccam_selector"}}
{{mb_script module="planningOp" script="plage_selector"}}
{{mb_script module="soins" script="plan_soins"}}
{{mb_script module="maternite" script="tdb"}}
{{mb_script module="maternite" script="grossesse"}}

<style>
  .gender_f, .gender_m {
    padding-left:3px;
    list-style: none;
  }

  .gender_m {
    border-left:solid 4px #6aa3ff;
  }

  .gender_f {
    border-left:solid 4px #ff9485;
  }
</style>

<script>
  Consultation.useModal();
  Operation.useModal();
  Grossesse.afterEditGrossesse = function(_id) {
    Control.Modal.close();
    Tdb.editGrossesse(_id);
  };

  Main.add(function () {
    Tdb.views.date = '{{$date_tdb}}';
    ViewPort.SetAvlHeight("grossesses"      , 0.5);
    ViewPort.SetAvlHeight("consultations"   , 0.5);
    ViewPort.SetAvlHeight("hospitalisations", 1.0);
    ViewPort.SetAvlHeight("accouchements"   , 1.0);
    Tdb.views.initListGrossesses();
    Calendar.regField(getForm("changeDate").date_tdb, null, {noView: true});
  });
</script>

<table class="main">
  <tr>
    <th colspan="2">
      <div style="float:left;">
        <input type="text" name="fast_search" placeholder="recherche rapide" value="" id="_seek_patient" onkeyup="Tdb.views.filterByText();" onchange="Tdb.views.filterByText()"><button class="cleanup notext" onclick="$V('_seek_patient', '', true);"></button>
      </div>

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