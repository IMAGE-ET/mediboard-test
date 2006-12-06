{{include file="inc_echantillonnage_fonctions.tpl"}}

<form action="index.php?m={{$m}}" name="echantillonage" method="post" onsubmit="return checkForm(this)">
<input type="hidden" name="m" value="{{$m}}" />
<input type="hidden" name="dosql" value="do_echantillonnage" />
<input type="hidden" name="tab" value="{{$tab}}" />

<table class="form">
  <tr>
    <td>
      <div class="accordionMain" id="accEchantillonnage">
        <div id="Etape1">
          <div id="Etape1Header" class="accordionTabTitleBar">
            Etape 1 - Etablissement
          </div>
          <div id="Etape1Content" class="accordionTabContentBox">
            {{include file="inc_echantillonnage_etape1.tpl"}}
          </div>
        </div>
        <div id="Etape2" style="display:none;">
          <div id="Etape2Header" class="accordionTabTitleBar">
            Etape 2 - Cabinet / Services  / Salles
          </div>
          <div id="Etape2Content" class="accordionTabContentBox"></div>
        </div>
        <div id="Etape3" style="display:none;">
          <div id="Etape3Header" class="accordionTabTitleBar">
            Etape 3 - Patients / Praticiens
          </div>
          <div id="Etape3Content" class="accordionTabContentBox"></div>
        </div>
        <div id="Etape4" style="display:none;">
          <div id="Etape4Header" class="accordionTabTitleBar">
            Etape 4 - Consultations / Interventions
          </div>
          <div id="Etape4Content" class="accordionTabContentBox"></div>
        </div>
      </div>
    </td>
  </tr>
</table>
</form>
<script language="Javascript" type="text/javascript">
var oAccord = new Rico.Accordion( $('accEchantillonnage'), {
  panelHeight: 300,
  showDelay:50,
  showSteps:3
});
</script>