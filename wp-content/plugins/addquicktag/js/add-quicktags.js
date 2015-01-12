/**
 * AddQuicktag Script to add buttons to html-editor
 * 
 * @package  AddQuicktag Plugin
 * @author   Frank Bueltge <frank@bueltge.de>
 * @version  12/19/2014
 * @since    2.0.0
 */

jQuery(document).ready(function(e){function t(e){try{var t=document.createElement("textarea");return t.innerHTML=e,t.value}catch(n){}try{var a=document.createElement("div");if(a.innerHTML=e.replace(/</g,"&lt;").replace(/>/g,"&gt;"),"undefined"!=typeof a.innerText)return a.innerText}catch(n){}}function n(e){return e.focus(),document.selection?document.selection.createRange().text:e.value.substring(e.selectionStart,e.selectionEnd)}if("undefined"!=typeof addquicktag_tags&&"undefined"!=typeof addquicktag_post_type&&"undefined"!=typeof addquicktag_pt_for_js){var a=addquicktag_tags.buttons;if("undefined"!=typeof a){if(e.inArray("addquicktag_post_type",addquicktag_pt_for_js))for(var o=0;o<a.length;o++)1===parseInt(a[o][addquicktag_post_type])&&("undefined"==typeof a[o].title&&(a[o].title=" "),"undefined"==typeof a[o].end&&(a[o].end=""),"undefined"==typeof a[o].access&&(a[o].access=""),QTags.addButton(t(a[o].text).replace(/"|\\/gi,"").toLowerCase(),a[o].text,a[o].start,a[o].end,a[o].access,a[o].title.replace(/"|\\/gi,"")));var i=addquicktag_tags.code_buttons;if("undefined"==typeof typenow&&(typenow=""),"undefined"!=typeof i&&("undefined"==typeof i.htmlentities&&(i.htmlentities=0),"undefined"==typeof i.pre&&(i.pre=0),1===i.htmlentities[typenow]&&(QTags.addButton("toHTML","HTML Entities",function(e,t){QTags.insertContent(n(t).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;"))},"Encode HTML Entities"),QTags.addButton("fromHTML","Decode HTML",function(e,t){QTags.insertContent(n(t).replace(/&amp;/g,"&").replace(/&lt;/g,"<").replace(/&gt;/g,">"))},"Decode HTML Entities")),1===i.pre[typenow])){var d=["html","javascript","css","bash","php","vb"];edButtons[109]={html:function(e){return'<select id="'+e+'code_language" class="language-select"><option>blank</option><option>'+d.join("</option><option>")+"</option></select>"}},e("body").on("change","select.language-select",function(){var t=e(this).val();edButtons[110].tagStart=t?'<code class="language-'+t+'">':"<code>"}),QTags.addButton("qt_pre","pre","<pre>","</pre>","","Preformatted text","108")}}}});