/* This compressed file is part of Xinha. For uncompressed sources, forum, and bug reports, go to xinha.org */
function Template(c){this.editor=c;var a=c.config;var b=this;a.registerButton({id:"template",tooltip:Xinha._lc("Insert template","Template"),image:c.imgURL("ed_template.gif","Template"),textMode:false,action:function(d){b.buttonPress(d)}});a.addToolbarElement("template","inserthorizontalrule",1)}Template._pluginInfo={name:"Template",version:"1.0",developer:"Udo Schmal",developer_url:"http://www.schaffrath-neuemedien.de/",c_owner:"Udo Schmal & Schaffrath NeueMedien",license:"htmlArea"};Template.prototype.onGenerate=function(){this.editor.addEditorStylesheet(Xinha.getPluginDir("Template")+"/template.css")};Template.prototype.buttonPress=function(a){a._popupDialog("plugin://Template/template",function(i){if(!i){return false}var d=a._doc.getElementsByTagName("body");var b=d[0];function c(j){var k=a._doc.getElementById(j);if(!k){k=a._doc.createElement("div");k.id=j;k.innerHTML=j;b.appendChild(k)}if(k.style){k.removeAttribute("style")}return k}var g=c("content");var h=c("menu1");var f=c("menu2");var e=c("menu3");switch(i.templ){case"1":h.style.position="absolute";h.style.right="0px";h.style.width="28%";h.style.backgroundColor="#e1ddd9";h.style.padding="2px 20px";g.style.position="absolute";g.style.left="0px";g.style.width="70%";g.style.backgroundColor="#fff";f.style.visibility="hidden";e.style.visibility="hidden";break;case"2":h.style.position="absolute";h.style.left="0px";h.style.width="28%";h.style.height="100%";h.style.backgroundColor="#e1ddd9";g.style.position="absolute";g.style.right="0px";g.style.width="70%";g.style.backgroundColor="#fff";f.style.visibility="hidden";e.style.visibility="hidden";break;case"3":h.style.position="absolute";h.style.left="0px";h.style.width="28%";h.style.backgroundColor="#e1ddd9";f.style.position="absolute";f.style.right="0px";f.style.width="28%";f.style.backgroundColor="#e1ddd9";g.style.position="absolute";g.style.right="30%";g.style.width="60%";g.style.backgroundColor="#fff";e.style.visibility="hidden";break}},null)};