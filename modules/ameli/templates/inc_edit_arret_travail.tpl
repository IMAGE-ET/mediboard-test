{{*
 * $Id$
 *  
 * @package    Mediboard
 * @subpackage ameli
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    OXOL, see http://www.mediboard.org/public/OXOL
 * @version    $Revision$
 * @link       http://www.mediboard.org
*}}

<script type="text/javascript">
  getFullAATHistory = function(patient_id) {
    var url = new Url('ameli', 'ajax_get_aat_history');
    url.addParam('patient_id', patient_id);
    url.requestModal();
  };

  toggleSelectDuree = function(motif_id) {
    if (motif_id) {
      $('btn_select_duree').enable();
    }
    else {
      $('btn_select_duree').disable();
    }
  };

  setDureeIndicative = function(duree) {
    duree = duree.split('|');
    var form = getForm('formArretTravail');
    $V(form.duree, duree[0]);
    $V(form.unite_duree, duree[1]);
  };

  resetMotif = function() {
    var form = getForm('formArretTravail');
    $V(form.libelle_motif, '');
    $V(form.motif_id, '');
    toggleSelectDuree(0);
  };

  updateMaxDuree = function() {
    var form = getForm('formArretTravail');
    var unite = $V(form.unite_duree);
    if ($V(form.type) == 'prolongation') {
      switch (unite) {
        case 'j':
          form.duree.max = 182;
          break;
        case 'm':
          form.duree.max = 6;
          break;
        case 'y':
          form.duree.max = 0;
          break;
        default:
      }
    }
    else {
      switch (unite) {
        case 'j':
          form.duree.max = 1092;
          break;
        case 'm':
          form.duree.max = 36;
          break;
        case 'y':
          form.duree.max = 3;
          break;
        default:
      }
    }
  };

  updateEndDate = function() {
    var form = getForm('formArretTravail');
    var begin_date = $V(form.debut);
    var unite_duree = $V(form.unite_duree);
    var duree = parseInt($V(form.duree));
    var year = parseInt(begin_date.substr(0, 4));
    var month = parseInt(begin_date.substr(5, 2));
    var day = parseInt(begin_date.substr(8, 2));
    var end_date = new Date(year, month, day);

    switch (unite_duree) {
      case 'j':
          day = day + duree;
          end_date.setDate(day);
          break;
        case 'm':
          month = month + duree;
          end_date.setMonth(month);
          break;
        case 'y':
          year = year + duree;
          end_date.setFullYear(year);
          break;
    }

    month = end_date.getMonth() + "";
    if (month.length == 1) {
      month = "0" + month;
    }
    day = end_date.getDate() + "";
    if (day.length == 1) {
      day = "0" + day;
    }

    $V(form.fin, end_date.getFullYear() + '-' + month + '-' + day);
    $V(form.fin_da, day + '/' + month + '/' + end_date.getFullYear());
  };

  displayDureeIndicative = function() {
    var url = new Url('ameli', 'ajax_duree_indicative_arret_travail');
    url.addParam('motif_code', $V(getForm('formArretTravail').motif_id));
    url.requestModal(null, null, {onClose: function() {
      setDureeIndicative($V(getForm('formDureeIndicative').duree_indicative));
    }});
  };

  submitArretTravail = function(form) {
    Control.Modal.close();
    return onSubmitFormAjax(form);
  };

  Main.add(function() {
    var url = new Url('ameli', 'ajax_motif_arret_travail_autocomplete');
    var form = getForm('formArretTravail');
    url.autoComplete(getForm('formArretTravail').libelle_motif, 'motif_autocomplete', {
      minChars: 3,
      updateElement: function(selected) {
        var form = getForm('formArretTravail');
        $V(form.motif_id, selected.down('.motif').get('code'));
        $V(form.libelle_motif, selected.down('.motif').get('libelle'));
      }
    });

    Calendar.regField(form.debut);
    Calendar.regField(form.date_accident);
    form.duree.addSpinner({min: 1, step: 1});
  });
</script>

{{if $aat_history}}
  {{mb_include module=ameli template=inc_full_aat_history}}
{{/if}}
<form name="formArretTravail" action="?" method="post" onsubmit="return submitArretTravail(this);">
  {{mb_class object=$arret_travail}}
  {{mb_key object=$arret_travail}}
  <input type="hidden" name="del" value="0"/>
  <input type="hidden" name="consult_id" value="{{$arret_travail->consult_id}}"/>
  <input type="hidden" name="patient_id" value="{{$arret_travail->patient_id}}"/>

  <table class="form">
    <tr>
      <th class="title" colspan="2">
        {{if $arret_travail->_id}}
          {{tr}}CAvisArretTravail-title-modify{{/tr}}
        {{else}}
          {{tr}}CAvisArretTravail-title-create{{/tr}}
        {{/if}}
      </th>
    </tr>
    <tr>
      <th>{{mb_label object=$arret_travail field=libelle_motif}}</th>
      <td>
        {{mb_field object=$arret_travail field=motif_id hidden=1 onchange="toggleSelectDuree(this.value);"}}
        {{mb_field object=$arret_travail field=libelle_motif}}
        <button type="button" onclick="resetMotif();" class="cancel notext" title="">{{tr}}Reset{{/tr}}</button>
        <div style="display: none; width: 300px;" class="autocomplete" id="motif_autocomplete"></div>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$arret_travail field=type}}</th>
      <td>{{mb_field object=$arret_travail field=type typeEnum=select onchange="updateMaxDuree();"}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$arret_travail field=accident_tiers}}</th>
      <td>{{mb_field object=$arret_travail field=accident_tiers onchange="\$('line_date_accident').toggle();"}}</td>
    </tr>
    <tr id="line_date_accident" {{if !$arret_travail->accident_tiers}}style="display: none;"{{/if}}>
      <th>{{mb_label object=$arret_travail field=date_accident}}</th>
      <td>{{mb_field object=$arret_travail field=date_accident}}</td>
    </tr>
    <tr>
      <th>{{mb_label object=$arret_travail field=debut}}</th>
      <td>{{mb_field object=$arret_travail field=debut}}</td>
    </tr>
    <tr>
      <th><label for="duree" title="{{tr}}Duration{{/tr}}">{{tr}}Duration{{/tr}}</label></th>
      <td>
        <input type="number" class="num" name="duree" size="4" min="1" max="1092" onchange="updateEndDate();" {{if $arret_travail->_duree}}value="{{$arret_travail->_duree}}"{{/if}}/>
        <select name="unite_duree" onchange="updateMaxDuree(); updateEndDate();">
          <option value="j" {{if $arret_travail->_unite_duree && $arret_travail->_unite_duree == 'j'}}selected="selected"{{/if}}>{{tr}}Day{{/tr}}</option>
          <option value="m" {{if $arret_travail->_unite_duree && $arret_travail->_unite_duree == 'm'}}selected="selected"{{/if}}>{{tr}}Month{{/tr}}</option>
          <option value="y" {{if $arret_travail->_unite_duree && $arret_travail->_unite_duree == 'y'}}selected="selected"{{/if}}>{{tr}}Year{{/tr}}</option>
        </select>
        <button id="btn_select_duree" class="search notext" type="button" onclick="displayDureeIndicative();" title="{{tr}}seeDureeIndicative{{/tr}}" {{if !$arret_travail->motif_id}}disabled="disabled"{{/if}}>{{tr}}seeDureeIndicative{{/tr}}</button>
      </td>
    </tr>
    <tr>
      <th>{{mb_label object=$arret_travail field=fin}}</th>
      <td>{{mb_field object=$arret_travail field=fin readonly="readonly"}}</td>
    </tr>
    <tr>
      <td colspan="2" style="text-align: center">
        {{if $arret_travail->_id}}
          <button type="submit" class="save">{{tr}}Save{{/tr}}</button>
          <button type="submit" class="trash" onclick="$V(getForm('formArretTravail').del, 1);">{{tr}}Delete{{/tr}}</button>
        {{else}}
          <button type="submit" class="new">{{tr}}Create{{/tr}}</button>
        {{/if}}
      </td>
    </tr>
  </table>
</form>