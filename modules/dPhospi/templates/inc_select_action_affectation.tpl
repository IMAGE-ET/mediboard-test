<table class="form">
  <tr>
    <th class="title">
      Affectation du {{$affectation->entree|date_format:$conf.datetime}} au {{$affectation->sortie|date_format:$conf.datetime}}
    </th>
  </tr>
  {{if $affectation->lit_id != $lit_id}}
    <tr>
      <td>
        <button type="button" class="tick"
          onclick="moveAffectation('{{$affectation_id}}', '{{$lit_id}}', '{{$sejour_id}}', '{{$affectation->lit_id}}'); Control.Modal.close();">
            Déplacer l'affectation
         </button>
      </td>
    </tr>
  {{/if}}
</table>

{{mb_include module=hospi template=inc_other_actions}}