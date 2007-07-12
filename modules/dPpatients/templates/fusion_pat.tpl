{{mb_include_script module=system script="mb_object"}}

<script type="text/javascript">

function setField(oField, sValue) {
  oField.value = sValue;
}

</script>

<h2 class="module {{$m}}">Fusion de patients</h2>

<form name="editFrm" action="?m={{$m}}" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="dosql" value="do_patients_fusion" />
<input type="hidden" name="del" value="0" />
<input type="hidden" name="patient1_id" value="{{$patient1->_id}}" />
<input type="hidden" name="patient2_id" value="{{$patient2->_id}}" />

<table class="main">
  <tr>
    <td>
      <div class="accordionMain" id="accordionConsult">    
        <div id="Identite">
          <div id="IdentiteHeader" class="accordionTabTitleBar">
            Identité
          </div>
          <div id="IdentiteContent"  class="accordionTabContentBox">
            {{include file="inc_acc/inc_acc_fusion_identite.tpl"}}
          </div>
        </div>
        <div id="Medical">
          <div id="MedicalHeader" class="accordionTabTitleBar">
            Médical
          </div>
          <div id="MedicalContent"  class="accordionTabContentBox">
            {{include file="inc_acc/inc_acc_fusion_medical.tpl"}}
          </div>
        </div>
        <div id="Corresp">
          <div id="CorrespHeader" class="accordionTabTitleBar">
            Correspondance
          </div>
          <div id="CorrespContent"  class="accordionTabContentBox">
            {{include file="inc_acc/inc_acc_fusion_corresp.tpl"}}
          </div>
        </div>
      </div>
    </td>
  </tr>
  
  <tr>
    <td class="button" colspan="5" style="text-align:center;" id="button">
      <button type="button" class="search" onclick="MbObject.viewBackRefs('{{$patient1->_class_name}}', ['{{$patient1->_id}}', '{{$patient2->_id}}']);">
        {{tr}}CMbObject-merge-moreinfo{{/tr}}
      </button>

      <button type="submit" class="submit">
        {{tr}}Merge{{/tr}}
      </button>
    </td>
  </tr>
</table>
</form>

<script language="Javascript" type="text/javascript">
var oAccord = new Rico.Accordion( $('accordionConsult'), { 
  panelHeight: ViewPort.SetAccordHeight('accordionConsult', { sOtherElmt: 'button', iBottomMargin : 7 }),
  showDelay: 50, 
  showSteps: 3 
} );
</script>