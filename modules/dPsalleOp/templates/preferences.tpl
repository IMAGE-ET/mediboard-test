{{if "dPsalleOp COperation password_sortie"|conf:"CGroups-$g" && $app->_ref_user->isAnesth()}}
  {{mb_include template=inc_pref spec=bool var=autosigne_sortie}}
{{/if}}
{{mb_include template=inc_pref spec=str var=default_salles_id readonly=1}}