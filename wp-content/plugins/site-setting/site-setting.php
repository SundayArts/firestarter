<?php
/*
Plugin Name: Site Setting
Plugin URI: http://www.example.com/plugin
Description: サイトの基本情報を設定するプラグイン
Author: sunday
Version: 0.1
Author URI: http://www.example.com
*/


class SiteSetting {
    function __construct() {
      add_action('admin_menu', array($this, 'add_pages'));
    }
    function add_pages() {
      add_menu_page('サイト基本設定','サイト基本設定',  'level_8', __FILE__, array($this,'site_setting_option_page'), '', 26);
    }
    function site_setting_option_page() {
        //$_POST['site_setting_options'])があったら保存
        $is_error = array();
        if ( isset($_POST['sns_options']) && isset($_POST['site_settings']) ) {
            check_admin_referer('shoptions');
            $opt = $_POST['sns_options'];
            $stg = $_POST['site_settings'];
            //if ($opt['text4'] == '') {
            //    $is_error[] = 'option4 error';
            //}
            if (count($is_error) > 0) {
                ?><div class="updated fade">
                    <?php foreach($is_error as $error): ?>
                    <p><strong><?php echo $error ?></strong></p>
                    <?php endforeach; ?>
                </div><?php
            } else {
                foreach($stg['roadmap'] as $key => $value) {
                    if (!$value['date'] && !$value['html']) {
                            unset($stg['roadmap'][$key]);
                    }
                }
                foreach($stg['etc']['whitepaper'] as $key => $value) {
                    if (!$stg['etc']['whitepaper'][$key] && !$stg['etc']['whitepaper_text'][$key] ) {
                        unset($stg['etc']['whitepaper'][$key]);
                        unset($stg['etc']['whitepaper_text'][$key]);
                    }
                }
                delete_option('sns_options');
                delete_option('site_settings');
                update_option('sns_options', $opt);
                update_option('site_settings', $stg);
                ?><div class="updated fade"><p><strong><?php _e('保存しました'); ?></strong></p></div><?php
            }
        }
        ?>
        <div class="wrap">
            <form action="" method="post">
                <div id="icon-options-general" class="icon32"><br /></div><h2>サイト基本設定</h2>
                <?php
                wp_nonce_field('shoptions');
                $opt = get_option('sns_options');
                $stg = get_option('site_settings');
                if (count($is_error) > 0) {
                    $sns_options['option1'] = $_POST['sns_options']['option1'];
                    $sns_options['option2'] = $_POST['sns_options']['option2'];
                    $sns_options['option3'] = $_POST['sns_options']['option3'];
                    $sns_options['option4'] = $_POST['sns_options']['option4'];
                    $sns_options['option5'] = $_POST['sns_options']['option5'];
                    $sns_options['option6'] = $_POST['sns_options']['option6'];
                    $sns_options['option7'] = $_POST['sns_options']['option7'];
                    $sns_options['option8'] = $_POST['sns_options']['option8'];
                    $site_settings['roadmap'] = $_POST['site_settings']['roadmap'];
                    $site_settings['etc'] = $_POST['site_settings']['etc'];
                } else {
                    $sns_options['option1'] = isset($opt['option1']) ? $opt['option1']: null;
                    $sns_options['option2'] = isset($opt['option2']) ? $opt['option2']: null;
                    $sns_options['option3'] = isset($opt['option3']) ? $opt['option3']: null;
                    $sns_options['option4'] = isset($opt['option4']) ? $opt['option4']: null;
                    $sns_options['option5'] = isset($opt['option5']) ? $opt['option5']: null;
                    $sns_options['option6'] = isset($opt['option6']) ? $opt['option6']: null;
                    $sns_options['option7'] = isset($opt['option7']) ? $opt['option7']: null;
                    $sns_options['option8'] = isset($opt['option8']) ? $opt['option8']: null;
                    $site_settings['roadmap'] = isset($stg['roadmap']) ? $stg['roadmap']: null;
                    $site_settings['etc'] = isset($stg['etc']) ? $stg['etc']: null;
                }
                ?>
                <div id="icon-options-general" class="icon32"><br /></div><h3>SNSボタンリンク先URL</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label>bitcointalk</label></th>
                        <td><input name="sns_options[option1]" type="text" value="<?php  echo $sns_options['option1'] ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>twitter</label></th>
                        <td><input name="sns_options[option2]" type="text" value="<?php  echo $sns_options['option2'] ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>facebook</label></th>
                        <td><input name="sns_options[option3]" type="text" value="<?php  echo $sns_options['option3'] ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>reddit</label></th>
                        <td><input name="sns_options[option4]" type="text" value="<?php  echo $sns_options['option4'] ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>linkedin</label></th>
                        <td><input name="sns_options[option5]" type="text" value="<?php  echo $sns_options['option5'] ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>telegram</label></th>
                        <td><input name="sns_options[option6]" type="text" value="<?php  echo $sns_options['option6'] ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>medium</label></th>
                        <td><input name="sns_options[option7]" type="text" value="<?php  echo $sns_options['option7'] ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>github</label></th>
                        <td><input name="sns_options[option8]" type="text" value="<?php  echo $sns_options['option8'] ?>" class="regular-text" /></td>
                    </tr>
                </table>

                <div id="icon-options-general" class="icon32"><br /></div><h2 style="margin-top: 20px;padding-top: 30px;border-top: 1px solid #ddd;">ロードマップ</h2>
                <table class="form-table ico-setting-table" id="roadmap-table">
                    <?php foreach($site_settings['roadmap'] as $key => $value): ?>
                    <?php preg_match("/roadmap(\d+)/", $key, $match); ?>
                    <?php $index = $match[1]; ?>
                    <?php if (!$index){continue;} ?>
                    <tr valign="top" id="roadmap-<?php echo $index; ?>">
                        <th scope="row"><label>ロードマップ<?php echo $index; ?></label></th>
                        <td>
                            <input name="site_settings[roadmap][<?php echo $key; ?>][date]" type="date" value="<?php  echo $value['date'] ?>" class="" /><br />
                            <textarea name="site_settings[roadmap][<?php echo $key; ?>][html]" rows="5" cols="45"><?php  echo stripslashes($value['html']) ?></textarea>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>

                <p class="submit"><input type="submit" name="AddButton" id="add-roadmap" class="button-secondary" value="ロードマップを追加" /></p>

                <div id="icon-options-general" class="icon32"><br /></div><h3>その他設定</h3>
                <table class="form-table ico-setting-table" id="whitepaper-table">
                    <?php foreach($site_settings['etc']['whitepaper'] as $key => $value): ?>
                    <?php $index = $key; ?>
                    <?php if (!$index){continue;} ?>
                    <tr valign="top" id="whitepaper-<?php echo $index; ?>">
                        <th scope="row"><label>ホワイトペーパー<?php echo $index; ?></label></th>
                        <td>
                            <input name="site_settings[etc][whitepaper][<?php echo $key; ?>]" type="text" value="<?php  echo htmlspecialchars($site_settings['etc']['whitepaper'][$key]) ?>" class="regular-text" />
                            <p class="description">URL</p>
                            <input name="site_settings[etc][whitepaper_text][<?php echo $key; ?>]" type="text" value="<?php  echo htmlspecialchars($site_settings['etc']['whitepaper_text'][$key]) ?>" class="regular-text" />
                            <p class="description">リンクテキスト</p>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <p class="submit"><input type="submit" name="AddButton" id="add-whitepaper" class="button-secondary" value="ホワイトペーパーを追加" /></p>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label>利用規約</label></th>
                        <td>
                            <input name="site_settings[etc][kiyaku]" type="text" value="<?php  echo htmlspecialchars($site_settings['etc']['kiyaku']) ?>" class="regular-text" />
                            <p class="description">URL</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>特定商取引法に基づく表記</label></th>
                        <td>
                            <input name="site_settings[etc][tokusho]" type="text" value="<?php  echo htmlspecialchars($site_settings['etc']['tokusho']) ?>" class="regular-text" />
                            <p class="description">URL</p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>WOVNプロジェクトトークン設定</label></th>
                        <td><input name="site_settings[etc][wovn_token]" type="text" value="<?php  echo htmlspecialchars($site_settings['etc']['wovn_token']) ?>" class="regular-text" /></td>
                    </tr>
                </table>

                <p class="submit"><input type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
            </form>
        <!-- /.wrap --></div>
        <script type='text/javascript' src='/wp-content/plugins/ico-setting/script.js'></script>
        <?php
    }
    function get_text() {
        $opt = get_option('site_setting_options');
        return isset($opt['text']) ? $opt['text']: null;
    }
}
$site_setting = new SiteSetting;
