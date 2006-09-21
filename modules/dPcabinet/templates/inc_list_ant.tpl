      <strong>Antécédents du patient</strong>
      <ul>
      {{if $patient->_ref_antecedents}}
        {{foreach from=$listAnt key=keyAnt item=currTypeAnt}}
        {{if $currTypeAnt}}
        <li>
          {{tr}}{{$keyAnt}}{{/tr}}
          {{foreach from=$currTypeAnt item=currAnt}}
          <ul><li>
            <form name="delAntFrm" action="?m=dPcabinet" method="post">
            <input type="hidden" name="m" value="dPpatients" />
            <input type="hidden" name="del" value="1" />
            <input type="hidden" name="dosql" value="do_antecedent_aed" />
            <input type="hidden" name="antecedent_id" value="{{$currAnt->antecedent_id}}" />
            <button class="trash notext" type="button" onclick="submitAnt(this.form)">
            </button>          
            {{if $currAnt->date|date_format:"%d/%m/%Y"}}
              {{$currAnt->date|date_format:"%d/%m/%Y"}} :
            {{/if}}
            <i>{{$currAnt->rques}}</i>
            </form>
          </li></ul>
          {{/foreach}}
        </li>
        {{/if}}
        {{/foreach}}
      {{else}}
        <li>Pas d'antécédents</li>
      {{/if}}
      </ul>
      <strong>Traitements du patient</strong>
      <ul>
        {{foreach from=$patient->_ref_traitements item=curr_trmt}}
        <li>
          <form name="delTrmtFrm" action="?m=dPcabinet" method="post">
          <input type="hidden" name="m" value="dPpatients" />
          <input type="hidden" name="del" value="1" />
          <input type="hidden" name="dosql" value="do_traitement_aed" />
          <input type="hidden" name="traitement_id" value="{{$curr_trmt->traitement_id}}" />
          <button class="trash notext" type="button" onclick="submitAnt(this.form)">
          </button>
          {{if $curr_trmt->fin}}
            Du {{$curr_trmt->debut|date_format:"%d/%m/%Y"}} au {{$curr_trmt->fin|date_format:"%d/%m/%Y"}}
          {{else}}
            Depuis le {{$curr_trmt->debut|date_format:"%d/%m/%Y"}}
          {{/if}}
          : <i>{{$curr_trmt->traitement}}</i>
          </form>
        </li>
        {{foreachelse}}
        <li>Pas de traitements</li>
        {{/foreach}}
      </ul>
      <strong>Diagnostics du patient</strong>
      <ul>
        {{foreach from=$patient->_codes_cim10 item=curr_code}}
        <li>
          <button class="trash notext" type="button" onclick="delCim10('{{$curr_code->code}}')">
          </button>
          {{$curr_code->code}}: {{$curr_code->libelle}}
        </li>
        {{foreachelse}}
        <li>Pas de diagnostic</li>
        {{/foreach}}
      </ul>