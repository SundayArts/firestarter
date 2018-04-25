jQuery(function(){
	jQuery('#add-ico').on('click',function(event){
        event.preventDefault();
        // 追加前に保存

        var lastIndex = 0;
        if (jQuery('#icos-table div.icos').length > 0) {
            lastIndex = jQuery('#icos-table div.icos:last').attr('id').match(/\d+/)[0];
        }
        var index = Number(lastIndex) + 1;
        var ico  = '';
        ico += '<div class="icos" id="ico-' + index + '">';
        ico += '    <div id="icon-options-general" class="icon32"><br /></div><h3 style="margin-top: 20px;padding-top: 30px;border-top: 1px solid #ddd;">ICO期間' + index + '</h2>';
        ico += '    <table class="form-table ico-setting-table">';
        ico += '        <tr valign="top">';
        ico += '            <th scope="row"><label>見出し</label></th>';
        ico += '            <td><input name="ico_settings[icos][ico' + index + '][title]" type="text" value="" class="regular-text" /></td>';
        ico += '        </tr>';
        ico += '        <tr valign="top">';
        ico += '            <th scope="row"><label>開始日時</label></th>';
        ico += '            <td><input name="ico_settings[icos][ico' + index + '][start_date]" type="date" value="" class="" />';
        ico += '                <input name="ico_settings[icos][ico' + index + '][start_time]" type="time" value="" class="" /></td>';
        ico += '        </tr>';
        ico += '        <tr valign="top">';
        ico += '            <th scope="row"><label>終了日時</label></th>';
        ico += '            <td><input name="ico_settings[icos][ico' + index + '][end_date]" type="date" value="" class="" />';
        ico += '                <input name="ico_settings[icos][ico' + index + '][end_time]" type="time" value="" class="" /></td>';
        ico += '        </tr>';
        ico += '         <tr valign="top">';
        ico += '             <th scope="row"><label>交換比率</label></th>';
        ico += '             <td>1ETH = <input name="ico_settings[icos][ico' + index + '][rate]" type="text" value="" class="" />Coin</td>';
        ico += '        </tr>';
        ico += '     </table>';
        ico += ' </div>';

        var html = jQuery('#icos-table').html();
        jQuery('#icos-table').html(html + ico);
        // 追加後に保存されたものを入れる
    });
    
    jQuery('#add-roadmap').on('click',function(event){
        event.preventDefault();
        // 追加前に保存

        var lastIndex = 0;
        if (jQuery('#roadmap-table tr').length > 0) {
            lastIndex = jQuery('#roadmap-table tr:last').attr('id').match(/\d+/)[0];
        }
        var index = Number(lastIndex) + 1;
        var roadmap  = '';
        roadmap += '<tr valign="top" id="roadmap-' + index + '">';
        roadmap += '    <th scope="row"><label>ロードマップ' + index + '</label></th>';
        roadmap += '    <td>';
        roadmap += '        <input name="site_settings[roadmap][roadmap' + index + '][date]" type="date" value="" class="" /><br />';
        roadmap += '        <textarea name="site_settings[roadmap][roadmap' + index + '][html]" rows="5" cols="45"></textarea>';
        roadmap += '    </td>';
        roadmap += '</tr>"';

        var $table = jQuery('#roadmap-table');
        if (jQuery('#roadmap-table tbody').length > 0) {
            $table = jQuery('#roadmap-table tbody');
        }
        var html = $table.html();
        $table.html(html + roadmap);
        // 追加後に保存されたものを入れる
    });
    
    jQuery('#add-whitepaper').on('click',function(event){
        event.preventDefault();
        // 追加前に保存

        var lastIndex = 0;
        if (jQuery('#whitepaper-table tr').length > 0) {
            lastIndex = jQuery('#whitepaper-table tr:last').attr('id').match(/\d+/)[0];
        }
        var index = Number(lastIndex) + 1;
        var roadmap  = '';
        roadmap += '<tr valign="top" id="whitepaper-' + index + '">';
        roadmap += '    <th scope="row"><label>ホワイトペーパー' + index + '</label></th>';
        roadmap += '    <td>';
        roadmap += '        <input name="site_settings[etc][whitepaper][' + index + ']" type="text" value="" class="regular-text" />';
        roadmap += '        <p class="description">URL</p>';
        roadmap += '        <input name="site_settings[etc][whitepaper_text][' + index + ']" type="text" value="" class="regular-text" />';
        roadmap += '        <p class="description">リンクテキスト</p>';
        roadmap += '    </td>';
        roadmap += '</tr>"';

        var $table = jQuery('#whitepaper-table');
        if (jQuery('#whitepaper-table tbody').length > 0) {
            $table = jQuery('#whitepaper-table tbody');
        }
        var html = $table.html();
        $table.html(html + roadmap);
        // 追加後に保存されたものを入れる
    });
});
