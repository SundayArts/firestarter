<?php
/*
Plugin Name: ICO Setting
Plugin URI: http://www.example.com/plugin
Description: ICOの開始時間などを設定する
Author: sunday
Version: 0.1
Author URI: http://www.example.com
*/


class IcoSetting {
    function __construct() {
      add_action('admin_menu', array($this, 'add_pages'));
    }
    function add_pages() {
      add_menu_page('ICO設定','ICO設定',  'level_8', __FILE__, array($this,'ico_settings_option_page'), '', 26);
    }
    function ico_settings_option_page() {
        //$_POST['ico_settings'])があったら保存
        $is_error = array();
        if ( isset($_POST['ico_settings'])) {
            check_admin_referer('shoptions');
            $opt = $_POST['ico_settings'];
            if ($opt['ico']['name'] == '') {
                $is_error[] = 'トークン名称を入力してください';
            }
            if ($opt['ico']['eth_address'] == '') {
                $is_error[] = 'ETHアドレスを入力して下さい';
            }
            if (count($is_error) > 0) {
                ?><div class="updated fade">
                    <?php foreach($is_error as $error): ?>
                    <p><strong><?php echo $error ?></strong></p>
                    <?php endforeach; ?>
                </div><?php
            } else {
                foreach($opt['icos'] as $key => $value) {
                    if (!$value['title'] &&
                        !$value['start_date'] &&
                        !$value['start_time'] &&
                        !$value['end_date'] &&
                        !$value['end_time'] &&
                        !$value['rate']) {
                            unset($opt['icos'][$key]);
                    }
                }
                delete_option('ico_settings');
                update_option('ico_settings', $opt);
                ?><div class="updated fade"><p><strong><?php _e('保存しました'); ?></strong></p></div><?php
            }
        }
        ?>
        <div class="wrap">
            <form action="" method="post">
                <div id="icon-options-general" class="icon32"><br /></div><h2>ICO設定</h2>
                <?php
                wp_nonce_field('shoptions');
                $opt = get_option('ico_settings');
                if (count($is_error) > 0) {
                    $ico_settings['ico'] = $_POST['ico_settings']['ico'];
                    $ico_settings['preico'] = $_POST['ico_settings']['preico'];
                    $ico_settings['icos'] = $_POST['ico_settings']['icos'];
                } else {
                    $ico_settings['ico'] = isset($opt['ico']) ? $opt['ico']: null;
                    $ico_settings['preico'] = isset($opt['preico']) ? $opt['preico']: null;
                    $ico_settings['icos'] = isset($opt['icos']) ? $opt['icos']: null;
                }
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label>トークン名</label></th>
                        <td><input name="ico_settings[ico][name]" type="text" value="<?php  echo htmlspecialchars($ico_settings['ico']['name']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>トークンの単位名</label></th>
                        <td><input name="ico_settings[ico][unit]" type="text" value="<?php  echo htmlspecialchars($ico_settings['ico']['unit']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>ETHアドレス</label></th>
                        <td><input name="ico_settings[ico][eth_address]" type="text" value="<?php  echo htmlspecialchars($ico_settings['ico']['eth_address']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>推奨ガスリミット</label></th>
                        <td><input name="ico_settings[ico][recommend_limit]" type="text" value="<?php  echo htmlspecialchars($ico_settings['ico']['recommend_limit']) ?>" class="regular-text" /></td>
                    </tr>
                </table>
                <div id="icon-options-general" class="icon32"><br /></div><h2 style="margin-top: 20px;padding-top: 30px;border-top: 1px solid #ddd;">ICO開始時設定</h2>
                <table class="form-table ico-setting-table">
                    <tr valign="top">
                        <th scope="row"><label>メイン見出し</label></th>
                        <td><input name="ico_settings[ico][headline]" type="text" value="<?php  echo htmlspecialchars($ico_settings['ico']['headline']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>プレICO最低調達金額<br />（ソフトキャップ）</label></th>
                        <td><input name="ico_settings[ico][preico_goal]" type="text" value="<?php  echo htmlspecialchars($ico_settings['ico']['preico_goal']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>プレICO最高調達金額<br />（ハードキャップ）</label></th>
                        <td><input name="ico_settings[ico][preico_limit]" type="text" value="<?php  echo htmlspecialchars($ico_settings['ico']['preico_limit'] )?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>最低調達金額<br />（ソフトキャップ）</label></th>
                        <td><input name="ico_settings[ico][goal]" type="text" value="<?php  echo htmlspecialchars($ico_settings['ico']['goal']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>最高調達金額<br />（ハードキャップ）</label></th>
                        <td><input name="ico_settings[ico][limit]" type="text" value="<?php  echo htmlspecialchars($ico_settings['ico']['limit']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>合計発行額</label></th>
                        <td><input name="ico_settings[ico][total]" type="text" value="<?php  echo htmlspecialchars($ico_settings['ico']['total']) ?>" class="regular-text" /> Coin</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>設定前メッセージエリア</label></th>
                        <td><textarea name="ico_settings[ico][pre_message]" rows="5" cols="45"><?php  echo stripslashes($ico_settings['ico']['pre_message']) ?></textarea></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>終了後メッセージエリア</label></th>
                        <td><textarea name="ico_settings[ico][end_message]" rows="5" cols="45"><?php  echo stripslashes($ico_settings['ico']['end_message']) ?></textarea></td>
                    </tr>
                </table>
                <div class="preico-table">
                <div id="icon-options-general" class="icon32"><br /></div><h3 style="margin-top: 20px;padding-top: 30px;border-top: 1px solid #ddd;">プレICO</h2>
                <table class="form-table ico-setting-table">
                    <tr valign="top">
                        <th scope="row"><label>見出し</label></th>
                        <td><input name="ico_settings[preico][title]" type="text" value="<?php  echo htmlspecialchars($ico_settings['preico']['title']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>開始日時</label></th>
                        <td><input name="ico_settings[preico][start_date]" type="date" value="<?php  echo htmlspecialchars($ico_settings['preico']['start_date']) ?>" class="" />
                            <input name="ico_settings[preico][start_time]" type="time" value="<?php  echo htmlspecialchars($ico_settings['preico']['start_time']) ?>" class="" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>終了日時</label></th>
                        <td><input name="ico_settings[preico][end_date]" type="date" value="<?php  echo htmlspecialchars($ico_settings['preico']['end_date']) ?>" class="" />
                            <input name="ico_settings[preico][end_time]" type="time" value="<?php  echo htmlspecialchars($ico_settings['preico']['end_time']) ?>" class="" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>交換比率</label></th>
                        <td>1ETH = <input name="ico_settings[preico][rate]" type="text" value="<?php  echo htmlspecialchars($ico_settings['preico']['rate']) ?>" class="" />Coin</td>
                    </tr>
                </table>
                </div>
                <div id="icos-table">
                    <?php foreach($ico_settings['icos'] as $key => $value): ?>
                    <?php preg_match("/ico(\d+)/", $key, $match); ?>
                    <?php $index = $match[1]; ?>
                    <?php if (!$index){continue;} ?>
                    <div class="icos" id="ico-<?php echo $index; ?>">
                        <div id="icon-options-general" class="icon32"><br /></div><h3 style="margin-top: 20px;padding-top: 30px;border-top: 1px solid #ddd;">ICO期間<?php echo $index; ?></h2>
                        <table class="form-table ico-setting-table">
                            <tr valign="top">
                                <th scope="row"><label>見出し</label></th>
                                <td><input name="ico_settings[icos][<?php echo $key; ?>][title]" type="text" value="<?php  echo htmlspecialchars($value['title']) ?>" class="regular-text" /></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label>開始日時</label></th>
                                <td><input name="ico_settings[icos][<?php echo $key; ?>][start_date]" type="date" value="<?php  echo htmlspecialchars($value['start_date']) ?>" class="" />
                                    <input name="ico_settings[icos][<?php echo $key; ?>][start_time]" type="time" value="<?php  echo htmlspecialchars($value['start_time']) ?>" class="" /></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label>終了日時</label></th>
                                <td><input name="ico_settings[icos][<?php echo $key; ?>][end_date]" type="date" value="<?php  echo htmlspecialchars($value['end_date']) ?>" class="" />
                                    <input name="ico_settings[icos][<?php echo $key; ?>][end_time]" type="time" value="<?php  echo htmlspecialchars($value['end_time']) ?>" class="" /></td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><label>交換比率</label></th>
                                <td>1ETH = <input name="ico_settings[icos][<?php echo $key; ?>][rate]" type="text" value="<?php  echo htmlspecialchars($value['rate']) ?>" class="" />Coin</td>
                            </tr>
                        </table>
                    </div>
                    <?php endforeach; ?>
                </div>
                <p class="submit"><input type="submit" name="AddButton" id="add-ico" class="button-secondary" value="ICO期間を追加" /></p>
                <p class="submit"><input type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
            </form>
        <!-- /.wrap --></div>
        <script type='text/javascript' src='/wp-content/plugins/ico-setting/script.js'></script>
        <?php
    }
    function get_ico_settings() {
        return get_option('ico_settings');
    }
}
$icoSetting = new IcoSetting;
