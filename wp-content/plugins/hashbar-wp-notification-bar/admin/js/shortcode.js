(function() {
	tinymce.PluginManager.add( 'btn_trigger', function( editor ){

		editor.addButton( 'btn_trigger', {
			title: 'HashBar Button',
			text: 'HashBar',
			icon: 'code',
			onclick: function(){
				editor.windowManager.open({
					title: 'HashBar Button Shortcode',
					body: [
					{
						type: 'textbox',
						name: 'btn_text',
						label: 'Button Text'
					},
					{
						type: 'textbox',
						name: 'btn_link',
						label: 'Button Link'
					},
					{
						type: 'textbox',
						name: 'btn_bg_color',
						label: 'Button background color'
					},
					{
						type: 'textbox',
						name: 'btn_text_color',
						label: 'Button text color'
					},
					{
						type: 'listbox',
						name: 'btn_style',
						label: 'Button Style',
						values: [
							{text: 'Style 1', value: 'style_1'}, 
							{text: 'Style 2', value: 'style_2'},
						]
					},
					{
						type: 'checkbox',
						name: 'btn_target',
						label: 'Open in new window'
					}],
					onsubmit: function(e){
						var tag = 'hashbar_btn';
						var btn_text = e.data.btn_text !== '' ? ' btn_text="'+ e.data.btn_text +'"' : '';
						var btn_link = e.data.btn_link !== '' ? ' btn_link="'+ e.data.btn_link +'"' : '';
						var btn_bg_color = e.data.btn_bg_color !== '' ? ' btn_bg_color="'+ e.data.btn_bg_color +'"' : '';
						var btn_text_color = e.data.btn_text_color !== '' ? ' btn_text_color="'+ e.data.btn_text_color +'"' : '';
						var btn_target = e.data.btn_target == true ? ' target="_blank"' : '';
						var btn_style = ' btn_style="'+ e.data.btn_style +'"';

						editor.insertContent('['+ tag + btn_text + btn_link + btn_target + btn_bg_color + btn_text_color + btn_style +']')
					}
				})
			}
		});
	});
})();
