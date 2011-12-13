/*
 * editor_plugin.js - Shopp TinyMCE Plugin
 * Copyright ?? 2008-2010 by Ingenesis Limited
 * Licensed under the GPLv3 {@see license.txt}
 */
(function(){tinymce.create("tinymce.plugins.Shopp",{init:function(a,b){a.addCommand("mceShopp",function(){a.windowManager.open({file:b+"/dialog.php",width:320,height:200,inline:1},{plugin_url:b})});a.addButton("Shopp",{title:a.getLang("Shopp.desc"),cmd:"mceShopp",image:b+"/shopp.png"})}});tinymce.PluginManager.add("Shopp",tinymce.plugins.Shopp)})();if(typeof(tinyMCE)!="undefined"&&typeof(ShoppDialog)!="undefined"){tinyMCE.addI18n(tinyMCEPreInit.mceInit.language+".Shopp",ShoppDialog)};