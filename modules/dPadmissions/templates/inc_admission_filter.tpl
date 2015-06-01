{{*
 * $Id$
 *  
 * @category admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org*}}

<table class="layout">
  <tr>
    <td>
      <form action="?" name="selType" method="get">
        <input type="hidden" name="date" value="{{$date}}" />
        <input type="hidden" name="filterFunction" value="{{$filterFunction}}" />
        <input type="hidden" name="selAdmis" value="{{$selAdmis}}" />
        <input type="hidden" name="selSaisis" value="{{$selSaisis}}" />
        <input type="hidden" name="order_col" value="{{$order_col}}" />
        <input type="hidden" name="order_way" value="{{$order_way}}" />
        <!-- print -->
        <input type="hidden" name="order_print_way" value="" />
        <input type="hidden" name="group_by_prat" value="1">
        <table class="tbl layout">
          <tr>
            <td class="narrow">
              <br />
              <select name="period" onchange="reloadAdmission();">
                <option value=""      {{if !$period          }}selected{{/if}}>&mdash; {{tr}}dPAdmission.admission all the day{{/tr}}</option>
                <option value="matin" {{if $period == "matin"}}selected{{/if}}>{{tr}}dPAdmission.admission morning{{/tr}}</option>
                <option value="soir"  {{if $period == "soir" }}selected{{/if}}>{{tr}}dPAdmission.admission evening{{/tr}}</option>
              </select>
            </td>
            <td class="narrow">
              <label title="Médecine">M<input title="Médecine" type="checkbox" name="type_pec[]" value="M" checked onclick="reloadFullAdmissions();"/></label>
              <label title="Chirurgie">C<input title="Chirurgie" type="checkbox" name="type_pec[]" value="C" checked onclick="reloadFullAdmissions();"/></label>
              <label title="Obstétrique">O<input title="Obstétrique" type="checkbox" name="type_pec[]" value="O" checked onclick="reloadFullAdmissions();"/></label>
              <br/>
              <select name="_type_admission" onchange="reloadFullAdmissions();" style="width:15em;">
                <option value="">&mdash; {{tr}}CSejour.all{{/tr}}</option>
                <option value="tous">&mdash; {{tr}}CSejour._type_admission.tous{{/tr}}</option>
                {{foreach from=$list_type_ad item=_admission}}
                  <option value="{{$_admission}}">{{tr}}CSejour._type_admission.{{$_admission}}{{/tr}}</option>
                {{/foreach}}
              </select>
            </td>
            <td>
              <br />
              <button type="button" onclick="Admissions.selectServices('listAdmissions');" class="search">Services</button>

              <select name="prat_id" onchange="reloadFullAdmissions();">
                <option value="">&mdash; {{tr}}CMediusers.praticiens.all{{/tr}}</option>
                {{mb_include module=mediusers template=inc_options_mediuser list=$prats selected=$sejour->praticien_id}}
              </select>
            </td>
          </tr>
        </table>
      </form>
    </td>
    <td>
      <table class="tbl layout">
        <tr>
          <td>
            <button type="button" onclick="Modal.open('preparePrintPlanning', {width: '500px', height: '150px'});" class="button print">{{tr}}Print{{/tr}}</button>
            <div id="preparePrintPlanning" style="display: none;">
              <form name="print_filter_option" method="get" style="text-align: center">
                <h2>{{tr}}dPAdmission.admission impression options{{/tr}}</h2>
                <p>
                  {{tr}}dPAdmission.admission group by prat{{/tr}}
                  <label><input type="radio" name="group_by_prat" value="1" checked="checked" onchange="$V(getForm('selType').group_by_prat, this.value);"/>Oui</label>
                  <label><input type="radio" name="group_by_prat" value="0" onchange="$V(getForm('selType').group_by_prat, this.value);"/>Non</label>
                </p>
                <p>
                  <label>
                    {{tr}}dPAdmission.admission ordonnate{{/tr}}
                    <select name="order_by" onchange="$V(getForm('selType').order_print_way, this.value);">
                      <option value="">{{tr}}dPAdmission.admission praticien name{{/tr}}</option>
                      <option value="patient_name">{{tr}}CPatient-nom-desc{{/tr}}</option>
                      <option value="entree_prevue">{{tr}}dPAdmission.admission heure prevue{{/tr}}</option>
                      <option value="entre_reelle">{{tr}}dPAdmission.admission heure reelle{{/tr}}</option>
                    </select>
                  </label>
                </p>
                <p><button type="button" onclick="printPlanning()" class="button print">{{tr}}Print{{/tr}}</button><button class="cancel" type="button" onclick="Control.Modal.close();">{{tr}}Cancel{{/tr}}</button> </p>
              </form>
            </div>
            <a href="#" onclick="Admissions.choosePrintForSelection()" class="button print">{{tr}}CCompteRendu-print_for_select{{/tr}}</a>
            {{if "web100T"|module_active}}
              <br/>
              {{mb_include module=web100T template=inc_button_send_all_prestations type=admissions}}
            {{/if}}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>