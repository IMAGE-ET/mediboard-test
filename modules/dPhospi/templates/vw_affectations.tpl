{{mb_script module=hospi script=vw_affectations ajax=1}}
{{mb_script module=planningOp script=prestations ajax=1}}

{{if "dPImeds"|module_active}}
  <script>
    Main.add(function() {
      ImedsResultsWatcher.loadResults();
    });
  </script>
{{/if}}

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