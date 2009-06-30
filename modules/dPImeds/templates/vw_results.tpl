{{mb_include_script module="dPpatients" script="pat_selector"}}

<table class="main">
  <tr>
    <td class="halfPane">
      <form name="patFrm" action="?" method="get">
      <table class="form">
        <tr>
          <th><label for="_view" title="Merci de choisir un patient pour voir ses résultats">Choix du patient</label></th>
          <td>
            <input type="hidden" name="m" value="{{$m}}" />
            <input type="hidden" name="patient_id" value="{{$patient->_id}}" onchange="this.form.submit()" />
            <input type="text" readonly="readonly" name="_view" value="{{$patient->_view}}" />
          </td>
          <td class="button">
            <button class="search" type="button" onclick="PatSelector.init()">Chercher</button>
            <script type="text/javascript">
            PatSelector.init = function(){
              this.sForm = "patFrm";
              this.sId   = "patient_id";
              this.sView = "_view";
              this.pop();
            }
          </script>
          </td>
        </tr>
      </table>
      </form>
    </td>
    <td class="halfPane">
      <form name="sejourFrm" action="?" method="get">
      <table class="form">
        <tr>
          <th><label for="_view" title="Merci de choisir un sejour pour voir ses résultats">Choix du sejour</label></th>
          <td>
            <input type="hidden" name="m" value="{{$m}}" />
            <select name="sejour_id" onchange="this.form.submit()">
              <option value="">&mdash; séjours disponibles</option>
              {{foreach from=$patient->_ref_sejours item=curr_sejour}}
              <option value="{{$curr_sejour->sejour_id}}" {{if $curr_sejour->sejour_id == $sejour->sejour_id}}selected="selected"{{/if}}>
                {{$curr_sejour->_view}}
              </option>
              {{/foreach}}
            </select>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  <tr>
    <td>
      {{if $patient->_IPP}}
      {{include file="inc_patient_results.tpl"}}
      {{/if}}
    </td>
    <td>
      {{if $sejour->_num_dossier}}
      {{include file="inc_sejour_results.tpl"}}
      {{/if}}
    </td>
  </tr>
</table>