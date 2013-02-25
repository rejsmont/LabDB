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
 * @param filter
 * 
 */
function checkVial(filter)
{
    var id = parseInt($('#barcode').val(),10);
    var checkboxName;
    var checkbox;
    
    checkboxName = "#select_items_" + id;    
    checkbox = $(checkboxName);
    
    $('#barcode').parents('.control-group').removeClass('error');
    $('#barcode').removeClass('error');
    $('#barcode').siblings('span.help-inline').html('');
    
    if(checkbox.length) {
        if (checkbox.is(':checked')) {
            checkbox.removeAttr('checked');
            checkbox.parents('tr').children('td')
                .stop().css("background-color","")
                .effect("highlight", {color: "red"}, 5000);
        } else {
            checkbox.prop("checked", true);
            checkbox.parents('tr').children('td')
                .stop().css("background-color","")
                .effect("highlight", {color: "green"}, 5000);
        }
    } else {

        url = $('#barcode').data('link');
          
        var request = $.ajax({
            type: "GET",
            url: url,
            data: {id: id, filter: filter, format: 'html'}
        });
        
        request.done(
            function(response) {
                $("#select tbody").append(response);
                checkbox = $(checkboxName);
                checkbox.parents('tr').children('td')
                    .stop().css("background-color","")
                    .effect("highlight", {color: "green"}, 5000);
                        
                });
                
        request.fail(
            function(xhr, ajaxOptions, thrownError) {
                $('#barcode').parents('.control-group').addClass('error');
                $('#barcode').addClass('error');
                $('#barcode').siblings('span.help-inline').html(form_error(xhr.responseText));
            });
    }

    $('#barcode').val('');
    $('#barcode').parents('form').find(':input').blur();
    $('#barcode').focus();
}

function getVial(caller)
{
    var caller_id = caller.id;
    var filter = caller_id.substring(caller_id.lastIndexOf('_')+1);
    var control = caller_id.substring(7);
    var vial_id = parseInt(caller.value,10);
    
    $('#' + caller.id + '_error').html('');
    
    if (caller.value == 0) {
        $('[id$="Type_' + filter + '"]').val('null');
        $('#' + caller.id + '_data').html('');
    } else {    
        
        var url = '';
        
        switch(filter) {
            case 'source':
            case 'stock':
            case 'parent':
                url = '/app_dev.php/ajax/vials/stock/';
                break;
            case 'source_cross':
            case 'cross':
                url = '/app_dev.php/ajax/vials/cross/';
                break;
            default:
                url = '/app_dev.php/ajax/vials/';
                break;
        }
        
        var request = $.getJSON(url + vial_id + '.json').success(function(vial) {
            
            var html = '';

            switch(filter) {
                case 'source':
                    if (vial.stock) {
                        html = pad(vial.id + '',6);
                        $('#' + control).val(vial.id);
                        $('#' + caller.id + '_data').html(html);
                        $('#' + caller.id + '_header').html('' + vial.stock.name);
                    }
                    break;
                case 'parent':
                    if (vial.stock) {
                        html = pad(vial.id + '',6);
                        $('#' + control).val(vial.id);
                        $('#' + caller.id + '_data').html(html);
                    }
                    break;
                case 'stock':
                    if (vial.stock) {
                        html = vial.stock.name;
                        $('#' + control).val(vial.stock.id);
                        $('#' + caller.id + '_data').html(html);
                    }
                    break;
                case 'source_cross':
                case 'cross':
                    if (vial.cross) {
                        html = vial.cross.virgin_name + " \u263f ✕ " + vial.cross.male_name + " \u2642";
                        $('#' + control).val(vial.cross.id);
                        $('#' + caller.id + '_data').html(html);
                    }
                    break;
                case 'virgin':
                case 'male':
                    if (vial.stock) {                            
                        html = vial.stock.name + ' (' + pad(vial.id + '',6) + ')';
                        $('#' + control).val(vial.id);
                        $('#' + caller.id + '_data').html(html);
                        $('#' + control + 'Name').val(vial.stock.name);
                    } else if (vial.cross) {                            
                        html = vial.cross.virgin_name + " \u263f ✕ " + vial.cross.male_name + " \u2642"
                             + ' (' + pad(vial.id + '',6) + ')';
                        $('#' + control).val(vial.id);
                        $('#' + caller.id + '_data').html(html);
                        $('#' + control + 'Name').val('');
                    }
                    break;
            }
        })
        
        request.fail(function(xhr, ajaxOptions, thrownError) {
            $('#' + caller.id + '_error').html(form_error(xhr.responseText));
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
        if (!$targ.is("textarea") && $targ.is(".barcode")) {
            $targ.blur();
            $targ.focus();
            $targ.select();
            return false;
        }
        return true;
    }
}

var popoverTimeoutLock;

$(document).ready(function() {
    $('form').bind("keypress", function(e) {
        return preventEnterSubmit(e);
    });

    $('#checkall').click(function () {
        var checked = $(this).is(":checked");
        $(this).parents('table').find('tbody :checkbox').each(function(index) {
           $(this).prop("checked", checked);
        });
    });
    
    $('.popover-trigger').mouseenter(function() {
      var element = $(this);
      element.addClass("hasFocus");
      setTimeout(function() { 
        if (element.hasClass("hasFocus")) {
          element.off('mouseenter mouseleave');
          element.removeClass("hasFocus");
          $.ajax({
            url: element.data('link'),
            type: 'get',
            data: {type: element.data('type'), id: element.data('id')},
            success: function(json) {
              var title = json.title == 'undefined' ? false : json.title;
              var html = json.html == 'undefined' ? false : json.html;
              if ((title != false)&&(html != false)) {
                element.popover({
                  title:title,
                  content:html,
                  placement:'bottom',
                  html:true,
                  trigger:'manual'
                }).popover('show').mouseenter(function() {
                    var ref = $(this);
                    ref.addClass("hasFocus");
                    setTimeout(function() { 
                      if (ref.hasClass("hasFocus")) {
                        ref.popover('show');
                      }
                    },2000);
                  }).mouseleave(function() {
                      var ref = $(this);
                      ref.removeClass("hasFocus");
                      popoverTimeoutLock = setTimeout(function() {
                        if (! ref.hasClass("hasFocus")) {
                          ref.popover('hide');
                        }
                      }, 250);
                  });
                $('.popover').off('mouseenter mouseleave').mouseenter(function() {
                    clearTimeout(popoverTimeoutLock);
                  }).mouseleave(function() {
                    var ref = $(this);
                    setTimeout(function() { 
                      ref.hide();
                    }, 250);
                  });
              }
            }
          });
        }
      },2000);
    }).mouseleave(function() {
      $(this).removeClass("hasFocus");
    })
}); 

function form_error(message) {
    var error_html = "";
    error_html += '<div class="ui-widget">';
    error_html += '<div class="ui-state-error ui-corner-all" style="padding: 3px 5px;">'
    error_html += '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'
    error_html += message + '.';
    error_html += '</div>'
    error_html += '</div>'
    return error_html;
}
