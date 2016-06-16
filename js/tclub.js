function formatMS2mmss(timeSlap)
{
	var ts = '';
	var m = Math.floor(timeSlap / 60000);
	if(m < 10)
	{
		ts += '0';
	}
	ts += m;
	ts += ':';
	var s = Math.floor((timeSlap % 60000) / 1000);
	if(s < 10)
	{
		ts += '0';
	}
	ts += s;
	return ts;
}
