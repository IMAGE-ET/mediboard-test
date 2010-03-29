

<h2>Environnement d'execution</h2>

<form name="editConfig" action="?m={{$m}}&amp;{{$actionType}}=configure" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="system" />
<input type="hidden" name="dosql" value="do_configure" />

<table class="form">

  <tr>
    <th class="category" colspan="100">Connexion à la source de données</th>
  </tr>
  
  {{assign var=m value=interop}}
  {{assign var=mod value=interop}}

  {{mb_include module=system template=inc_config_enum var=mode_compat values="default|medicap"}}
	
  {{assign var=m value=dPsante400}}
  {{mb_include module=system template=inc_config_str var=dsn}}
  {{mb_include module=system template=inc_config_str var=user}}
  {{mb_include module=system template=inc_config_str var=pass}}
   
  <tr>
    <th class="category" colspan="100">Traitement des mouvements</th>
  </tr>

  {{mb_include module=system template=inc_config_str var=group_id}}
  {{mb_include module=system template=inc_config_str var=nb_rows}}
  {{mb_include module=system template=inc_config_bool var=mark_row}}
        
  <tr>
    <th class="category" colspan="100">Synchronisation des objets</th>
  </tr>

  {{mb_include module=system template=inc_config_str var=cache_hours}}
	
  
	{{assign var=class value=CSejour}}
	<tr>
    <th class="category" colspan="100">{{tr}}{{$class}}{{/tr}}</th>
  </tr>
	
  {{mb_include module=system template=inc_config_str var=sibling_hours}}

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

<h2>Mouvements</h2>

<table class="tbl">
  <tr>
    <th>Choix des mouvements</th>
    <th>Action sur les mouvements</th>
    <th>Status</th>
  </tr>
  <tr>
    <td>

			<script type="text/javascript">
			
			var Moves = {
			  doAction: function(sAction) {
			    var url = new Url("dPsante400", "httpreq_do_moves");
			    url.addParam("action", sAction);
			    url.addElement($("ActionType"));
			    url.addElement($("ActionMarked"));
			    url.requestUpdate("purgeMoves");
			  }
			}
			
			</script>

      <label for="ActionType" title="{{tr}}CMouvement400-type-desc{{/tr}}">{{tr}}CMouvement400-type{{/tr}}</label>
      <select id="ActionType" name="type">
        <option value="all">&mdash; {{tr}}All{{/tr}}</option>
        {{foreach from=$types item=_type}}
        <option value="{{$_type}}">{{tr}}CMouvement400-type-{{$_type}}{{/tr}}</option>
        {{foreachelse}}
        <option value="">Pas de type disponible</option>
        {{/foreach}}
      </select>
      
      <br />

	    <label for="marked" title="{{tr}}CMouvement400-marked-desc{{/tr}}">{{tr}}CMouvement400-marked{{/tr}}</label>
	    <select id="ActionMarked" name="marked">
        <option value="all">&mdash; {{tr}}All{{/tr}}</option>
	      <option value="0">{{tr}}CMouvement400-marked-0{{/tr}}</option>
	      <option value="1">{{tr}}CMouvement400-marked-1{{/tr}}</option>
	    </select>
    </td>
    <td>
      <button class="search" onclick="Moves.doAction('count')">
        {{tr}}Count{{/tr}}
      </button>
    </td>
    <td class="text" id="purgeMoves" />
  </tr>

</table>

