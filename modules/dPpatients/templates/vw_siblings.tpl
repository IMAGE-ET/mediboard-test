{{mb_include_script module="dPpatients" script="pat_selector"}}

<script type="text/javascript">
function mergeMatchingPatients(button){
  var url = new Url("dPpatients", "ajax_merge_matching_patients");
  url.addParam("do_merge", $V(button.form.elements.do_merge) ? 1 : 0);
  url.requestUpdate("matching-patients-messages");
}
</script>

<table class="main">
  <tr>
    <td class="button" colspan="100">
      <form name="patFrm" action="?" method="get">
        
      <table class="form">
        <col style="width:20%" />
        
        <tr>
          <th colspan="2" class="category">
            Fusion de masse de patients identiques
          </th>
        </tr>
        <tr>
          <td style="text-align: right;">
            <button type="button" class="change" onclick="mergeMatchingPatients(this)">
              Chercher les patients identiques
            </button>
            <label><input type="checkbox" name="do_merge" /> Fusionner</label>
          </td>
          <td id="matching-patients-messages"></td>
        </tr>
        
        <tr>
          <th colspan="2" class="category">
            Fusion de patients similaires
          </th>
        <tr>
          <th>
            <label for="patNom">Choix du patient</label>
          </th>
          <td>
            <input type="hidden" name="m" value="dPpatients" />
            <input type="hidden" name="patient_id" value="{{$patient->patient_id}}" onchange="this.form.submit()" />
            <input type="text" readonly="readonly" name="patNom" value="{{$patient->_view}}" ondblclick="PatSelector.init()" />

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
    <!-- @TODO: HTML invalide (form qui englobe un TR) -->
    <form name="fusion" action="?" method="get">
      <input type="hidden" name="m" value="dPpatients" />
      <input type="hidden" name="a" value="fusion_pat" />
      <input type="hidden" name="objects_class" value="CPatient" />
      <input type="hidden" name="readonly_class" value="CPatient" />
      <tr>
        <th class="title">
          <input type="radio" name="objects_id[]" value="{{$patient->_id}}" checked="checked" style="float: left;" />
          <button type="submit" class="search" style="float: left;">
            {{tr}}Merge{{/tr}}
          </button>
          Patient analysé
        </th>
        {{foreach from=$listSiblings item="curr_sib"}}
        <th class="title">
          <input type="checkbox" name="objects_id[]" value="{{$patient->_id}}" style="float: left;" />
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
      <td>
        {{include file="inc_vw_patient.tpl" patient=$curr_sib}}
      </td>
    {{/foreach}}
  </tr>
  {{/if}}
</table>