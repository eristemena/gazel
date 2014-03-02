var menu;
var calendar = null;

function selected(cal, date) {
	cal.sel.value = date;
}
function closeHandler(cal) {
	cal.hide();
	Calendar.removeEvent(document, "mousedown", checkCalendar);
}
function checkCalendar(ev) {
	var el = Calendar.is_ie ? Calendar.getElement(ev) : Calendar.getTargetElement(ev);
	for (; el != null; el = el.parentNode)
	if (el == calendar.element || el.tagName == "A") break;
	if (el == null) {
		calendar.callCloseHandler(); Calendar.stopEvent(ev);
	}
}
function showCalendar(id) {
	var el = document.getElementById(id);
	if (calendar != null) {
		calendar.hide();
		calendar.parseDate(el.value);
	} else {
		var cal = new Calendar(true, null, selected, closeHandler);
		calendar = cal;
		cal.setRange(1900, 2070);
		calendar.create();
		calendar.parseDate(el.value);
	}
	calendar.sel = el;
	calendar.showAtElement(el);
	
	Calendar.addEvent(document, "mousedown", checkCalendar);
	return false;
}
function initMenu(){
	menu = new dhtmlXMenuObject("menu", "dhx_blue");
	menu.setImagePath(baseurl+"/assets/js/dhtmlx/dhtmlxMenu/imgs/");
	menu.loadXML(menulink);
	menu.attachEvent('onClick',function(id){
		var n=id.split('|');
		if(n.length>1){
			location.href=n[1];
		}
	});
}
function altTable(){
	$('table.tadmin tbody tr:even').addClass('row1');
}
function admin_cb(){
	$('input#cbcall').click(function(){
		if($(this).is(':checked')){
			$('input[type="checkbox"][name="cb[]"]').each(function(){
				$(this).attr('checked','checked');
			});
		}else{
			$('input[type="checkbox"][name="cb[]"]').each(function(){
				$(this).attr('checked','');
			});
		}
	});
}
function tableSortby(id,type){
	$.ajax({
		url:orderlink,
		type:'post',
		data:'id='+id+'&type='+type,
		success:function(){
			location.href=indexlink;
		}
	});
}
function addInpDateBut(el){
	el.after('<input id="search_butt_cal" type="button" value="..." onClick="return showCalendar(\'search_keyword\', \'dd-mm-y\');" />');
}
function admin_table(){
	var lh=$('table.tadmin th').length;
	if($('table.tadmin tbody tr').length==0){
		$('table.tadmin tbody').append('<tr><td colspan="'+lh+'">No Data Available</td></tr>');
	}
	$('table.tadmin tfoot td').each(function(){
		$(this).attr('colspan',lh);
	});
	$('table.tadmin th').each(function(){
		if ($(this).attr('sort')){
			if ( $(this).attr('order') ) {
				if ( $(this).attr('order')=='asc' ) {
					type='desc';
				} else {
					type='asc';
				}
			} else {
				type='asc';
			}
			$(this).wrapInner('<a href="javascript:tableSortby(\''+$(this).attr('sort')+'\',\''+type+'\')"></a>');
		}
	});
	altTable();
	$('#searchForm').submit(function(){
		if ( $(this).find('input[name="keyword"]').val().length==0 ) {
			alert('Please specify what you are looking for!');
			return false;
		}
		var data=$(this).serialize();
		$.ajax({
			url:searchlink,
			type:'post',
			data:data,
			success:function(){
				location.href=indexlink;
			}
		});
		return false;
	});
	$('#searchForm').find('input.clearsearch').click(function(){
		$.ajax({
			url:searchlink,
			type:'post',
			data:'clear=1',
			success:function(){
				location.href=indexlink;
			}
		});
		return false;
	});
	$('#searchForm').find('select[name="fieldname"]').change(function(){
		var val=$(this).val();
		$(this).find('option').each(function(){
			if(val==$(this).attr('value')){
				if($(this).attr('rel')=='date'){
					addInpDateBut($('#searchForm').find('input#search_keyword'));
					var opt='<option value="equal">Equal to</option>';
					opt+='<option value="greater">Greater than</option>';
					opt+='<option value="less">Less than</option>';
					$('#searchForm').find('select#search_criteria').html(opt);
					$('#searchForm').find('input[name="mode"]').val('date');
				}else{
					if($('#searchForm').find('input[name="mode"]').val()!='text'){
						$('#searchForm').find('input[name="mode"]').val('text');
						if ($('#searchForm').find('input#search_butt_cal')){
							$('#searchForm').find('input#search_butt_cal').remove();
						}
						var opt='<option value="equal">Equal to</option>';
						opt+='<option value="contains">Contains</option>';
						$('#searchForm').find('select#search_criteria').html(opt);
					}
				}
			}
		});
	});
	if($('#searchForm').find('input[name="mode"]').val()=='date'){
		addInpDateBut($('#searchForm').find('input#search_keyword'));
	}
	$('table.gazel_form label.label').parent().addClass('label');
}
function admdelete(){
	var cb=[];
	$('input[type="checkbox"][name="cb[]"]').each(function(){
		if($(this).is(':checked')){
			cb[cb.length]='cb[]='+$(this).val();
		}
	});
	
	if ( cb.length==0 ) {
		alert('Please select row to delete first');
	}else{
		if ( confirm('Are you sure you want to delete?') ){
			$.ajax({
				url:deletelink,
				type:'post',
				dataType:'json',
				data:cb.join('&'),
				success:function(data){
					if (data.stat=='failed'){
						alert(data.msg);
					}else{
						location.href=data.msg;
					}
				}
			});
		}
	}
}
function admin_tree(){
	$('div.tree input[type="checkbox"]').each(function(){
		$(this).click(function(){
			if($(this).is(':checked')){
				var c=true;
			}else{
				var c=false;
			}
			$(this).next().find('input[type="checkbox"]').each(function(){
				if(c){
					$(this).attr('checked','checked');
				}else{
					$(this).attr('checked','');
				}
			});
		});
	});
}
function initSubmenuIcon(){
	$('li.admin-delete a').attr('title','Delete').css({'display':'block','width':'16px','height':'14px'}).html('<img border="0" src="'+baseurl+'/assets/images/action_delete.png" />');
	$('li.admin-add a').attr('title','Add').css({'display':'block','width':'16px','height':'14px'}).html('<img border="0" src="'+baseurl+'/assets/images/action_add.png" />');
	$('li.admin-browse a').attr('title','Browse').css({'display':'block','width':'16px','height':'14px'}).html('<img border="0" src="'+baseurl+'/assets/images/application.png" />');
}
$(document).ready(function(){
	initMenu();
	initSubmenuIcon();
	admin_cb();
	admin_table();
	admin_tree();
});