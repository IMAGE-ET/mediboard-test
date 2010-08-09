{{* $Id: vw_extract_passages.tpl 7641 2009-12-17 10:50:38Z lryo $ *}}

{{*
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision: 7671 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
*}}

{{mb_include_script module="dPcabinet" script="file"}}

<script type="text/javascript">
  function popupEchangeViewer(extract_passages_id) {
    var url = new Url("dPurgences", "extract_viewer");
    url.addParam("extract_passages_id", extract_passages_id);
    url.popup(700, 550, "Message Passage");
    return false;
  }

  function encrypt(extract_passages_id) {
    var url = new Url("dPurgences", "ajax_encrypt_passages");
    url.addParam("extract_passages_id", extract_passages_id);
    url.addParam("view", 1);
    url.requestUpdate('file_passage_'+extract_passages_id);
  }
    
  function changePage(page) {
    $V(getForm('listFilter').page,page);
  }
</script>

<div>
  <strong>Total des RPUs extrait : {{$total_rpus}}</strong> 
</div>

<form name="listFilter" action="?m={{$m}}" method="get">
  <input type="hidden" name="m" value="{{$m}}" />
  <input type="hidden" name="tab" value="{{$tab}}" />
  <input type="hidden" name="page" value="{{$page}}" onchange="this.form.submit()"/>
        
  {{if $total_passages != 0}}
    {{mb_include module=system template=inc_pagination total=$total_passages current=$page change_page='changePage'}}
  {{/if}}
</form>
      
<table class="tbl">
  <tr>
    <th class="title" colspan="18">PASSAGES</th>
  </tr>
  <tr>
    <th>{{mb_title object=$extractPassages field="extract_passages_id"}}</th>
    <th>{{mb_title object=$extractPassages field="type"}}</th>
    <th>{{mb_title object=$extractPassages field="date_extract"}}</th>
    <th>{{mb_title object=$extractPassages field="debut_selection"}}</th>
    <th>{{mb_title object=$extractPassages field="fin_selection"}}</th>
    <th>{{mb_title object=$extractPassages field="_nb_rpus"}}</th>
    <th>{{mb_title object=$extractPassages field="date_echange"}}</th>
    <th>{{mb_title object=$extractPassages field="nb_tentatives"}}</th>
    <th>{{mb_title object=$extractPassages field="message_valide"}}</th>
    <th>Fichiers</th>
    <th colspan="3">{{tr}}Actions{{/tr}}</th>
  </tr>
  {{foreach from=$listPassages item=_passage}}
  <tr>
    <td style="width:0.1%">
      <a href="#1" onclick="return popupEchangeViewer('{{$_passage->_id}}')" class="button search">
       {{$_passage->_id|str_pad:6:'0':$smarty.const.STR_PAD_LEFT}}
      </a>
    </td>
    <td style="width:0.1%">
      {{mb_value object=$_passage field="type"}}
    </td>
    <td style="width:0.1%">
      <label title='{{mb_value object=$_passage field="date_extract"}}'>
        {{mb_value object=$_passage field="date_extract" format=relative}}
      </label>
    </td>
    <td style="width:0.1%">
      {{mb_value object=$_passage field="debut_selection"}}
    </td>
    <td style="width:0.1%">
      {{mb_value object=$_passage field="fin_selection"}}
    </td>
    <td style="width:0.1%" {{if $_passage->type != "rpu"}}class="arretee"{{/if}}>
      {{if $_passage->type == "rpu"}}
       {{mb_value object=$_passage field="_nb_rpus"}}
      {{/if}}
    </td>
    <td style="width:0.1%">
      <label title='{{mb_value object=$_passage field="date_echange"}}'>
        {{mb_value object=$_passage field="date_echange" format=relative}}
      </label>
    </td>
    <td style="width:0.1%" class="{{if $_passage->nb_tentatives > 5}}warning{{/if}}">
      {{mb_value object=$_passage field="nb_tentatives"}}
    </td>
    <td style="width:0.1%" class="{{if !$_passage->message_valide}}error{{/if}}">
      {{mb_value object=$_passage field="message_valide"}}
    </td>
    <td style="width:0.1%" id="file_passage_{{$_passage->_id}}">
      {{mb_include template=inc_extract_file}}       
    </td>
    <td style="width: 0.1%">
      <a target="blank" href="?m=dPurgences&a=download_echange&extract_passages_id={{$_passage->_id}}&dialog=1&suppressHeaders=1" class="button modify notext"></a>
    </td>
    <td style="width: 0.1%">
      <button class="tick" type="button" id="encrypt_rpu" onclick="encrypt({{$_passage->_id}})">Chiffrer</button>
    </td>
    <td style="width:0.1%">
      {{if $can->admin}} 
        <form name="Purge-{{$_passage->_guid}}" action="?m={{$m}}&amp;tab=vw_extract_passages" method="post" onsubmit="return confirmCreation(this)">
        <input type="hidden" name="dosql" value="do_extract_passages_aed" />
        <input type="hidden" name="tab" value="vw_extract_passages" />
        <input type="hidden" name="del" value="0" />
        <input type="hidden" name="_purge" value="0" />
        <input type="hidden" name="extract_passages_id" value="{{$_passage->_id}}" />
                
         <script type="text/javascript">
           confirmPurge{{$_passage->_id}} = function(form) {
             if (confirm("ATTENTION : Vous êtes sur le point de purger l'extraction d'un passage !")) {
               form._purge.value = "1";
               confirmDeletion(form,  {
                 typeName:'l\'extraction de passage',
                 objName:'{{$_passage->_view|smarty:nodefaults|JSAttribute}}'
               } );
             }
           }
         </script>
         <button type="button" class="cancel" onclick="confirmPurge{{$_passage->_id}}(this.form);">
           {{tr}}Purge{{/tr}}
         </button>
        </form>
      {{/if}}
    </td>
  </tr>
  {{foreachelse}}
  <tr>
    <td colspan="18">{{tr}}CExtractPassages.none{{/tr}}</td>
  </tr>
  {{/foreach}}
</table>