<table class="main">
  <tr>
    <td class="halfPane">
       <form name="editcipitem" action="?m=sip&amp;a=mbCip&amp;suppressHeaders=1" method="post" onsubmit="return checkForm(this)">
	        <table class="form">
	       <tr>
	         <th class="title" colspan="2">Identification</th>
	       </tr>
	       <tr>
	         <th> Utilisateur </th> 
	         <td> <input type="text" name="username" /></td>
	       </tr>
	       <tr>
					<th><label for="_user_password" title="Saisir le mot de passe. Obligatoire">Mot de passe</label></th>
					<td><input  type="password" name="_user_password" value="" onkeyup="checkFormElement(this)" />
					<span id="editFrm__user_password_message"></span>
					</td>
			   </tr>
	       <tr>
	         <td class="button" colspan="2">
	           <button class="submit" type="submit">Valider</button>
	         </td>
	       </tr>        
	     </table>
	    </form>
    </td>
  </tr>
</table>