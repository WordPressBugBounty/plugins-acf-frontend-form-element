!function(t){t("body").on("click",".sub-fields-close",(function(){t(this).removeClass("sub-fields-close").addClass("sub-fields-open"),function(e){var n=t(".popup_"+e);$subfields_section=n.find(".elementor-control-"+e+"_fields"),$subfields_section.css("display","none"),$parent_section.after($subfields_section),n.remove()}(type)})),t("body").on("click",".new-fea-form",(function(e){$link=t(this).data("link"),window.open($link,"_blank")})),t("body").on("click",".edit-fea-form",(function(e){e.stopPropagation();var n=t(this).parents(".elementor-control").siblings(".elementor-control-admin_forms_select").find("select[data-setting=admin_forms_select]").val();$link=t(this).data("link"),window.open($link+"?post="+n+"&action=edit","_blank")})),t("body").on("click",".sub-fields-open",(function(e){e.stopPropagation(),type=t(this).data("type");var n=t('<div class="sub-fields-container popup_'+type+'"><button class="add-sub-field" type="button"><i class="eicon-plus" aria-hidden="true"></i></button></div>');$parent_section=t(this).parents(".elementor-control-fields_selection"),t(this).after(n),$subfields_section=$parent_section.siblings(".elementor-control-"+type+"_fields"),$subfields_section.css("display","block"),n.prepend($subfields_section),t(this).removeClass("sub-fields-open").addClass("sub-fields-close")})),t("body").on("click",".add-sub-field",(function(){var t=$subfields_section.find(".elementor-repeater-fields-wrapper");t.find(".elementor-repeater-fields:last-child").find(".elementor-repeater-tool-duplicate").click();var e=t.find(".elementor-repeater-fields:last-child");e.find('input[data-setting="field_label_on"]').val("true").change();var n=e.find('select[data-setting="field_type"]');n.val("description").change(),e.find('input[data-setting="label"]').val(n.find('option[value="description"]').text()).change().trigger("input")}));const e=elementor.modules.controls.Select2.extend({onReady:async function(){if(this.controlSelect=this.$el.find(".custom-control-select"),this.savedValue=this.$el.find(".saved-value").val(),!feaRestData)return;const t=this.controlSelect.data("action");if(!t)return;if(feaRestData[t]){const e=this.getOptions(feaRestData[t]);return void this.controlSelect.select2({data:e})}this.controlSelect.select2({data:[{id:0,text:"Loading..."}],placeholder:"Loading Options..."});const e=await fetch(feaRestData.url+"frontend-admin/v1/"+t,{method:"GET",headers:{"Content-Type":"application/json","X-WP-Nonce":feaRestData.nonce}}),n=await e.json();feaRestData[t]=n;const o=this.getOptions(n);this.controlSelect.select2({data:o}),this.controlSelect.find('option[value="0"]').remove(),this.savedValue&&this.controlSelect.val(this.savedValue),this.controlSelect.on("change",(()=>{this.saveValue()}))},getOptions:function(t){const e=this.savedValue.split(",");this.controlSelect.data("children_of");return t.map((t=>{let n=!1;if(e.includes(t.id)&&(n=!0),t.children){if(null)return;t.children=t.children.map((t=>{let n=!1;return e.includes(t.id)&&(n=!0),{id:t.id,text:t.text,selected:n}}))}else t.id&&e.includes(t.id)&&(n=!0),t.selected=n;return t}))},saveValue:function(){let t=this.controlSelect.val();this.setValue(t)}});elementor.addControlView("fea_select",e)}(jQuery),function(t,e){var n=e.modules.controls.BaseData.extend({onReady:function(){var e=this;this.$el.on("click",".manage-conditions",(function(){e.$el.find(".fea-conditions-modal").fadeIn(),e.loadConditions()})),this.$el.on("click",".close-modal",(function(){e.$el.find(".fea-conditions-modal").fadeOut()})),this.$el.on("click",".add-or-group",(function(){t(".or-groups").append(e.createOrGroup(!0)),e.initAutocomplete()})),this.$el.on("click",".add-and-rule",(function(){t(this).siblings(".and-rules").append(e.createAndRule()),e.initAutocomplete()})),this.$el.on("click",".remove-and-rule",(function(){t(this).parent(".and-rule").remove()})),this.$el.on("click",".remove-or-group",(function(){t(this).parent(".or-group").remove()})),this.$el.on("click",".save-conditions",(function(){e.saveConditions(),e.$el.find(".fea-conditions-modal").fadeOut()})),this.initAutocomplete()},createAndRule:function(){return'\n\t\t\t<div class="and-rule">\n\t\t\t\t<input type="text" class="condition-key" placeholder="Key">\n\t\t\t\t<select class="condition-operator">\n\t\t\t\t\t<option value="=">=</option>\n\t\t\t\t\t<option value="!=">!=</option>\n\t\t\t\t\t<option value=">">></option>\n\t\t\t\t\t<option value="<"><</option>\n\t\t\t\t\t<option value=">=">>=</option>\n\t\t\t\t\t<option value="<="><=</option>\n\t\t\t\t\t<option value="IN">IN</option>\n\t\t\t\t\t<option value="NOT IN">NOT IN</option>\n\t\t\t\t</select>\n\t\t\t\t<input type="text" class="condition-value" placeholder="Value">\n\t\t\t\t<button class="button remove-and-rule">Remove</button>\n\t\t\t</div>'},createOrGroup:function(t=!1){return`\n\t\t\t<div class="or-group">\n\t\t\t\t<h4>OR Condition Group</h4>\n\t\t\t\t<button class="button add-and-rule">Add AND Condition</button>\n\t\t\t\t<button class="button remove-or-group">Remove OR Group</button>\n\t\t\t\t<div class="and-rules">\n\t\t\t\t\t${t?this.createAndRule():""}\n\t\t\t\t</div>\n\t\t\t</div>`},saveConditions:function(){var e=[];t(".or-group").each((function(){var n=[];t(this).find(".and-rule").each((function(){var e=t(this).find(".condition-key").val(),o=t(this).find(".condition-operator").val(),i=t(this).find(".condition-value").val();e&&o&&i&&n.push({key:e,operator:o,value:i})})),n.length>0&&e.push({or_group:n})})),t(".conditions-json").val(JSON.stringify(e)),this.setValue(JSON.stringify(e))},loadConditions:function(){var e=this.getControlValue();if(t(".or-groups").empty(),e){var n=JSON.parse(e),o=this;n.forEach((function(e){var n=t(o.createOrGroup());e.or_group.forEach((function(e){var i=t(o.createAndRule());i.find(".condition-key").val(e.key),i.find(".condition-operator").val(e.operator),i.find(".condition-value").val(e.value),n.find(".and-rules").append(i)})),t(".or-groups").append(n)})),o.initAutocomplete()}else t(".or-groups").append(this.createOrGroup(!0)),this.initAutocomplete()},initAutocomplete:function(){var e=["post_author","post_date","post_title","post_status","current_user","current_user_email","current_user_role","meta_key","current_date"];t(".condition-key").autocomplete({source:e}),t(".condition-value").autocomplete({source:e,select:function(e,n){return t(this).val(`{{${n.item.value}}}`),!1}})}});e.addControlView("fea_conditions_control",n)}(jQuery,window.elementor);