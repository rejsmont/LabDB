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

$(document).ready(function () {
  $('.ajax-typeahead').typeahead({
    source: function(query, process) {
      return $.ajax({
        url: $(this)[0].$element.data('link'),
        type: 'get',
        data: {query: query},
        dataType: 'json',
        success: function(json) {
          return typeof json.options == 'undefined' ? false : process(json.options);
        }
      });
    }
  });
});
