function checkVial(b){var e=parseInt($("#barcode").val(),10);var a;var d;a="#select_items_"+e;d=$(a);$("#barcode").parents(".control-group").removeClass("error");$("#barcode").removeClass("error");$("#barcode").siblings("span.help-inline").html("");if(d.length){if(d.attr("checked")){d.removeAttr("checked");d.parents("tr").children("td").stop().css("background-color","").effect("highlight",{color:"red"},5000)}else{d.prop("checked",true);d.parents("tr").children("td").stop().css("background-color","").effect("highlight",{color:"green"},5000)}}else{url=b?"/app_dev.php/ajax/vials/"+b+"/":url="/app_dev.php/ajax/vials/";var c=$.ajax({type:"GET",url:url+e+".html"});c.done(function(f){$("#select tbody").append(f);d=$(a);d.parents("tr").children("td").stop().css("background-color","").effect("highlight",{color:"green"},5000)});c.fail(function(h,f,g){$("#barcode").parents(".control-group").addClass("error");$("#barcode").addClass("error");$("#barcode").siblings("span.help-inline").html(form_error(h.responseText))})}$("#barcode").val("");$("#barcode").parents("form").find(":input").blur();$("#barcode").focus()}function getVial(c){var a=c.id;var d=a.substring(a.lastIndexOf("_")+1);var f=a.substring(7);var g=parseInt(c.value,10);$("#"+c.id+"_error").html("");if(c.value==0){$('[id$="Type_'+d+'"]').val("null");$("#"+c.id+"_data").html("")}else{var b="";switch(d){case"source":case"stock":case"parent":b="/app_dev.php/ajax/vials/stock/";break;case"source_cross":case"cross":b="/app_dev.php/ajax/vials/cross/";break;default:b="/app_dev.php/ajax/vials/";break}var e=$.getJSON(b+g+".json").success(function(i){var h="";switch(d){case"source":if(i.stock){h=pad(i.id+"",6);$("#"+f).val(i.id);$("#"+c.id+"_data").html(h);$("#"+c.id+"_header").html(""+i.stock.name)}break;case"parent":if(i.stock){h=pad(i.id+"",6);$("#"+f).val(i.id);$("#"+c.id+"_data").html(h)}break;case"stock":if(i.stock){h=i.stock.name;$("#"+f).val(i.stock.id);$("#"+c.id+"_data").html(h)}break;case"source_cross":case"cross":if(i.cross){h=i.cross.virgin_name+" \u263f ✕ "+i.cross.male_name+" \u2642";$("#"+f).val(i.cross.id);$("#"+c.id+"_data").html(h)}break;case"virgin":case"male":if(i.stock){h=i.stock.name+" ("+pad(i.id+"",6)+")";$("#"+f).val(i.id);$("#"+c.id+"_data").html(h);$("#"+f+"Name").val(i.stock.name)}else{if(i.cross){h=i.cross.virgin_name+" \u263f ✕ "+i.cross.male_name+" \u2642 ("+pad(i.id+"",6)+")";$("#"+f).val(i.id);$("#"+c.id+"_data").html(h);$("#"+f+"Name").val("")}}break}});e.fail(function(j,h,i){$("#"+c.id+"_error").html(form_error(j.responseText))})}c.value=""}function pad(b,a){return b.length<a?pad("0"+b,a):b}function preventEnterSubmit(c){if(c.which==13){var a=$(c.target);if(!a.is("textarea")&&!a.is(":button,:submit")){var b=a.parents("form").find(":input").not(":hidden");b.eq(b.index(c.target)+1).focus();return false}return true}}$(document).ready(function(){$("form").bind("keypress",function(a){return preventEnterSubmit(a)});$("#checkall").click(function(){var a=$(this).is(":checked");$(this).parents("table").find("tbody :checkbox").each(function(b){$(this).prop("checked",a)})});form_errors()});function form_error(b){var a="";a+='<div class="ui-widget">';a+='<div class="ui-state-error ui-corner-all" style="padding: 3px 5px;">';a+='<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>';a+=b+".";a+="</div>";a+="</div>";return a}function form_errors(){var a;$("td[id$='_error']").each(function(b){a=$(this).find("li").html();if(a){$(this).html(form_error(a))}})};