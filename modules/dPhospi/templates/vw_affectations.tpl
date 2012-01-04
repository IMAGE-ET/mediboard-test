{{mb_script module=dPhospi script=vw_affectations ajax=1}}
{{mb_script module=dPhospi script=affectation_uf ajax=1}}
<table class="main">
  <tr>
    <td class="greedyPane" id="tableauAffectations">
      {{mb_include module=dPhospi template="inc_tableau_affectations_lits"}}
    </td>
    <td>
      {{mb_include module=dPhospi template="inc_patients_a_placer"}}
    </td>
  </tr>
</table>