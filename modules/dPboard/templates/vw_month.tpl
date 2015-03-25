{{*
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
*}}

{{mb_script module=dPbloc script=edit_planning}}

<script>
  refreshList = function() {
    var form = getForm('selectFrm');
    var url = new Url("board", "ajax_vw_month");
    if ($V(form.chir_id)) {
      url.addParam("praticien_id", $V(form.chir_id));
    }
    if ($V(form.function_id)) {
      url.addParam("function_id", $V(form.function_id));
    }
    url.addParam("date", $V(form.date));
    url.requestUpdate("month_calendar_board");
  };

  Main.add(function() {
    var oform = getForm("selectFrm");
    DateFormat.MONTH_NAMES = Control.DatePicker.Language['fr'].months;
    Calendar.regField(oform.date, null, {dateFormat: "MMM yyyy"});
    refreshList();
  });
</script>

<form name="selectFrm" action="?" method="get" onsubmit="return false">
  <input type="hidden" name="m" value="{{$m}}" />

  {{if $listPrat|@count}}
    <label for="chir_id">Praticien</label>
    <select name="chir_id" style="width: 20em;" onchange="if (this.form.function_id) {this.form.function_id.selectedIndex=0;} refreshList();">
      <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
      {{foreach from=$listPrat item=curr_prat}}
        <option class="mediuser" style="border-color: #{{$curr_prat->_ref_function->color}};"
          value="{{$curr_prat->user_id}}" {{if ($prat->_id == $curr_prat->user_id) && !$function_id}} selected="selected" {{/if}}>
          {{$curr_prat->_view}}
        </option>
      {{/foreach}}
    </select>
  {{/if}}

  {{if $listFunc|@count}}
    <label for="function_id" title="Filtrer les protocoles d'une fonction">Fonction</label>
      <select name="function_id" style="width: 20em;" onchange="if (this.form.chir_id) { this.form.chir_id.selectedIndex=0; } refreshList();">
        <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
        {{foreach from=$listFunc item=curr_function}}
          <option class="mediuser" style="border-color: #{{$curr_function->color}};"
                  value="{{$curr_function->_id}}" {{if $curr_function->_id == $function_id}}selected="selected"{{/if}}>
            {{$curr_function->_view}}
          </option>
        {{/foreach}}
      </select>
  {{/if}}

    <label for="date">Mois</label>
    <input type="hidden" name="date" class="date" value="{{$date}}" onchange="refreshList();" />

    <button type="button" class="change notext" onclick="refreshList();">{{tr}}Refresh{{/tr}}</button>
</form>

<div id="month_calendar_board" style="width: 100%;">
</div>

<script>
  ViewPort.SetAvlHeight("month_calendar_board", 1);
</script>