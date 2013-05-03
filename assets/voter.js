var voter = {
	url: '',
	open_window: function ( target )
	{
		sunwindow = window.open('', 'suncore', 'scrollbars=yes,menubar=0,height=700,width=1000,toolbar=0,status=0');
		sunwindow.document.write('<img src="http://static.suncore.lv/img/loader-big.gif" style="display:block;margin:250px auto 0 auto"alt="" />');
		sunwindow.document.location = target;
		sunwindow.moveTo(200, 200);
	},
	setCookie: function(c_name,value,exdays)
	{
		var exdate=new Date();
		exdate.setDate(exdate.getDate() + exdays);
		var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
		document.cookie=c_name + "=" + c_value;
	}

};
$(document).ready(function()
{
	$('.voter-link a').click(function()
	{
		if($(this).parent().hasClass('voted'))
		{
			alert('Jau balsots!');
			return false;
		}
		
		voter.open_window( voter.url + '/?target=' + $(this).attr('data-site'));
	});
});