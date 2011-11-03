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
    var id = parseInt(barcode.value,10);
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
            url:"http://labdb.localhost/app_dev.php/vials/getFormRow/" + id,
            success: 
                function(response) {
                    $("#vials").append(response);
                    $("#FlyVialSelectType_items_" + id).parent().parent()
                        .stop().css("background-color","")
                        .effect("highlight", {color: "green"}, 5000);
                        
                }
        });
    }

    barcode.value = '';
}