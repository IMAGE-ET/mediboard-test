{if $canEdit}
<div>
  <form name="editFrm" action="?m=system" method="post">
    <input type="hidden" name="m" value="system" />
    <input type="hidden" name="dosql" value="empty_logs" />
    <button type="submit">
      <img src="modules/{$m}/images/cross.png" alt="supprimer" />
      Réinitialiser les logs
    </button>
</div>
{/if}