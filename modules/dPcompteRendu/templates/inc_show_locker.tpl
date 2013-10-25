<table class="form">
  {{if $compte_rendu->valide || $auto_lock}}
    <tr>
      <th class="title" colspan="2">Verrouillé</th>
    </tr>
    {{if $compte_rendu->valide}}
      <tr>
        <th>Par</th>
        <td>{{mb_include module=mediusers template=inc_vw_mediuser}}</td>
      </tr>
    {{/if}}
    {{if $auto_lock}}
      <tr>
        <td colspan="2">
          Document verrouillé automatiquement
          (veuillez contacter un administrateur pour connaitre la configuration du verrouillage automatique)
        </td>
      </tr>
    {{/if}}
  {{else}}
    <tr>
      <th class="title" colspan="2">Document non verrouillé</th>
    </tr>
  {{/if}}
</table>
