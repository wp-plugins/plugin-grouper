jQuery(document).ready(function($){function t(t){return $(t?'.plugin_grouper_wrap input[type="checkbox"][data-id="'+g(t)+'"]':'.plugin_grouper_wrap input[type="checkbox"]')}function i(t){return $(t?".wp-list-table.plugins tr#"+g(t):".plugin_grouper_wrap")}function n(){t().click(function(){var t=$(this).attr("data-plugin-id"),i=$(this).attr("data-id"),n=$(this).attr("data-name"),a={plugin_id:t,group_id:i,group_name:n};e(),$(this).is(":checked")?(a.action="PIGPR_INPUT_INTO_GROUP",$.post(ajaxurl,a,function(a){var e='<a href="'+a[0]+'" data-id="'+i+'">'+n+"</a>";$(".wp-list-table.plugins tr#"+g(t)+" td.column-description .groups").append(e),r()},"json")):(a.action="PIGPR_DELETE_FROM_GROUP",$.post(ajaxurl,a,function(n){$(".wp-list-table.plugins tr#"+g(t)+' td.column-description .groups a[data-id="'+i+'"]').remove(),r()},"json"))})}function a(){i().remove(),$(".group_open").removeClass("group_open")}function e(){$(".wp-list-table.plugins .loading_spinner").show(),t().attr("disabled",!0)}function r(){$(".wp-list-table.plugins .loading_spinner").hide(),t().removeAttr("disabled")}function p(){$(".wp-list-table.plugins .btn-close_group").click(function(t){return t.preventDefault(),a(),!0})}function l(){$(".wp-list-table.plugins .inp-create_group").keypress(function(t){(10===t.which||13===t.which)&&($(".wp-list-table.plugins .btn-create_group").click(),t.preventDefault())}),$(".wp-list-table.plugins .btn-create_group").click(function(t){if(t.preventDefault(),$(".wp-list-table.plugins .inp-create_group").val().length){var i=$(".plugin_grouper_wrap").attr("data-id"),a={action:"PIGPR_CREATE_GROUP",group_name:$(".wp-list-table.plugins .inp-create_group").val(),plugin_id:i};e(),$.post(ajaxurl,a,function(t){r();var a=t[0],e=t[1];$(".plugin_grouper_wrap ul").append("<li></li>"),$("#Grouping-Row ul").append("<li></li>");var p=$(".plugin_grouper_wrap ul li:last-child"),l=$("#Grouping-Row ul li:last-child"),u=p.index(),o="";o='<input id="group_radio_'+u+'" type="checkbox" data-id="'+a+'"  data-name="'+e+'" data-plugin-id="'+i+'" />',o+='<label for="group_radio_'+u+'">'+e+"</label>",p.html(o),o='<input type="checkbox" data-id="'+a+'"  data-name="'+e+'" data-plugin-id="'+i+'" />',o+="<label>"+e+"</label>",l.html(o),n(),$(".wp-list-table.plugins .inp-create_group").val(""),$("#group_radio_"+u).click()},"json")}else $(".wp-list-table.plugins .inp-create_group").focus();return!0})}function u(n){t().removeAttr("checked"),i(n).find("td.column-description .groups a").each(function(){var i=$(this).attr("data-id");t(i).attr("checked",!0)})}function o(t){var i=c("plugin_group"),n="Plugin Group : "+t;n+=' <a href="#" class="add-new-h2 btn-delete_group">Delete Group</a>',$("#wpbody .wrap > h2").html(n),$(".btn-delete_group").click(function(t){t.preventDefault(),window.location.href=window.location.href+"&action=delete_group&group_id="+i})}function d(t){$(".wp-list-table.plugins tbody tr").each(function(){var i=$(this).find("td.plugin-title .activate a").attr("href")+"&plugin_group="+t,n=$(this).find("td.plugin-title .deactivate a").attr("href")+"&plugin_group="+t,a=$(this).find("td.plugin-title .delete a").attr("href")+"&plugin_group="+t;$(this).find("td.plugin-title .activate a").attr("href",i),$(this).find("td.plugin-title .deactivate a").attr("href",n),$(this).find("td.plugin-title .delete a").attr("href",a)})}function c(t){for(var i=window.location.search.substring(1),n=i.split("&"),a=0;a<n.length;a++){var e=n[a].split("=");if(e[0]===t)return e[1]}}function g(t){return t=t.replace(/([ #;?%&,.+*~\':"!^$[\]()=>|\/@])/g,"\\$1")}$("input#plugin_group_name").length&&(o($("input#plugin_group_name").val()),d(c("plugin_group"))),$(".button-grouping").click(function(t){if(t.preventDefault(),$(this).hasClass("group_open"))return a(),!0;a();var e=$(this).attr("data-id"),r=$("#Grouping-Row").clone();return i(e).after('<tr class="inactive plugin_grouper_wrap" data-id="'+e+'"><td colspan="1000">'+r.html()+"</td></tr>"),$(".plugin_grouper_wrap li").each(function(t){var i="group_radio_"+t;$(this).find("input").attr("data-plugin-id",e),$(this).find("input").attr("id",i),$(this).find("label").attr("for",i)}),$(this).addClass("group_open"),p(),l(),n(),u(e),!0})});