
$(document).ready(function(){
	$('input:eq(0)').focus();
	$('#formConfig').submit(function(){
		if($('input[name="db_host"]').val().length==0){
			alert('You must fill host name');
			$('input[name="db_host"]').focus();
			return false;
		}
		if($('input[name="db_username"]').val().length==0){
			alert('You must fill username');
			$('input[name="db_username"]').focus();
			return false;
		}
		if($('input[name="db_dbname"]').val().length==0){
			alert('You must fill db name');
			$('input[name="db_dbname"]').focus();
			return false;
		}
		$.ajax({
			url:$('#formConfig').attr('action'),
			data:'act=checkconnection&'+$('#formConfig').serialize(),
			type:'post',
			dataType:'json',
			beforeSend:function(){
				$('#msg').html('');
			},
			success:function(json){
				if(json.stat=='success'){
					location.href='/';
				}else{
					$('table#dbconnection').effect("highlight",{},3000);
					$('#msg').html(json.msg);
				}
			}
		});
		return false;
	});
});