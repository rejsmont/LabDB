/* 
 * Copyright 2011 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Handles vial barcode input for vial selection form
 * 
 */
function checkVial()
{
    var id = parseInt($('#barcode').val(),10);
    var checkbox = $("#FlyVialSelectType_items_" + id);
    
    if(checkbox.length) {
        if (checkbox.attr('checked')) {
            checkbox.removeAttr("checked");
            checkbox.parent().parent()
                .stop().css("background-color","")
                .effect("highlight", {color: "red"}, 5000);
        } else {
            checkbox.attr("checked","checked");
            checkbox.parent().parent()
                .stop().css("background-color","")
                .effect("highlight", {color: "green"}, 5000);
        }
    } else {
  
        $.ajax({
            type: "GET",
            url:"/app_dev.php/ajax/vials/" + id,
            success: 
                function(response) {
                    $("#vials").append(response);
                    $("#FlyVialSelectType_items_" + id).parent().parent()
                        .stop().css("background-color","")
                        .effect("highlight", {color: "green"}, 5000);
                        
                }
        });
    }

    $('#barcode').val('');
    $('#barcode').focus();
}

function getVial(caller)
{
    var caller_id = caller.id;
    var filter = caller_id.substring(7);
    var vial_id = parseInt(caller.value,10);
    
    if (caller.value == 0) {
        $('[id$="Type_' + filter + '"]').val('null');
        $('#' + caller.id + '_data').html('');
    } else {    
        $.getJSON('/app_dev.php/ajax/vials/' + vial_id + '.json', function(vial) {
            
            var html = '';

            if (vial) {           
                switch(filter) {
                    case 'parent':
                        if (vial.stock) {
                            html = pad(vial.id + '',6);
                            $('[id$="Type_' + filter + '"]').val(vial.id);
                            $('#' + caller.id + '_data').html(html);
                        }
                        break;
                    case 'stock':
                        if (vial.stock) {
                            html = vial.stock.name;
                            $('[id$="Type_' + filter + '"]').val(vial.stock.id);
                            $('#' + caller.id + '_data').html(html);
                        }
                        break;
                    case 'source_cross':
                    case 'cross':
                        if (vial.cross) {
                            html = vial.cross.virgin_name + " \u263f ✕ " + vial.cross.male_name + " \u2642";
                            $('[id$="Type_' + filter + '"]').val(vial.cross.id);
                            $('#' + caller.id + '_data').html(html);
                        }
                        break;
                    case 'virgin':
                    case 'male':
                        if (vial.stock) {                            
                            html = vial.stock.name + ' (' + pad(vial.id + '',6) + ')';
                            $('[id$="Type_' + filter + '"]').val(vial.id);
                            $('#' + caller.id + '_data').html(html);
                            $('[id$="Type_' + filter + 'Name"]').val(vial.stock.name);
                        } else if (vial.cross) {                            
                            html = vial.cross.virgin_name + " \u263f ✕ " + vial.cross.male_name + " \u2642"
                                 + ' (' + pad(vial.id + '',6) + ')';
                            $('[id$="Type_' + filter + '"]').val(vial.id);
                            $('#' + caller.id + '_data').html(html);
                            $('[id$="Type_' + filter + 'Name"]').val('');
                        }
                        break;
                }
            }
        });
    
    }
    caller.value = '';
}

function pad (str, max) {
  return str.length < max ? pad("0" + str, max) : str;
}

function preventEnterSubmit(e) {
    if (e.which == 13) {
        
        var $targ = $(e.target);
        
        if (!$targ.is("textarea") && !$targ.is(":button,:submit")) {
            
            var inputs = $targ.closest('form').find(':input').not(":hidden");
            inputs.eq( inputs.index(e.target) + 1 ).focus();
            
            return false;
        }
        return true;
    }
}

$(document).ready(function() {
    $('form').bind("keypress", function(e) {
        return preventEnterSubmit(e);
    });
    
    $('.date').datepicker({
        dateFormat: 'dd M yy'
    });

    $('nav.top a').button();
    
    $('select').selectmenu({
        style:'dropdown',
        wrapperElement: "<div class='ui-selectmenu-wrap' />"
    });
    
    $('table.ui-table td').addClass('ui-state-default');
    $('table.ui-table th').addClass('ui-widget-header');
    $('table.ui-table').attr('cellspacing','0');
    
    $('input').addClass('ui-state-default');
    $('input').hover(
        function(){$(this).addClass("ui-state-hover");},
        function(){$(this).removeClass("ui-state-hover");}
    );
    $('input').focus(
        function(){$(this).addClass("ui-state-active");}
    );
    $('input').blur(
        function(){$(this).removeClass("ui-state-active");}
    );
    
    $('#checkall').click(function () {
        $(this).parents('table').find(':checkbox').attr('checked', this.checked);
    });
}); 

