/* 
 * Copyright 2013 Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
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
 */
function checkVial(filter) {
    
    var barcode = $('#barcode').val();
    var rack_redirect = $('#barcode').data('rack-redirect');
    var id = parseInt(barcode.match(/\d+$/),10);
    
    if ((rack_redirect != null)&&(barcode.match(/^R\d+$/) != null)) {
        window.location = rack_redirect +  id;
    } else if (barcode.match(/^\d+$/) == null) {
        $('#barcode').parents('.form-group').addClass('has-error');
        $('#barcode').parents('.form-group').find('span.help-block').html(form_error('Wrong barcode format'));
        $('#barcode').val('');
        $('#barcode').parents('form').find(':input').blur();
        $('#barcode').focus();
        return;
    }
    
    var checkboxName;
    var checkbox;
    
    checkboxName = "#select_items_" + id;    
    checkbox = $(checkboxName);
    
    $('#barcode').parents('.form-group').removeClass('has-error');
    $('#barcode').parents('.form-group').find('span.help-block').html('');
    
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
        order = $('#select').children('tbody').children('tr').length;
        
        var request = $.ajax({
            type: "GET",
            url: url,
            data: {id: id, filter: filter, format: 'html', order: order}
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
                $('#barcode').parents('.form-group').addClass('has-error');
                $('#barcode').parents('.form-group').find('span.help-block').html(form_error(xhr.responseText));
            });
    }

    $('#barcode').val('');
    $('#barcode').parents('form').find(':input').blur();
    $('#barcode').focus();
}

/**
 * Handles vial barcode input for rack vial selection form
 * 
 * TODO: add logic for detailed view
 */
function checkRackVial() {
  
    var barcode = $('#barcode').val();
    var vialID = parseInt(barcode.match(/\d+$/),10);
    
    if (barcode.match(/^\d+$/) == null) {
        $('#barcode').parents('.form-group').addClass('has-error');
        $('#barcode').parents('.form-group').find('span.help-block').html(form_error('Wrong barcode format'));
        $('#barcode').val('');
        $('#barcode').parents('form').find(':input').blur();
        $('#barcode').focus();
        return;
    }
  
    var position = $('.rack-display').find('td.empty.info');
    var positionID = position.length ? position.attr('id').replace("position_", "") : null;
    var rackID = $('.rack-display').attr('id').replace("rack_", "");
    var detail = $('#detail_' + positionID);
    var checkboxName;
    var checkbox;
    var detailCheckboxName;
    var detailCheckbox;
        
    checkboxName = "#select_items_" + vialID;
    checkbox = $(checkboxName);
    detailCheckboxName = "#select_detail_items_" + vialID;
    detailCheckbox = $(detailCheckboxName);
    
    $('#barcode').parents('.form-group').removeClass('has-error');
    $('#barcode').parents('.form-group').find('span.help-block').html('');
    
    if(checkbox.length) {
        if (checkbox.is(':checked')) {
            checkbox.removeAttr('checked');
            detailCheckbox.removeAttr('checked');
            checkbox.parents('td').stop().css("background-color","")
                .effect("highlight", {color: "red"}, 5000);
            detailCheckbox.parents('tr').children('td').stop().css("background-color","")
                .effect("highlight", {color: "red"}, 5000);
        } else {
            checkbox.prop("checked", true);
            detailCheckbox.prop("checked", true);
            checkbox.parents('td').stop().css("background-color","")
                .effect("highlight", {color: "green"}, 5000);
            detailCheckbox.parents('tr').children('td').stop().css("background-color","")
                .effect("highlight", {color: "green"}, 5000);
        }
    } else if(position.length) {

        var emptyPositions = $('.rack-display').find('td.empty');
        url = $('#barcode').data('link');
        order = $('.rack-display').find('td').index(position);
        var request = $.ajax({
            type: "GET",
            url: url,
            data: { vialID: vialID, positionID: positionID, rackID: rackID, order: order }
        });
        request.done(
            function(json) {
                position.html(json.position).removeClass('empty').removeClass('info').addClass(json.class)
                    .stop().css("background-color","")
                    .effect("highlight", {color: "green"}, 5000);
                position.find('.popover-trigger').hover(function(e) {
                    popoverHover($(this), e);
                });
                position.find("[id^=select_items_]").bind("change", function() {
                  var id = "#" + $(this).attr('id').replace("select_items_", "select_detail_items_");
                  $(id).prop("checked", $(this).prop("checked"));
                });
                detail.html(json.detail).removeClass('hidden')
                    .children('td').stop().css("background-color","")
                    .effect("highlight", {color: "green"}, 5000);
                detail.find('.popover-trigger').hover(function(e) {
                    popoverHover($(this), e);
                });
                detail.find("[id^=select_detail_items_]").bind("change", function() {
                  var id = "#" + $(this).attr('id').replace("select_detail_items_", "select_items_");
                  $(id).prop("checked", $(this).prop("checked"));
                });
                if (emptyPositions.length > 1) {
                  var index = emptyPositions.index(position);
                  if (++index < emptyPositions.length) {
                    $(emptyPositions[index]).addClass('info');
                  } else {
                    $(emptyPositions[0]).addClass('info');
                  }
                }
        });
        request.fail(
            function(xhr, ajaxOptions, thrownError) {
                $('#barcode').parents('.form-group').addClass('has-error');
                $('#barcode').parents('.form-group').find('span.help-block').html(form_error(xhr.responseText));
        });
    } else {
      $('#barcode').parents('.form-group').addClass('has-error');
      $('#barcode').parents('.form-group').find('span.help-block').html(form_error('Rack is full'));
    }

    $('#barcode').val('');
    $('#barcode').parents('form').find(':input').blur();
    $('#barcode').focus();
}

/**
 * Remove vial from the rack
 * 
 * @param e
 * @param vialID
 * @param rackID
 */
function removeVial(e,vialID,rackID) {
    var element = $(e);
    $.ajax({
          url: element.data('link'),
          type: 'get',
          data: {vialID: vialID, rackID: rackID},
          success: function() {
            var cell = $('#select_items_' + vialID).parents('td');
            $('.rack-display').find('td.info').removeClass('info');
            cell.html('').removeClass('success warning danger').addClass('empty info')
                .stop().css("background-color","")
                .effect("highlight", {color: "red"}, 5000);
            var detail = $('#select_detail_items_' + vialID).parents('tr');
            detail.html('').addClass('hidden');
          }
    });
}

/**
 * Clear rack
 * 
 * @param e
 * @param rackID
 */
function clearRack(e,rackID) {
    var element = $(e);
    $.ajax({
          url: element.data('link'),
          type: 'get',
          data: {rackID: rackID},
          success: function() {
            var cells = $('.rack-display').find('td')
            cells.html('').removeClass('info success warning danger').addClass('empty')
            $(cells[0]).addClass('info');
            cells.stop().css("background-color","")
                .effect("highlight", {color: "red"}, 5000);
          }
    });
}

/**
 * Set autoprint
 * 
 * @param e
 */
function setAutoPrint(e) {
  var element = $(e);
  var checked = element.prop("checked");
  var setting = checked ? 'enabled' : 'disabled'
  $.ajax({
          url: element.data('link'),
          type: 'post',
          data: {setting: setting}
    });
}

/**
 * Set labelmode
 * 
 * @param e
 */
function setLabelMode(e) {
  var element = $(e);
  var mode = element.prop("value");
  $.ajax({
          url: element.data('link'),
          type: 'post',
          data: {labelmode: mode}
    });
}

/**
 * Handle popover events
 * 
 * @param el Element
 * @param ev Event
 *
 */
function popoverHover(el, ev) {
  var element = el;
  var event = ev;
  var timeout = element.data('delay') != null ? element.data('delay') : 0;
    
  if (event.type == 'mouseenter') {
    clearTimeout(element.data('timeout'));
    element.data('timeout', setTimeout(function() {
      clearTimeout(element.data('timeout'));
      if (! element.hasClass('loaded')) {
        $.ajax({
          url: element.data('link'),
          type: 'get',
          data: {type: element.data('type'), id: element.data('id'), rack: element.data('rack')},
          success: function(json) {
            title = json.title == 'undefined' ? false : json.title;
            content = json.html == 'undefined' ? false : json.html;
            if ((title != false)&&(content != false)) {
              element.popover({
                title: title,
                content: content,
                html: true,
                trigger: 'manual'
              }).popover('show');
              element.addClass('loaded');
              element.addClass('on');
            }
          }
        });
      } else {
        if (! element.hasClass('on')) {
          element.popover('show');
          element.addClass('on');
        }
      }
    }, timeout));
  } else if (event.type == 'mouseleave') {
    clearTimeout(element.data('timeout'));
    element.data('timeout', setTimeout(function() {
      if (element.hasClass('on')) {
        element.popover('hide');
        element.removeClass('on')
      }
    }, 250));
  }
}

/**
 * Prevent form submission on barcode inputs
 * 
 * @param e
 */
function preventEnterSubmit(e) {
    if (e.which == 13) {
        var targ = $(e.target);
        var form = targ.parents('form');
        if (!targ.is("textarea") && (targ.is(".barcode")||(form.hasClass('select')))) {
            if(targ.is(".barcode")) {
              targ.blur();
              targ.focus();
              targ.select();
            }
            return false;
        }
        return true;
    }
    return null;
}

/**
 * Generate form error html
 * 
 * @param message
 */
function form_error(message) {
    var error_html = "";
    error_html += '<div class="ui-widget">';
    error_html += '<div class="ui-state-error ui-corner-all" style="padding: 3px 5px;">'
    error_html += '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>'
    error_html += message;
    error_html += '</div>'
    error_html += '</div>'
    return error_html;
}

/*
 * The following code is executed on page load
 */
$(document).ready(function() {
    $('form').bind("keypress", function(e) {
        return preventEnterSubmit(e);
    });

    $('.checkall').click(function () {
        var checked = $(this).is(":checked");
        $(this).parents('table').find('tbody :checkbox').each(function(index) {
           $(this).prop("checked", checked);
           $(this).change();
        });
    });
    
    $('.checkrow').click(function () {
        var checked = $(this).is(":checked");
        $(this).parents('tr').find(':checkbox').each(function(index) {
           $(this).prop("checked", checked);
           $(this).change();
        });
    });
   
   $('.popover-trigger').hover(function(e) {
        popoverHover($(this), e);
   });

    $('.rack-display').find('td').click(function() {
        if($(this).hasClass('empty')) {
            $('.rack-display').find('td.info').removeClass('info');
            $(this).addClass('info');
        }
        $('#barcode').focus();
    });
    
    $('.fb-vendor').each(function() {
      var $this = $(this);
      var url = $this.data('link') + '?vendor=%QUERY';
      var template = Hogan.compile('<p>{{stock_center}}</p>');
      var source = new Bloodhound({
        datumTokenizer: function(d) { 
          return Bloodhound.tokenizers.whitespace(d.stock_center); 
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: url
      });
      source.initialize();
      $this.typeahead(null,{
        displayKey: 'stock_center',
        templates: {
          suggestion: function (d) { return template.render(d); }
        },
        source: source.ttAdapter()       
      });
    });
    
    $('.fb-vendorid').each(function() {
      var $this = $(this);
      var url = $this.data('link') + '?stock=%QUERY&vendor=%VENDOR';
      var template = Hogan.compile('<p><b>Stock {{stock_id}}</b> <small>{{stock_center}}</small></p><p><i>{{stock_genotype}}</i></p>');
      var source = new Bloodhound({
        datumTokenizer: function(d) { 
          return Bloodhound.tokenizers.whitespace(d.stock_id); 
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
          url: url,
          replace: function(url, query) {
            var vendor = encodeURIComponent($('.fb-vendor').val());
            return url.replace('%QUERY', query).replace('%VENDOR', vendor);
          }
        }
      });
      source.initialize();
      $this.typeahead(null,{
        displayKey: 'stock_id',
        templates: {
          suggestion: function (d) { return template.render(d); }
        },
        source: source.ttAdapter()       
      }).on('typeahead:selected', function(event, data) {
         $('.fb-genotype').typeahead('val', data.stock_genotype);
         $('.fb-vendor').typeahead('val', data.stock_center);
         $('.fb-link').val(data.stock_link);
      });
    });
    
    $('.foodselect').each(function() {
      var $this = $(this);
      var url = $this.data('link');
      $this.select2({
        width: 'resolve',
        initSelection : function (element, callback) {
          var data = {id: element.val(), text: element.val()};
          callback(data);
        },
        ajax: {
          url: url,
          dataType: 'json',
          data: function (term, page) {
            return {
              query: term
            };
          },
          results: function (data, page) {
            return {
              results: data
            };
          }
        }
      }).on('select2-open', function() {
        $('.select2-search').each(function() {
          var $search = $(this);
          if ($search.children('i.fa').length === 0) {
            $search.append('<i class="fa fa-spinner fa-lg fa-spin"></i>');
          }
        });
      });
    });
    $('.genotype-typeahead').each(function() {
      var $this = $(this);
      var url = $this.data('link') + '?id=%VIAL&query=%QUERY';
      var id_source = '.' + $this.data('id-source');
      var source = new Bloodhound({
        datumTokenizer: function(d) { 
          return Bloodhound.tokenizers.whitespace(d.genotype); 
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
          url: url,
          replace: function(url, query) {
            var vial = encodeURIComponent($(id_source).val());
            return url.replace('%QUERY', query).replace('%VIAL', vial);
          }
        }
      });
      source.initialize();
      $this.typeahead(null,{
        displayKey: 'genotype',
        source: source.ttAdapter()       
      });
    });
    
    $('.toggle-children').change(function() {
      var name = $(this).prop('name');
      $('[name="' + name + '"]').each(function() {
        if ($(this).is(':checked')) {
          $(this).closest('li').children('ul').addClass('in');
        } else {
          $(this).closest('li').children('ul').removeClass('in');
        }
      });
    });
    
    if ($('.navbar-toggle').is(':visible')) {
      $('.mobile-collapsed.in').removeClass('in');
    }
    
    $('#search_form_terms').keypress(function (e) {
      if (e.which === 13) {
        $('form#search-form').submit();
        return false;
      }
    });
});
