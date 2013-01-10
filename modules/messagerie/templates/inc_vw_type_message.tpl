{{mb_default var=subject value=""}}

{{if $subject|substr:0:4 == "Fwd:"}}
	<img src="modules/messagerie/images/icons/fwd.png" title="{{tr}}CUserMail-type-fwd{{/tr}}" alt="{{tr}}CUserMail-type-fwd{{/tr}}" style="height:15px" />

{{elseif $subject|substr:0:3 == "Re:"}}
	<img src="modules/messagerie/images/icons/resp.png" title="{{tr}}CUserMail-type-responded{{/tr}}" alt="{{tr}}CUserMail-type-responded{{/tr}}" style="height:15px" />
{{/if}}