{{mb_include_script module=$m script="exam_audio"}}

<script type="text/javascript">

// Lancement du reload
window.opener.ExamDialog.reload('{{$exam_audio->consultation_id}}');

</script>

<form name="editFrm" action="?m=dPcabinet&amp;a=exam_audio&amp;dialog=1" method="post" onsubmit="return checkForm(this)">

<input type="hidden" name="m" value="dPcabinet" />
<input type="hidden" name="dosql" value="do_exam_audio_aed" />
<input type="hidden" name="del" value="0" />
{{mb_field object=$exam_audio field="examaudio_id" hidden=1 prop=""}}
{{mb_field object=$exam_audio field="consultation_id" hidden=1 prop=""}}

<table class="main" id="weber">

<tr>
  <th class="title" colspan="2">
    Consultation de <span style="color:#f00;">{{$exam_audio->_ref_consult->_ref_patient->_view}}</span>
    le {{$exam_audio->_ref_consult->_date|date_format:"%A %d/%m/%Y"}}
    par le Dr {{$exam_audio->_ref_consult->_ref_chir->_view}}
  </th>
</tr>
  
<tr>
  <th class="title" colspan="2">Audiométrie tonale (Test de Weber)</th>
</tr>

<tr>
	<th>
		<span style="float: left; padding-left: 30px;">Grave</span>
		<span style="float: right; padding-right: 30px;">Aigu</span>
	</th>
  <th>
      <span style="float: left; padding-left: 30px;">Grave</span>
    <span style="float: right; padding-right: 30px;">Aigu</span>
  </th>
</tr>
<tr>
  <td id="td_graph_tonal_droite" class="halfPane" style="height: 250px;">
    {{$map_tonal_droite|smarty:nodefaults}}    
    <img id="tonal_droite" src="?m=dPcabinet&amp;a=graph_audio_tonal&amp;suppressHeaders=1&amp;examaudio_id={{$exam_audio->_id}}&amp;side=droite&amp;time={{$time}}" usemap="#graph_tonal_droite" onclick="changeTonalValueMouseDroite(event)" alt="Audio tonal gauche" />
  </td>
  <td id="td_graph_tonal_gauche" class="halfPane" style="height: 250px;">
    {{$map_tonal_gauche|smarty:nodefaults}}
    <img id="tonal_gauche" src="?m=dPcabinet&amp;a=graph_audio_tonal&amp;suppressHeaders=1&amp;examaudio_id={{$exam_audio->_id}}&amp;side=gauche&amp;time={{$time}}" usemap="#graph_tonal_gauche" onclick="changeTonalValueMouseGauche(event)" alt="Audio tonal droite" />
  </td>
</tr>
<tr>
  <td class="radiointeractive" colspan="2">
    <input type="radio" name="_conduction" value="aerien" {{if $_conduction == "aerien"}}checked="checked"{{/if}} />
    <label for="_conduction_aerien" title="Conduction aérienne pour la saisie intéractive">Conduction aérienne</label>
    <input type="radio" name="_conduction" value="osseux" {{if $_conduction == "osseux"}}checked="checked"{{/if}} />
    <label for="_conduction_osseux" title="Conduction osseuse pour la saisie intéractive">Conduction osseuse</label>
    <input type="radio" name="_conduction" value="ipslat" {{if $_conduction == "ipslat"}}checked="checked"{{/if}} />
    <label for="_conduction_ipslat" title="Stapédien ipsilatéral pour la saisie intéractive">Stapédien ipsilatéral</label>
    <input type="radio" name="_conduction" value="conlat" {{if $_conduction == "conlat"}}checked="checked"{{/if}} />
    <label for="_conduction_conlat" title="Stapédien controlatéral pour la saisie intéractive">Stapédien controlatéral</label>
    <input type="radio" name="_conduction" value="pasrep" {{if $_conduction == "pasrep"}}checked="checked"{{/if}} />
    <label for="_conduction_pasrep" title="Pas de réponse pour la saisie intéractive">Pas de réponse</label>
  </td>
</tr>
<tr>
  <td colspan="2">

    <table class="form" id="allvalues">
      <tr id="dataTonal-trigger">
        <th class="category" colspan="9">Toutes les valeurs</th>
      </tr>
      <tbody id="dataTonal">
        <tr>
          <th class="category" colspan="9">Oreille droite</th>
        </tr>
        <tr>
          <th>Conduction aérienne</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <td><input type="text" name="_droite_aerien[{{$index}}]" class="num min|-10 max|120" value="{{$exam_audio->_droite_aerien.$index}}" tabindex="{{$index+110}}" size="4" maxlength="4" /></td>
          {{/foreach}}
        </tr>
        
        <tr>
          <th>Conduction osseuse</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <td><input type="text" name="_droite_osseux[{{$index}}]" class="num min|-10 max|120" value="{{$exam_audio->_droite_osseux.$index}}" tabindex="{{$index+120}}" size="4" maxlength="4" /></td>
          {{/foreach}}
        </tr>
  
        <tr>
          <th>stapédien ipsilatéral</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <td><input type="text" name="_droite_ipslat[{{$index}}]" class="num min|-10 max|120" value="{{$exam_audio->_droite_ipslat.$index}}" tabindex="{{$index+130}}" size="4" maxlength="4" /></td>
          {{/foreach}}
        </tr>
  
        <tr>
          <th>stapédien controlatéral</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <td><input type="text" name="_droite_conlat[{{$index}}]" class="num min|-10 max|120" value="{{$exam_audio->_droite_conlat.$index}}" tabindex="{{$index+140}}" size="4" maxlength="4" /></td>
          {{/foreach}}
        </tr>
  
        <tr>
          <th>pas de réponse</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <td><input type="text" name="_droite_pasrep[{{$index}}]" class="num min|-10 max|120" value="{{$exam_audio->_droite_pasrep.$index}}" tabindex="{{$index+150}}" size="4" maxlength="4" /></td>
          {{/foreach}}
        </tr>
  
        <tr>
          <th class="category">Fréquences</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <th class="category">
            {{$frequence}}
          </th>
          {{/foreach}}
        </tr>
        <tr>
          <th class="category" colspan="9">Oreille gauche</th>
        </tr>
        <tr>
          <th>Conduction aérienne</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <td><input type="text" name="_gauche_aerien[{{$index}}]" class="num min|-10 max|120" value="{{$exam_audio->_gauche_aerien.$index}}" tabindex="{{$index+10}}" size="4" maxlength="4" /></td>
          {{/foreach}}
        </tr>
        
        <tr>
          <th>Conduction osseuse</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <td><input type="text" name="_gauche_osseux[{{$index}}]" class="num min|-10 max|120" value="{{$exam_audio->_gauche_osseux.$index}}" tabindex="{{$index+20}}" size="4" maxlength="4" /></td>
          {{/foreach}}
        </tr>
  
        <tr>
          <th>Stapédien ipsilatéral</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <td><input type="text" name="_gauche_ipslat[{{$index}}]" class="num min|-10 max|120" value="{{$exam_audio->_gauche_ipslat.$index}}" tabindex="{{$index+30}}" size="4" maxlength="4" /></td>
          {{/foreach}}
        </tr>
  
        <tr>
          <th>Stapédien controlatéral</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <td><input type="text" name="_gauche_conlat[{{$index}}]" class="num min|-10 max|120" value="{{$exam_audio->_gauche_conlat.$index}}" tabindex="{{$index+40}}" size="4" maxlength="4" /></td>
          {{/foreach}}
        </tr>
  
        <tr>
          <th>Pas de réponse</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <td><input type="text" name="_gauche_pasrep[{{$index}}]" class="num min|-10 max|120" value="{{$exam_audio->_gauche_pasrep.$index}}" tabindex="{{$index+50}}" size="4" maxlength="4" /></td>
          {{/foreach}}
        </tr>
  
        <tr>
          <td class="button" colspan="9">
            <button class="submit" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete : reloadGraphTonale})">{{tr}}Save{{/tr}}</button>
          </td>
        </tr>
      </tbody>
    </table>
  </td>
</tr>

<tr>
  <th class="title" colspan="2">Bilan comparé</th>
</tr>

<tr>
  <td colspan="2" id="td_bilan">
    {{include file="inc_exam_audio/inc_examaudio_bilan.tpl"}}
  </td>
</tr>

<tr>
  <td colspan="2">
    <table style="width: 100%">
      <tr>
        <th class="title"><a name="vocal"></a>Audiométrie vocale</th>
        <th class="title"><a name="tympan"></a>Tympanométrie</th>
      </tr>
      <tr>
        <td id="td_graph_vocal" style="height:318px;width:534px;">
          {{$map_vocal|smarty:nodefaults}}
          <img id="image_vocal" src="?m=dPcabinet&amp;a=graph_audio_vocal&amp;suppressHeaders=1&amp;examaudio_id={{$exam_audio->_id}}&amp;time={{$time}}" usemap="#graph_vocal" onclick="changeVocalValueMouse(event)" alt="Audiogramme vocal" />
        </td>
        
        <td rowspan="2">
          <table style="width: 100%">
            <tr>
              <td id="td_graph_tympan_droite" style="height:176px;">
                {{$map_tympan_droite|smarty:nodefaults}}
                <img id="tympan_droite" src="?m=dPcabinet&amp;a=graph_audio_tympan&amp;suppressHeaders=1&amp;examaudio_id={{$exam_audio->_id}}&amp;side=droite&amp;time={{$time}}" usemap="#graph_tympan_droite" onclick="changeTympanValueMouseDroite(event)" alt="Tympan droit" />
              </td>
            </tr>
            <tr>
              <td id="td_graph_tympan_gauche" rowspan="2" style="height:162px;">
                {{$map_tympan_gauche|smarty:nodefaults}}
                <img id="tympan_gauche" src="?m=dPcabinet&amp;a=graph_audio_tympan&amp;suppressHeaders=1&amp;examaudio_id={{$exam_audio->_id}}&amp;side=gauche&amp;time={{$time}}" usemap="#graph_tympan_gauche" onclick="changeTympanValueMouseGauche(event)" alt="Tympa Gauche" />
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="radiointeractive" style="height:20px;width:534px;">
          <input type="radio" name="_oreille" value="gauche" {{if $_oreille == "gauche"}}checked="checked"{{/if}} />
          <label for="_oreille_gauche" title="Oreille gauche pour la saisie intéractive">Oreille gauche</label>
          <input type="radio" name="_oreille" value="droite" {{if $_oreille == "droite"}}checked="checked"{{/if}} />
          <label for="_oreille_droite" title="Oreille gauche pour la saisie intéractive">Oreille droite</label>
        </td>
      </tr>
    </table>
  </td>
</tr>

<tr>
  <td colspan="2" class="radiointeractive">
    <table class="form" id="allvocales">
      <tr id="dataVocal-trigger">
        <th class="category" colspan="9">Toutes les valeurs</th>
      </tr>
      <tbody id="dataVocal">
        <tr>
          <th class="category">Audiométrie vocale</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <th class="category">
            Point #{{$index}}<br />dB / %
          </th>
          {{/foreach}}
        </tr>
        <tr>
          <th>Oreille droite</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <td>
            <input type="text" name="_droite_vocale[{{$index}}][0]" class="num min|0 max|120" value="{{$exam_audio->_droite_vocale.$index.0}}" tabindex="{{$index*2+220}}" size="1" maxlength="3" />
            <input type="text" name="_droite_vocale[{{$index}}][1]" class="num min|0 max|100" value="{{$exam_audio->_droite_vocale.$index.1}}" tabindex="{{$index*2+221}}" size="1" maxlength="3" />
          </td>
          {{/foreach}}
        </tr>
        <tr>
          <th>Oreille gauche</th>
          {{foreach from=$frequences|smarty:nodefaults key=index item=frequence}}
          <td>
            <input type="text" name="_gauche_vocale[{{$index}}][0]" class="num min|0 max|120" value="{{$exam_audio->_gauche_vocale.$index.0}}" tabindex="{{$index*2+200}}" size="1" maxlength="3" />
            <input type="text" name="_gauche_vocale[{{$index}}][1]" class="num min|0 max|100" value="{{$exam_audio->_gauche_vocale.$index.1}}" tabindex="{{$index*2+201}}" size="1" maxlength="3" />
          </td>
          {{/foreach}}
        </tr>
  
        <tr>
          <th class="category">Tympanométrie</th>
          {{foreach from=$pressions|smarty:nodefaults item=pression}}
          <th class="category">
            {{$pression}} mm H²O
          </th>
          {{/foreach}}
        </tr>
        <tr>
          <th>Oreille droite</th>
          {{foreach from=$pressions|smarty:nodefaults key=index item=pression}}
          <td><input type="text" name="_droite_tympan[{{$index}}]" class="num min|-10 max|120" value="{{$exam_audio->_droite_tympan.$index}}" tabindex="{{$index+310}}" size="4" maxlength="4" /></td>
          {{/foreach}}
        </tr>
        <tr>
          <th>Oreille gauche</th>
          {{foreach from=$pressions|smarty:nodefaults key=index item=pression}}
          <td><input type="text" name="_gauche_tympan[{{$index}}]" class="num min|-10 max|120" value="{{$exam_audio->_gauche_tympan.$index}}" tabindex="{{$index+300}}" size="4" maxlength="4" /></td>
          {{/foreach}}
        </tr>
  
        <tr>
          <td class="button" colspan="9">
            {{if $exam_audio->examaudio_id}}
            <button class="submit" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete : reloadGraphTympan})">{{tr}}Save{{/tr}}</button>
            {{else}}
            <button class="submit" type="submit">{{tr}}Save{{/tr}}</button>
            {{/if}}
          </td>
        </tr>
      </tbody>

    </table>
  </td>
</tr>
<tr>
  <td colspan="2">
    <table class="form">
      <tr>
        <th class="category">Remarques</th>
      </tr>
      <tr>
        <td style="text-align:left;">
          <select name="_helpers_remarques" size="1" onchange="pasteHelperContent(this);" class="not-printable helper">
            <option value="">&mdash; Aide</option>
            {{html_options options=$exam_audio->_aides.remarques.no_enum}}
          </select>
          <button class="new notext not-printable" title="Ajouter une aide à la saisie" type="button" onclick="addHelp('CExamAudio', this.form.remarques)">{{tr}}New{{/tr}}</button><br />
          <textarea name="remarques" rows="2">{{$exam_audio->remarques}}</textarea>
        </td>
      </tr>
      <tr>
        <td class="button radiointeractive">
          <button class="submit" type="button" onclick="submitFormAjax(this.form, 'systemMsg', { onComplete: reloadAllGraphs })">{{tr}}Save{{/tr}}</button>
        </td>
      </tr>
    </table>
  </td>
</tr>
</table>
</form>