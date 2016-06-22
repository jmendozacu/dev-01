window.onload = function(e){
    if (!document.getElementById("row_amsmtp_smtp_autofill")) {
        return;
    }
    $$("#row_amsmtp_smtp_autofill > td")[2].hide();
    var inputProviders = document.getElementById("amsmtp_smtp_provider");
    var buttonSelector = document.getElementById("row_amsmtp_smtp_autofill").childNodes[1].childNodes[0];

    var iServer = document.getElementById("amsmtp_smtp_server");
    var iPort   = document.getElementById("amsmtp_smtp_port");
    var iSec    = document.getElementById("amsmtp_smtp_sec");
    var iAuth    = document.getElementById("amsmtp_smtp_auth");

    inputProviders.onchange = function(){
        if(inputProviders.value == 'other'){
            buttonSelector.hide();
            iServer.value = "";
            iPort.value = "";
            iSec.value = "";
            iAuth.value = "";
        } else {
            buttonSelector.show();
        }
    }
    buttonSelector.onclick = function(){
        var selectedOption = inputProviders.value;

        switch(selectedOption){
            case "aol":
                iServer.value = "smtp.de.aol.com";
                iPort.value = "587";
                iSec.value = "";
                iAuth.value = "login";
                break
            case "gmail":
                iServer.value = "smtp.gmail.com";
                iPort.value = "465";
                iSec.value = "ssl";
                iAuth.value = "login";
                break
            case "outlook":
                iServer.value = "smtp-mail.outlook.com";
                iPort.value = "587";
                iSec.value = "tls";
                iAuth.value = "login";
                break
            case "gmx":
                iServer.value = "mail.gmx.net";
                iPort.value = "587";
                iSec.value = "tls";
                iAuth.value = "login";
                break
            case "yahoo":
                iServer.value = "smtp.mail.yahoo.de";
                iPort.value = "465";
                iSec.value = "ssl";
                iAuth.value = "login";
                break
            case "zoho":
                iServer.value = "smtp.zoho.com";
                iPort.value = "465";
                iSec.value = "ssl";
                iAuth.value = "login";
                break
            case "mailcom":
                iServer.value = "smtp.mail.com";
                iPort.value = "465";
                iSec.value = "ssl";
                iAuth.value = "login";
                break
            case "office365":
                iServer.value = "smtp.office365.com";
                iPort.value = "587";
                iSec.value = "tls";
                iAuth.value = "login";
                break
            case "o2":
                iServer.value = "smtp.o2.ie";
                iPort.value = "25";
                iSec.value = "";
                iAuth.value = "login";
                break
            case "orange":
                iServer.value = "smtp.orange.net";
                iPort.value = "25";
                iSec.value = "";
                iAuth.value = "login";
                break
            case "hotmail":
                iServer.value = "smtp.live.com";
                iPort.value = "465";
                iSec.value = "ssl";
                iAuth.value = "login";
                break
            case "comcast":
                iServer.value = "smtp.comcast.net";
                iPort.value = "587";
                iSec.value = "";
                iAuth.value = "login";
                break
            case "other":
            default:
                iServer.value = "";
                iPort.value = "";
                iSec.value = "";
                iAuth.value = "";
        }
    };
}