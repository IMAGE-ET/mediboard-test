{{if $selOp->_ref_documents|@count}}
   <table class="tbl">
     <tr id="operation{{$selOp->_id}}-trigger">
       <th class="category" colspan="2">{{$selOp->_ref_documents|@count}} document(s)</th>
     </tr>
     <tbody class="operationEffect" id="operation{{$selOp->_id}}" style="display:none;">
     {{foreach from=$selOp->_ref_documents item=document}}
     <tr>
       <td>{{$document->nom}}</td>
       <td class="button">
         <form name="editDocumentFrm{{$document->compte_rendu_id}}" action="?m={{$m}}" method="post">
         <input type="hidden" name="m" value="dPcompteRendu" />
         <input type="hidden" name="del" value="0" />
         <input type="hidden" name="dosql" value="do_modele_aed" />
         <input type="hidden" name="object_id" value="{{$selOp->_id}}" />
         {{mb_field object=$document field="compte_rendu_id" hidden=1 prop=""}}
         <button class="edit notext" type="button" onclick="Document.edit({{$document->compte_rendu_id}})">Edition du document
         </button>
         <button class="trash notext" type="button" onclick="confirmDeletion(this.form, {typeName:'le document',objName:'{{$document->nom|smarty:nodefaults|JSAttribute}}',ajax:1,target:'systemMsg'},{onComplete: function(){ Document.refreshList({{$selOp->_id}}) } })">
         Suppression du document
         </button>
         </form>
       </td>
     </tr>
     {{/foreach}}
     </tbody>
   </table>
 {{/if}}
 
 <script type="text/javascript">
      
        PairEffect.initGroup("operationEffect", { 
          bStoreInCookie: true
        });
      </script>
 