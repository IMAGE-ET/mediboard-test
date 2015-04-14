{{*
 * $Id: $
 *
 * @package    Mediboard
 * @subpackage dPpmsi
 * @author     SARL OpenXtrem
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: $
 *}}
<script>
  Main.add(function () {
    Control.Tabs.create('tabs-liste-actes', true);
    {{foreach from=$sejour->_ref_operations item=_op}}
      PMSI.loadExportActes('{{$_op->_id}}', 'COperation');
    {{/foreach}}
    PMSI.loadExportActes('{{$sejour->_id}}', 'CSejour', null, "{{$m}}" );
    PMSI.loadDiagsDossier('{{$sejour->_id}}');
    PMSI.loadDiagsPMSI('{{$sejour->_id}}');
  });
</script>

<table class="main layout">
  <tr>
    <td style="white-space:nowrap;" class="narrow">
      <ul id="tabs-liste-actes" class="control_tabs_vertical">
        <li>
          <a href="#{{$sejour->_guid}}" class="{{if $sejour->_count_actes == 0}}empty{{/if}} {{if $sejour->annule}}cancelled{{/if}}"
            >Sejour (<span id="count_actes_{{$sejour->_guid}}">{{$sejour->_count_actes}}</span>)
            <br/>
            <span>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$sejour->_ref_praticien}}</span>
          </a>
        </li>
        {{foreach from=$sejour->_ref_operations item=_op}}
          <li>
            <a href="#{{$_op->_guid}}" class="{{if $_op->_count_actes == 0}}empty{{/if}} {{if $_op->annulee}}cancelled{{/if}}"
              >Intervention du {{$_op->_datetime|date_format:$conf.date}} (<span id="count_actes_{{$_op->_guid}}">{{$_op->_count_actes}}</span>)
              <br/>
              <span>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_op->_ref_praticien}}</span>
            </a>
          </li>
        {{/foreach}}
        {{foreach from=$sejour->_ref_consultations item=_consult}}
          <li>
            <a href="#{{$_consult->_guid}}" class="{{if $_consult->_count_actes == 0}}empty{{/if}} {{if $_consult->annule}}cancelled{{/if}}"
              >Consultation du {{$_consult->_ref_plageconsult->date|date_format:$conf.date}}
                (<span id="count_actes_{{$_consult->_guid}}">{{$_consult->_count_actes}}</span>)
              <br/>
              <span>{{mb_include module=mediusers template=inc_vw_mediuser mediuser=$_consult->_ref_praticien}}</span>
            </a>
          </li>
        {{/foreach}}
      </ul>
    </td>
    <td>
      <div id="{{$sejour->_guid}}" style="display: none;">
        <table class="main layout">
          <tr>
            <td class="halfPane">
              <fieldset>
                <legend>Diagnostics PMSI</legend>
                <div id="diags_pmsi"></div>
              </fieldset>
            </td>
            <td>
              <fieldset>
                <legend>Diagnostics du dossier</legend>
                <div id="diags_dossier"></div>
              </fieldset>
            </td>
          </tr>
          <tr>
            <td>
              <fieldset>
                <legend>Antécédents</legend>
                <table class="tbl">
                  <tr>
                    <th class="halfPane category">{{tr}}CPatient{{/tr}}</th>
                    <th class="category">{{tr}}CSejour{{/tr}}</th>
                  </tr>
                  <tr>
                    <td class="text">
                      <ul>
                        {{foreach from=$sejour->_ref_patient->_ref_dossier_medical->_ref_antecedents_by_type key=curr_type item=list_antecedent}}
                          {{if $list_antecedent|@count}}
                            <li>
                              {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
                              {{foreach from=$list_antecedent item=curr_antecedent}}
                                <ul>
                                  <li>
                                    {{if $curr_antecedent->date}}
                                      {{mb_value object=$curr_antecedent field=date}} -
                                    {{/if}}
                                    <em>{{$curr_antecedent->rques}}</em>
                                  </li>
                                </ul>
                              {{/foreach}}
                            </li>
                          {{/if}}
                        {{foreachelse}}
                          <li class="empty">{{tr}}CAntecedent.none{{/tr}}</li>
                        {{/foreach}}
                      </ul>
                    </td>
                    <td class="text">
                      <ul>
                        {{foreach from=$sejour->_ref_dossier_medical->_ref_antecedents_by_type key=curr_type item=list_antecedent}}
                          {{if $list_antecedent|@count}}
                            <li>
                              {{tr}}CAntecedent.type.{{$curr_type}}{{/tr}}
                              {{foreach from=$list_antecedent item=curr_antecedent}}
                                <ul>
                                  <li>
                                    {{if $curr_antecedent->date}}
                                      {{mb_value object=$curr_antecedent field=date}} -
                                    {{/if}}
                                    <em>{{$curr_antecedent->rques}}</em>
                                  </li>
                                </ul>
                              {{/foreach}}
                            </li>
                          {{/if}}
                          {{foreachelse}}
                          <li class="empty">{{tr}}CAntecedent.none{{/tr}}</li>
                        {{/foreach}}
                      </ul>
                    </td>
                  </tr>
                </table>
              </fieldset>
            </td>
            <td>
              <fieldset>
                <legend>Traitements personnels</legend>
                <table class="tbl">
                  <tr>
                    <th class="halfPane category">{{tr}}CPatient{{/tr}}</th>
                    <th class="category">{{tr}}CSejour{{/tr}}</th>
                  </tr>
                  <tr>
                    <td class="text">
                      {{assign var=dossier_medical value=$sejour->_ref_patient->_ref_dossier_medical}}
                      <ul>
                        <!-- Traitements personnels du patient -->
                        {{if $dossier_medical->_ref_prescription}}
                          {{foreach from=$dossier_medical->_ref_prescription->_ref_prescription_lines item=_line_med}}
                            <li>
                              <a href="#1" onclick="Prescription.viewProduit(null,'{{$_line_med->code_ucd}}','{{$_line_med->code_cis}}');">
                                {{$_line_med->_ucd_view}}
                              </a>
                              {{if $_line_med->_ref_prises|@count}}
                                ({{foreach from=$_line_med->_ref_prises item=_prise name=foreach_prise}}
                                {{$_prise->_view}}{{if !$smarty.foreach.foreach_prise.last}},{{/if}}
                              {{/foreach}})
                              {{/if}}
                              {{if $_line_med->commentaire}}
                                ({{$_line_med->commentaire}})
                              {{/if}}
                              {{if $_line_med->debut || $_line_med->fin}}
                                <span class="compact">({{mb_include module=system template=inc_interval_date from=$_line_med->debut to=$_line_med->fin}})</span>
                              {{/if}}
                            </li>
                          {{/foreach}}
                        {{/if}}
                        <hr/>
                        {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
                          <li>
                            {{if $curr_trmt->fin}}
                              Depuis {{mb_value object=$curr_trmt field=debut}}
                              jusqu'à {{mb_value object=$curr_trmt field=fin}} :
                            {{elseif $curr_trmt->debut}}
                              Depuis {{mb_value object=$curr_trmt field=debut}} :
                            {{/if}}
                            <em>{{$curr_trmt->traitement}}</em>
                          </li>
                          {{foreachelse}}
                          {{if $dossier_medical->absence_traitement}}
                            <li class="empty">{{tr}}CTraitement.absence{{/tr}}</li>
                          {{else}}
                            <li class="empty">{{tr}}CTraitement.none{{/tr}}</li>
                          {{/if}}
                        {{/foreach}}
                      </ul>
                    </td>
                    <td class="text">
                      <ul>
                        {{foreach from=$dossier_medical->_ref_traitements item=curr_trmt}}
                          <li>
                            {{if $curr_trmt->fin}}
                              Depuis {{mb_value object=$curr_trmt field=debut}}
                              jusqu'à {{mb_value object=$curr_trmt field=fin}} :
                            {{elseif $curr_trmt->debut}}
                              Depuis {{mb_value object=$curr_trmt field=debut}} :
                            {{/if}}
                            <em>{{$curr_trmt->traitement}}</em>
                          </li>
                          {{foreachelse}}
                          <li class="empty">{{tr}}CTraitement.none{{/tr}}</li>
                        {{/foreach}}
                      </ul>
                    </td>
                  </tr>
                </table>
              </fieldset>
            </td>
          </tr>
          <tr>
            <td>
              <div id="export_{{$sejour->_guid}}"></div>
            </td>
          </tr>
        </table>
        {{mb_include module=pmsi template=inc_codage_actes subject=$sejour}}
      </div>
      {{foreach from=$sejour->_ref_operations item=_op}}
        <div id="{{$_op->_guid}}" style="display: none;">
          {{mb_include module=pmsi template=inc_header_actes subject=$_op}}
          {{mb_include module=pmsi template=inc_codage_actes subject=$_op}}

          <table class="main layout">
            {{if ($conf.dPpmsi.systeme_facturation == "siemens")}}
            <tr>
              <td colspan="4">
                <form name="editOpFrm{{$_op->_id}}" action="?" method="post" onsubmit="return onSubmitFormAjax(this)">
                  <input type="hidden" name="dosql" value="do_planning_aed" />
                  <input type="hidden" name="m" value="dPplanningOp" />
                  <input type="hidden" name="del" value="0" />
                  <input type="hidden" name="operation_id" value="{{$_op->operation_id}}" />
                  <table class="form">
                    <tr>
                      <th class="category" colspan="2">
                        <em>Lien S@nté.com</em> : Intervention
                      </th>
                    </tr>
                    <tr>
                      <th><label for="_cmca_uf_preselection" title="Choisir une pré-selection pour remplir les unités fonctionnelles">Pré-sélection</label></th>
                      <td>
                        <select name="_cmca_uf_preselection" onchange="PMSI.choosePreselection(this)">
                          <option value="">&mdash; Choisir une pré-selection</option>
                          <option value="ABS|ABSENT">(ABS) Absent</option>
                          <option value="AEC|ARRONDI EURO">(AEC) Arrondi Euro</option>
                          <option value="AEH|ARRONDI EURO">(AEH) Arrondi Euro</option>
                          <option value="AMB|CHIRURGIE AMBULATOIRE">(AMB) Chirurgie Ambulatoire</option>
                          <option value="CHI|CHIRURGIE">(CHI) Chirurgie</option>
                          <option value="CHO|CHIRURGIE COUTEUSE">(CHO) Chirurgie Coûteuse</option>
                          <option value="EST|ESTHETIQUE">(EST) Esthétique</option>
                          <option value="EXL|EXL POUR RECUP V4 V5">(EXL) EXL pour récup. v4 v5</option>
                          <option value="EXT|EXTERNES">(EXT) Externes</option>
                          <option value="MED|MEDECINE">(MED) Médecine</option>
                          <option value="PNE|PNEUMOLOGUE">(PNE) Pneumologie</option>
                          <option value="TRF|TRANSFERT >48H">(TRF) Transfert > 48h</option>
                          <option value="TRI|TRANSFERT >48H">(TRI) Transfert > 48h</option>
                        </select>
                      </td>
                    </tr>
                    <tr>
                      <th>
                        <label for="code_uf" title="Choisir un code pour l'unité fonctionnelle">Code d'unité fonct.</label>
                      </th>
                      <td>
                        <input type="text" class="notNull {{$_op->_props.code_uf}}" name="code_uf" value="{{$_op->code_uf}}" size="10" maxlength="10" />
                      </td>
                    </tr>
                    <tr>
                      <th>
                        <label for="libelle_uf" title="Choisir un libellé pour l'unité fonctionnelle">Libellé d'unité fonct.</label>
                      </th>
                      <td>
                        <input type="text" class="notNull {{$_op->_props.libelle_uf}}" name="libelle_uf" value="{{$_op->libelle_uf}}" size="20" maxlength="35" onchange="this.form.onsubmit()" />
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" id="updateOp{{$_op->operation_id}}"></td>
                    </tr>
                  </table>
                </form>
              </td>
            </tr>
            {{/if}}
            <tr>
              <td colspan="4" id="export_{{$_op->_guid}}">
              </td>
            </tr>
          </table>
        </div>
      {{/foreach}}
      {{foreach from=$sejour->_ref_consultations item=_consult}}
        <div id="{{$_consult->_guid}}" style="display: none;">
          {{mb_include module=pmsi template=inc_header_actes subject=$_consult}}
          {{mb_include module=pmsi template=inc_codage_actes subject=$_consult}}
        </div>
      {{/foreach}}
    </td>
  </tr>
</table>