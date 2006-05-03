var debugWindow = null;

function debug(s, name) {
    if (!debugWindow) {
        debugWindow = window.open("", "debugWindow", "width=400,height=500,scrollbars=yes,resizable=yes");
        debugWindow.document.write("<pre>");
    }
    
    if (name) {
        debugWindow.document.write('<div style="font: 12px sans-serif; font-weight: bold;">'+name+'</div>');
    }
    
    debugWindow.document.write(s + "\n");
}

function debugObject(oObject, sName) {
	sInfo = "";

	for (var sPropName in oObject) {
		var oProp = oObject[sPropName];
		var sType = typeof oProp;
		
		sInfo += "\nProperty." + sPropName + " = ";

		if (oProp == null) {
			sInfo += "null";
			continue;
		}
		
		if ((sType == "object" || sType == "function") && oProp.toString) {
			sInfo += "[[ " + sType + " ]]";
        } else {
			sInfo += oProp;
        }
    }

    debug(sInfo, sName);
}

function debugArray(arr, name) {
    var s = '';
    for (var i = 0; i < arr.length; ++i) {
        s += "Array[" + i + "]=" + arr[i] + "\n";
    }
    
    debug(s, name);
}