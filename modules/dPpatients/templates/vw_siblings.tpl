{{mb_include_script module="dPpatients" script="pat_selector"}}

<table class="main">
  <tr>
    <td class="button" colspan="100">
      <form name="patFrm" action="?" method="get">
      <table class="form">
        <tr>
          <th><label for="patNom" title="Merci de choisir un patient">Choix du patient</label></th>
          <td>
            <input type="hidden" name="m" value="dPpatients" />
            <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" onchange="this.form.submit()" />
            <input type="text" readonly="readonly" name="patNom" value="{{$patient->_view}}" />
          </td>
          <td class="button">
            <button class="search" type="button" onclick="PatSelector.init()">Chercher</button>
            <script type="text/javascript">
            PatSelector.init = function(){
              this.sForm = "patFrm";
              this.sId   = "patient_id";
              this.sView = "patNom";
              this.pop();
            }
            </script>
          </td>
        </tr>
      </table>
      </form>
    </td>
  </tr>
  {{if $patient->_id}}
  <tbody>
  <form name="fusion" action="?" method="get">
  <input type="hidden" name="m" value="dPpatients" />
  <input type="hidden" name="a" value="fusion_pat" />
  <tr>
    <th class="title">
      <input type="radio" name="fusion_{{$patient->_id}}" value="{{$patient->_id}}" checked="checked" style="float: left;" />
      <button type="submit" class="search" style="float: left;">
        {{tr}}Merge{{/tr}}
      </button>
      Patient analysé
    </th>
    {{foreach from=$listSiblings item="curr_sib"}}
    <th class="title">
      <input type="checkbox" name="fusion_{{$curr_sib->_id}}" style="float: left;" />
      <label for="fusion_fusion_{{$curr_sib->_id}}">Doublon</label>
    </th>
    {{/foreach}}
  </tr>
  </form>
  </tbody>
  <tr>
    <td>
      {{include file="inc_vw_patient.tpl"}}
    </td>
    {{foreach from=$listSiblings item="curr_sib"}}
    {{assign var="patient" value=$curr_sib}}
    <td>
      {{include file="inc_vw_patient.tpl"}}
    </td>
    {{/foreach}}
  </tr>
  {{/if}}
</table>