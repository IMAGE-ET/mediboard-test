{{if !$app->_is_intranet}}
	<div class="big-info">
	Pour des raisons de sécurité, l'affichage des résultats de laboratoire n'est pas disponible depuis l'accès distant.
	<br />
	Merci de réessayer ultérieurement depuis un <strong>accès sur site</strong>.
	</div>
	
{{else}}
	<div class="big-warning">
	Le module IMeds n'a pas été correctement configuré.
	<br />
	Merci de contacter un administrateur pour <strong>paramétrer les URL de connexions au serveur de résultat</strong>.
</div>
{{/if}}