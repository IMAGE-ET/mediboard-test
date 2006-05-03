            {if $consult_anesth->consultation_anesth_id}
            <form name="editAnesthPatFrm" action="?m={$m}" method="post" onsubmit="checkForm(this);">
            <input type="hidden" name="m" value="{$m}" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="dosql" value="do_consult_anesth_aed" />
            <input type="hidden" name="consultation_anesth_id" value="{$consult_anesth->consultation_anesth_id}" />
            <table class="form">
              <tr>
                <th><label for="poid" title="Poids du patient">Poids:</label></th>
                <td>
                  <input type="text" size="4" name="poid" title="{$consult_anesth->_props.poid}" value="{$consult_anesth->poid}" />
                  kg
                </td>
                <th><label for="tabac" title="Comportement tabagique">Tabac:</label></th>
                <td>
                  <select name="tabac" title="{$consult_anesth->_props.tabac}|%2B|%2B%2B">
                    <option value="?" {if $consult_anesth->tabac == "?"}selected="selected"{/if}>
                      ?
                    </option>
                    <option value="-" {if $consult_anesth->tabac == "-"}selected="selected"{/if}>
                      -
                    </option>
                    <option value="%2B" {if $consult_anesth->tabac == "+"}selected="selected"{/if}>
                      +
                    </option>
                    <option value="%2B%2B" {if $consult_anesth->tabac == "++"}selected="selected"{/if}>
                      ++
                    </option>
                  </select>
                </td>
              </tr>
              <tr>
                <th><label for="taille" title="Taille du patient">Taille:</label></th>
                <td>
                  <input type="text" size="4" name="taille" title="{$consult_anesth->_props.taille}" value="{$consult_anesth->taille}" />
                  m
                </td>
                <th><label for="oenolisme" title="Comportement alcoolique">Oenolisme:</label></th>
                <td>
                  <select name="oenolisme" title="{$consult_anesth->_props.oenolisme}|%2B|%2B%2B">
                    <option value="?" {if $consult_anesth->oenolisme == "?"}selected="selected"{/if}>
                      ?
                    </option>
                    <option value="-" {if $consult_anesth->oenolisme == "-"}selected="selected"{/if}>
                      -
                    </option>
                    <option value="%2B" {if $consult_anesth->oenolisme == "+"}selected="selected"{/if}>
                      +
                    </option>
                    <option value="%2B%2B" {if $consult_anesth->oenolisme == "++"}selected="selected"{/if}>
                      ++
                    </option>
                  </select>
                </td>
              </tr>
              <tr>
                <th><label for="groupe" title="Groupe sanguin">Groupe:</label></th>
                <td>
                  <select name="groupe" title="{$consult_anesth->_props.groupe}">
                    {html_options values=$consult_anesth->_enums.groupe output=$consult_anesth->_enums.groupe selected=$consult_anesth->groupe}
                  </select>
                  /
                  <select name="rhesus" title="{$consult_anesth->_props.rhesus}|%2B">
                    <option value="?" {if $consult_anesth->rhesus == "?"}selected="selected"{/if}>
                      ?
                    </option>
                    <option value="-" {if $consult_anesth->rhesus == "-"}selected="selected"{/if}>
                      -
                    </option>
                    <option value="%2B" {if $consult_anesth->rhesus == "+"}selected="selected"{/if}>
                      +
                    </option>
                  </select>
                </td>
                <th><label for="transfusions" title="Antécédents de transfusions">Transfusion:</label></th>
                <td>
                  <select name="transfusions" title="{$consult_anesth->_props.transfusions}|%2B">
                    <option value="?" {if $consult_anesth->transfusions == "?"}selected="selected"{/if}>
                      ?
                    </option>
                    <option value="-" {if $consult_anesth->transfusions == "-"}selected="selected"{/if}>
                      -
                    </option>
                    <option value="%2B" {if $consult_anesth->transfusions == "+"}selected="selected"{/if}>
                      +
                    </option>
                  </select>
                </td>
              </tr>
              <tr>
                <th><label for="tasys" title="Pression arterielle">TA:</label></th>
                <td>
                  <input type="text" size="2" name="tasys" title="{$consult_anesth->_props.tasys}" value="{$consult_anesth->tasys}" />
                  -
                  <input type="text" size="2" name="tadias" title="{$consult_anesth->_props.tadias}" value="{$consult_anesth->tadias}" />
                </td>
                <th><label for="ASA" title="Score ASA">ASA:</label></th>
                <td>
                  <select name="ASA">
                    {html_options values=$consult_anesth->_enums.ASA output=$consult_anesth->_enums.ASA selected=$consult_anesth->ASA}
                  </select>
                </td>
              </tr>
              <tr>
                <td class="button" colspan="4">
                  <button type="button" onclick="submitConsultAnesth()">Valider</button>
                </td>
              </tr>
            </table>
            </form>
            {else}
            <form name="addOpFrm" action="?m={$m}" method="post">
            <input type="hidden" name="dosql" value="do_consultation_aed" />
            <input type="hidden" name="del" value="0" />
            <input type="hidden" name="m" value="dPcabinet" />
            <input type="hidden" name="consultation_id" value="{$consult->consultation_id}" />
            <table width="100%">
              <tr>
                <td><strong>Veuillez séléctionner une intervention</strong></td>
              </tr>
              {foreach from=$patient->_ref_operations item=curr_op}
              <tr>
                <td class="text">
                  <input type="radio" name="_operation_id" value="{$curr_op->operation_id}" />
                  Intervention le {$curr_op->_ref_plageop->date|date_format:"%d/%m/%Y"}
                  avec le Dr. {$curr_op->_ref_chir->_view}
                  {if $curr_op->_ext_codes_ccam|@count}
                  <ul>
                    {foreach from=$curr_op->_ext_codes_ccam item=curr_code}
                    <li><i>{$curr_code->libelleLong}</i></li>
                    {/foreach}
                  </ul>
                  {/if}
                </td>
              </tr>
              {foreachelse}
              <tr>
                <td>Aucune intervention de prévu</td>
              </tr>
              {/foreach}
              <tr>
                <td class="button">
                  <button type="button" onclick="submitOpConsult()">valider</button>
            </table>
            </form>
            {/if}