{{$line->_ref_praticien->_view}}
{{if $line->signee}}
 <img src="images/icons/tick.png" alt="Ligne signée par le praticien" title="Ligne signée par le praticien" />
{{else}}
	 <img src="images/icons/cross.png" alt="Ligne non signée par le praticien"title="Ligne non signée par le praticien" />
{{/if}}