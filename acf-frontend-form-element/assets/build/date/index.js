(()=>{"use strict";const e=window.wp.blocks,t=window.wp.i18n,a=window.wp.blockEditor,n=window.wp.components,l=function(e){var n=e.attributes,l=e.setAttributes,r=n.label,c=n.hide_label,i=n.required,o=n.instructions;return React.createElement("div",{className:"acf-field"},React.createElement("div",{className:"acf-label"},React.createElement("label",null,!c&&React.createElement(a.RichText,{tagName:"label",onChange:function(e){return l({label:e})},withoutInteractiveFormatting:!0,placeholder:(0,t.__)("Text Field","acf-frontend-form-element"),value:r}),i&&React.createElement("span",{className:"acf-required"},"*"))),React.createElement("div",{className:"acf-input"},o&&React.createElement(a.RichText,{tagName:"p",className:"description",onChange:function(e){return l({instructions:e})},withoutInteractiveFormatting:!0,value:o}),React.createElement("div",{className:"acf-input-wrap",style:{display:"flex",width:"100%"}},e.children)))},r=window.React;var c="acf-frontend-form-element";const i=function(e){var l=e.attributes,i=e.setAttributes,o=l.label,u=l.hide_label,d=l.required,s=l.instructions,p=function(e){return e.toLowerCase().replace(/[^a-z0-9 _]/g,"").replace(/\s+/g,"_")};return(0,r.useEffect)((function(){"field_key"in l&&!l.field_key&&i({field_key:Math.random().toString(36).substring(2,10)})}),[]),React.createElement(a.InspectorControls,{field_key:"fea-inspector-controls"},React.createElement(n.PanelBody,{title:(0,t.__)("General",c),initialOpen:!0},React.createElement(n.TextControl,{label:(0,t.__)("Label",c),value:o,onChange:function(e){return i({label:e})}}),React.createElement(n.ToggleControl,{label:(0,t.__)("Hide Label",c),checked:u,onChange:function(e){return i({hide_label:e})}}),"name"in l&&React.createElement(n.TextControl,{label:(0,t.__)("Name",c),value:l.name||p(o),onChange:function(e){return i({name:p(e)})}}),"field_key"in l&&React.createElement(n.TextControl,{label:(0,t.__)("Field Key",c),value:l.field_key,readOnly:!0,onChange:function(e){}}),React.createElement(n.TextareaControl,{label:(0,t.__)("Instructions",c),rows:"3",value:s,onChange:function(e){return i({instructions:e})}}),React.createElement(n.ToggleControl,{label:(0,t.__)("Required",c),checked:d,onChange:function(e){return i({required:e})}}),e.children))};var o="acf-frontend-form-element";const u=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":2,"name":"frontend-admin/date-field","title":"Date Field","description":"Displays a date field.","category":"frontend-admin","textdomain":"frontend-admin","icon":"list-view","supports":{"align":["wide"]},"attributes":{"name":{"type":"string","default":""},"label":{"type":"string","default":"Text Field"},"hide_label":{"type":"boolean","default":""},"required":{"type":"boolean","default":""},"default_value":{"type":"string","default":""},"placeholder":{"type":"string","default":""},"instructions":{"type":"string","default":""},"prepend":{"type":"string","default":""},"append":{"type":"string","default":""}},"editorScript":"file:../../date/index.js"}');(0,e.registerBlockType)(u,{edit:function(e){var r=e.attributes,c=e.setAttributes,u=r.default_value,d=r.placeholder,s=r.prepend,p=r.append,f=(0,a.useBlockProps)();return React.createElement("div",f,React.createElement(i,e,React.createElement(n.TextControl,{type:"date",label:(0,t.__)("Default Value",o),value:u,onChange:function(e){return c({default_value:e})}}),React.createElement(n.TextControl,{label:(0,t.__)("Placeholder",o),value:d,onChange:function(e){return c({placeholder:e})}}),React.createElement(n.TextControl,{label:(0,t.__)("Prepend",o),value:s,onChange:function(e){return c({prepend:e})}}),React.createElement(n.TextControl,{label:(0,t.__)("Append",o),value:p,onChange:function(e){return c({append:e})}})),React.createElement(l,e,s&&React.createElement("span",{className:"acf-input-prepend"},s),React.createElement("input",{type:"date",placeholder:d,value:u,onChange:function(e){c({default_value:e.target.value})},style:{width:"auto",flexGrow:1}}),p&&React.createElement("span",{className:"acf-input-append"},p)))},save:function(){return null}})})();