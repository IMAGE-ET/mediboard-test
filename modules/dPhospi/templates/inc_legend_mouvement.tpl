<table class="tbl">
  <tr>
    <th colspan="2">Légende</th>
  </tr>
  <tr>
    <td style="width: 60px">
      <div class="wrapper_line">
        <div class="affect_legend clit" style="width: 33px">&nbsp;</div>
      </div>
    </td>
    <td>
      Affectation dont le début ou la fin ne sont pas visibles 
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit debut_sejour" style="width: 30px">&nbsp;</div>
      </div>
    </td>
    <td>
      Affectation dont le début correspond au début du séjour
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit fin_sejour" style="width: 30px">&nbsp;</div>
      </div>
    </td>
    <td>
      Affectation dont la fin correspond à la fin du séjour
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit affect_left" style="width: 30px">&nbsp;</div>
      </div>
    </td>
    <td>
      Affectation faisant suite à une précédente affectation
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit affect_right" style="width: 30px">&nbsp;</div>
      </div>
    </td>
    <td>
      Affectation qui fera suite à une autre affectation
    </td>
  </tr>
  <tr>
    <td>
      <div class="wrapper_line">
        <div class="affect_legend clit_bloque" style="width: 33px">&nbsp;</div>
      </div>
    </td>
    <td>
      Lit bloqué
    </td>
  </tr>
  <tr>
    <td class="button" colspan="2">
      <button type="button" class="cancel" onclick="Control.Modal.close();">{{tr}}Close{{/tr}}</button>
    </td>
  </tr>
</table>