/*////////
//////////	THIS FILE SIMPLY DISPLAYS A LIVE DATE/TIME TO THE USER /////////////
*/////////

function startTime() {
    var today = new Date();
	var dd = today.getDate();
	var days = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"]; 
	var MM = today.getMonth()+ 1; //January is 0!
	var yyyy = today.getFullYear();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
	
	
	// add a '0' before month number if month is < Nov
	
	if(dd < 10) {
		dd='0'+dd
	} 

	if(MM < 10) {
		MM='0'+MM
	} 

    document.getElementById('datetime').innerHTML =
    today = days[today.getDay()] + ' ' + dd+'/'+MM+'/'+yyyy + ' '+ h + ":" + m + ":" + s;
    var t = setTimeout(startTime, 500);
}
function checkTime(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}