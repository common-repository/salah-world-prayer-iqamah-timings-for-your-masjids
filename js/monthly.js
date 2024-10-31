var iarray=(jQuery).parseJSON(monthly_settings.array);

var now = new Date();
	var currentDate = new Date();
	var method = monthly_settings.method;
	var lat = monthly_settings.lat ;
	var lng = monthly_settings.long;
	var timeZone = monthly_settings.timeZone;
	var dst = 0;
	var timeFormat = 1;
		

	var icount=0;


	update();
		
	function displayMonth(offset) {
		jQuery("#tbodyid").empty();
		prayTimes.setMethod(method);
		currentDate.setMonth(currentDate.getMonth()+ 1* offset);
		var month = currentDate.getMonth();
		var year = currentDate.getFullYear();
		var title = monthFullName(month)+ " "+ year;
		document.getElementById("table-title").innerHTML =title;

		getTimes(year, month, lat, lng, timeZone, dst);



	}

	function getTimes(year, month, lat, lng, timeZone, dst)
	{
		var iarray=(jQuery).parseJSON(monthly_settings.array);

var iqString = [];		
var cDate= new Date(year, month, 1);
for (i = 0; i < iarray.length; i++) {
	var da= iarray[i].field_1.split("-");
	var oDate= new Date(da[0], da[1], da[2]);   
	
	oDate.setMonth(oDate.getMonth() - 1);
	
	if(oDate>=cDate){
		
		iqString.push(iarray[i]);
		oDate.setDate(oDate.getDate() - 1);
		
		//iqString[i].field_1 = oDate.getYear() + "-" + oDate.getMonth() + "-" + oDate.getDate();
	}
}

makeTable(year, month, lat, lng, timeZone, dst,iqString);
return;

	}
	
	function makeTable(year, month, lat, lng, timeZone, dst,iqString) {
		var items = {day: "Day", fajr: "Fajr", sunrise: "Sunrise",
					zuhr: "Dhuhr", asr: "Asr", 
					magrib: "Maghrib", isha: "Isha"};
		var tableRef = document.getElementById("myTable").getElementsByTagName("tbody")[0];
		var date = new Date(year, month, 1);
		var endDate = new Date(year, month+ 1, 1);
		var format = timeFormat ? "12hNS" : "24h";
		icount = 0;
		while (date < endDate) {
			var times = prayTimes.getTimes(date, [lat, lng], timeZone, dst, format);
			times.day = date.getDate();
			var today = new Date();
			var isToday = (date.getMonth() == today.getMonth()) && (date.getDate() == today.getDate());
			var klass = isToday ? "today-row" : "";
            var newRow   = tableRef.insertRow(tableRef.rows.length);
			var xa=0;	
			
			for (var i in items) {
				// Insert a cell in the row at index 0
				var newCell  = newRow.insertCell(xa);

				// Append a text node to the cell
				if(i=="day")
					var newText  = document.createTextNode(times[i] + " "+dayFullName(date.getDay()));
				else
					var newText  = document.createTextNode(times[i]);

				// Append a text node to the cell
				newCell.appendChild(newText);
				xa++;
				
				if(i=="day")
					newCell.className = "koo";
				if(klass)
					newCell.className = klass;
			
				if(i!="day" && i!="sunrise" ){
					if(typeof iqString[icount] != "undefined") {
					if(i=="fajr") {
						var date1 = date;
						var date2 = new Date(iqString[icount]["field_1"]);
						date2.setDate(date2.getDate() + 1);
						var diffDays = date2.getDate()+2 - date1.getDate()-1;
						
						if(diffDays == 0)
							icount++;
						
						
					}}
					if(typeof iqString[icount] != "undefined") {
						
					if(i=="magrib") {
						if(iqString[icount][i] == 0) {
							newCell.setAttribute("colspan", 2);
						}
						else if(iqString[icount][i].indexOf(":") > -1) {
							//newCell.style.textAlign = "right";
							var newCell  = newRow.insertCell(xa);					
							var newText  = document.createTextNode(iqString[icount][i]);

							newCell.appendChild(newText);

							newCell.className = "long";
							if(klass)
								newCell.className += klass;
							//else
								//newCell.style.paddingTop = "3%";

							xa++;
						}
						else {
							var res = times[i].split(":");
							res[1]=parseInt(res[1])+parseInt(iqString[icount][i]);
							
							if(parseInt(res[1])>59) {
								res[1] = parseInt(res[1]) - 60;
								res[0] = parseInt(res[0]) + 1;
							}
							//newCell.style.textAlign = "right";
							var newCell  = newRow.insertCell(xa);
							var newText  = document.createTextNode(res[0] + ":" + ("0" + res[1]).slice(-2));
							newCell.appendChild(newText);
							newCell.className = "long";
							if(klass)
								newCell.className += klass;
							//else
								//newCell.style.paddingTop = "3%";
							
							xa++;							 
						}
						
					} 
					else {			
										
						var newCell  = newRow.insertCell(xa);					
						var newText  = document.createTextNode(iqString[icount][i]);
						
						newCell.appendChild(newText);
						
							newCell.className = "long";
						if(klass)
							newCell.className += klass;
						
						xa++;                                                                                                                                                  
					}					
				}
				else {
				newCell.setAttribute("colspan", 2);
			}
			}
			
			}
			if ((jQuery(newRow).find("td.long").length) || (jQuery(newRow).find("td.longtoday-row").length))
				newRow.className += "iRow";
			

			if(klass) {
				jQuery("td[rowspan]").slice(-4).addClass(" today-row");
			}
			
			date.setDate(date.getDate()+ 1);  // next day
        }
	}
	function update() {
		displayMonth(0);

	}

	// return month full name
	function monthFullName(month) {
		var monthName = new Array("January", "February", "March", "April", "May", "June",
						"July", "August", "September", "October", "November", "December");
		
		return monthName[month];
	}
	
	// return day full name
	function dayFullName(month) {
		var days = ["Sun","Mon","Tues","Wed","Thur","Fri","Sat"];

		return days[month];
	}
	