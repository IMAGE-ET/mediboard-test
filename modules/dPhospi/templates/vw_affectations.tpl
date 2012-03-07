{{mb_script module=hospi script=vw_affectations ajax=1}}

<table class="main">
  <tr>
    <td class="greedyPane" id="tableauAffectations">
      {{mb_include module=hospi template="inc_tableau_affectations_lits"}}
    </td>
    <td>
      {{mb_include module=hospi template="inc_patients_a_placer"}}
    </td>
  </tr>
</table>