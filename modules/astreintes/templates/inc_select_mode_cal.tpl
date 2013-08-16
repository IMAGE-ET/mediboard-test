{{*
  * select the view mode
  *  
  * @category Astreintes
  * @package  Mediboard
  * @author   SARL OpenXtrem <dev@openxtrem.com>
  * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
  * @version  SVN: $Id:$ 
  * @link     http://www.mediboard.org
*}}

<script>
  Main.add(function() {
    var form = getForm("filterPlanningAstreinte");
    window.calendar_planning = Calendar.regField(form.date);
  });
</script>

<form action="" method="get" name="filterPlanningAstreinte">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="vw_astreinte_cal" />
  <div style="text-align: center;">

    <a class="button notext left" href="?m={{$m}}&amp;date={{$prev}}&amp;mode={{$mode}}">&lt;&lt;&lt;</a>
    <input type="hidden" name="date" value="{{$date}}" class="date" onchange="this.form.submit()"/>
    <a class="button notext right" href="?m={{$m}}&amp;date={{$next}}&amp;mode={{$mode}}">&gt;&gt;&gt;</a>
    <br/>
    Mode :
    <label>
      <input type="radio" name="mode" value="day" {{if $mode == "day"}}checked="checked" {{/if}} onclick="submit(this)"/>
      {{tr}}day{{/tr}}
    </label>

    <label>
      <input type="radio" name="mode" value="week" {{if $mode == "week"}}checked="checked" {{/if}} onclick="submit(this)"/>
      {{tr}}week{{/tr}}
    </label>

    <label>
      <input type="radio" name="mode" value="month" {{if $mode == "month"}}checked="checked" {{/if}} onclick="submit(this)"/>
      {{tr}}month{{/tr}}
    </label>
  </div>
</form>