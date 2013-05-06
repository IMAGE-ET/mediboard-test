

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

  {{mb_include module=system template=inc_config_enum var=mode_compat values='|'|implode:$modes}}
	
  {{assign var=m value=dPsante400}}
  {{mb_include module=system template=inc_config_enum var=prefix values=odbc|mysql}}
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

  {{assign var=class value=CIncrementer}}
  <tr>
    <th class="category" colspan="100">{{tr}}{{$class}}{{/tr}}</th>
  </tr>

  {{mb_include module=system template=inc_config_str var=cluster_count}}
  <tr>
    <th>
      <label title="{{tr}}config-dPsante400-CIncrementer-cluster_position-desc{{/tr}}">
        {{tr}}config-dPsante400-CIncrementer-cluster_position{{/tr}}
      </label>
    </th>
    <th>
      <div class="small-info">
        La position dans le cluster doit être définie dans le fichier <code>config_overload.php</code>, 
        <br />
        A la position : <code>dPsante400 CIncrementer cluster_position</code>
        <br />
        Elle est définie à <strong>{{$conf.dPsante400.CIncrementer.cluster_position}}</strong> sur ce serveur.
      </div>
    </th>
  </tr>

  <tr>
    <td class="button" colspan="6">
      <button class="modify" type="submit">{{tr}}Save{{/tr}}</button>
    </td>
  </tr>
</table>

</form>

<h2>Mouvements</h2>

<script type="text/javascript">

var Moves = {
  board: function() {
    this.url = new Url('sante400', 'mouvements_board');
    this.url.requestModal();
  },
  boardAction: function(action, type) {
    var url = new Url('sante400', 'ajax_do_moves');
    url.addParam('action', action);
    url.addParam('type', type);
    url.requestUpdate('doBoard', this.url.refreshModal.bind(this.url));
  },
  doAction: function(action) {
    var url = new Url('sante400', 'ajax_do_moves');
    url.addParam('action', action);
    url.addElement($('ActionType'));
    url.addElement($('ActionMarked'));
    url.requestUpdate('doMoves');
  },
  doImport: function() {
    var url = new Url('sante400', 'ajax_do_import');
    url.addElement($('ImportType'));
    url.addElement($('ImportOffset'));
    url.addElement($('ImportStep'));
    url.addElement($('ImportVerbose'));
    var onComplete = $('ImportAuto').checked ? Moves.doImport : Prototype.emptyFunction;
    url.requestUpdate('doImport', onComplete);
    
    var offset = parseInt($V('ImportOffset'), 10);
    var step   = parseInt($V('ImportStep'  ), 10);
    $V('ImportOffset', offset+step);
  }
}

</script>

<button class="change singleclick" onclick="Moves.board();">
  Board
</button>


<table class="tbl">
  <tr>
    <th class="narrow">Mouvements</th>
    <th class="narrow">Action</th>
    <th>Status</th>
  </tr>

  <tr>
    <td>
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
      <div>
        <button class="search singleclick" onclick="Moves.doAction('count')">
          {{tr}}Count{{/tr}}
        </button>
      </div>
      <div>
        <button class="search singleclick" onclick="Moves.doAction('obsolete')">
          {{tr}}Obsolete{{/tr}}
        </button>
      </div>
    </td>
    <td class="text" id="doMoves"></td>
  </tr>

  <tr>
    <td>
      <div>
        <label for="ImportType" title="{{tr}}CMouvement400-type-desc{{/tr}}">{{tr}}CMouvement400-type{{/tr}}</label>
        <select id="ImportType" name="type">
          <option value="">&mdash; {{tr}}Choose{{/tr}}</option>
          {{foreach from=$types item=_type}}
          <option value="{{$_type}}">{{tr}}CMouvement400-type-{{$_type}}{{/tr}}</option>
          {{foreachelse}}
          <option value="">Pas de type disponible</option>
          {{/foreach}}
        </select>
      </div>

      <div>
        <label for="ImportOffset">Offset</label>
        <input id="ImportOffset" type="text" name="offset" value="0" />
      </div>

      <div>
        <label for="ImportStep">Step</label>
        <input id="ImportStep" type="text" name="step" value="1" />
      </div>

      <div>
        <input id="ImportAuto" type="checkbox" name="auto" value="1"  />
        <label for="ImportAuto">Auto</label>
      </div>

      <div>
        <input id="ImportVerbose" type="checkbox" name="verbose" value="1" />
        <label for="ImportVerbose">Verbose</label>
      </div>

    </td>
    <td>

      <button class="search singleclick" onclick="Moves.doImport()">
        {{tr}}Import{{/tr}}
      </button>
    </td>
    <td class="text" id="doImport"></td>
  </tr>

</table>

