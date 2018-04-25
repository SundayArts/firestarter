<?php
/*
Plugin Name: Contract Setting
Plugin URI: http://www.example.com/plugin
Description: contractの設定と生成
Author: sunday
Version: 0.1
Author URI: http://www.example.com
*/


class ContractSetting {
    function __construct() {
      add_action('admin_menu', array($this, 'add_pages'));
    }
    function add_pages() {
      add_menu_page('コントラクト設定','コントラクト設定',  'level_8', __FILE__, array($this,'contract_settings_option_page'), '', 26);
    }
    function contract_settings_option_page() {
        //$_POST['contract_settings'])があったら保存
        $is_error = array();
        if ( isset($_POST['contract_settings'])) {
            check_admin_referer('shoptions');
            $opt = $_POST['contract_settings'];
            /*
            if ($opt['ico']['name'] == '') {
                $is_error[] = 'トークン名称を入力してください';
            }
            if ($opt['ico']['eth_address'] == '') {
                $is_error[] = 'ETHアドレスを入力して下さい';
            }
             */
            if (count($is_error) > 0) {
                ?><div class="updated fade">
                    <?php foreach($is_error as $error): ?>
                    <p><strong><?php echo $error ?></strong></p>
                    <?php endforeach; ?>
                </div><?php
            } else {
                delete_option('contract_settings');
                update_option('contract_settings', $opt);
                ?><div class="updated fade"><p><strong><?php _e('保存しました'); ?></strong></p></div><?php
            }
        }

        ?>
        <div class="wrap" id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <h1 class="wp-heading-inline">コントラクト設定</h1>
            <form action="" method="post">
            <div id="post-body-content">
                <?php
                wp_nonce_field('shoptions');
                $opt = get_option('contract_settings');

                if (count($is_error) > 0) {
                    $contract_settings['ico'] = $_POST['contract_settings']['ico'];
                } else {
                    $contract_settings['ico'] = isset($opt['ico']) ? $opt['ico']: null;
                }
                // テンプレートの取得
                $mytoken_tmp = file_get_contents(__DIR__ . '/MyToken_tmp.sol');
                $presale_tmp = file_get_contents(__DIR__ . '/Presale_tmp.sol');
                $crowdsale_tmp = file_get_contents(__DIR__ . '/Crowdsale_tmp.sol');

                // テンプレートの置換
                $prototype_text = $this->createTokenPrototype($contract_settings['ico'], $mytoken_tmp);
                $pre_sale_prototype_text = $this->createPreSalePrototype($contract_settings['ico'], $presale_tmp);
                $crowd_sale_prototype_text = $this->createCrowdSalePrototype($contract_settings['ico'], $crowdsale_tmp);

                ?>
                <h3 style="margin-top: 20px;padding-top: 30px;border-top: 1px solid #ddd;">ICO開始時設定</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label>トークン名</label></th>
                        <td><input name="contract_settings[ico][token_name]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['token_name']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>トークンの単位</label></th>
                        <td><input name="contract_settings[ico][symbol]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['symbol']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>小数点桁数</label></th>
                        <td><input name="contract_settings[ico][decimals]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['decimals']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>トークンの発行単位</label></th>
                        <td><input name="contract_settings[ico][total_supply]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['total_supply']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>手数料レート</label></th>
                        <td><input name="contract_settings[ico][fee_per]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['fee_per']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>手数料徴収フラグ</label></th>
                        <td><input name="contract_settings[ico][isDonate]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['isDonate']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                    <th scope="row"><label>手数料を受け取るアドレス<?php /*<br />（プライベートネット）*/ ?></label></th>
                        <td><input name="contract_settings[ico][recieve_fee_address_private]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['recieve_fee_address_private']) ?>" class="regular-text" /></td>
                    </tr>
                    <?php /*
                    <tr valign="top">
                        <th scope="row"><label>手数料を受け取るアドレス<br />（テストネット）</label></th>
                        <td><input name="contract_settings[ico][recieve_fee_address_rinkeby]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['recieve_fee_address_rinkeby']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>手数料を受け取るアドレス<br />（ライブネット）</label></th>
                        <td><input name="contract_settings[ico][recieve_fee_address_live]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['recieve_fee_address_live']) ?>" class="regular-text" /></td>
                    </tr>
                    */ ?>
                    <tr valign="top">
                        <th scope="row"><label>ロックアップ期限の有無</label></th>
                        <td><input name="contract_settings[ico][isLockup]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['isLockup']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>ロックアップ期限</label></th>
                        <td><input name="contract_settings[ico][lockupPeriod]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['lockupPeriod']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>トークンのアドレス</label></th>
                        <td><input name="contract_settings[ico][token_address]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['token_address']) ?>" class="regular-text" />
                        <p class="description">トークンのデプロイ後に入力して下さい</p></td>
                    </tr>
                </table>
                <h3 style="margin-top: 20px;padding-top: 30px;border-top: 1px solid #ddd;">プレセールの設定</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label>ETHの調達目標額</label></th>
                        <td><input name="contract_settings[ico][pre_funding_goal]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['pre_funding_goal']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>販売するトークンの上限額</label></th>
                        <td><input name="contract_settings[ico][pre_transferable_token]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['pre_transferable_token']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>1ETHあたりのトークン交換量</label></th>
                        <td><input name="contract_settings[ico][pre_price]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['pre_price']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>プレセールの開催日時</label></th>
                        <td><input name="contract_settings[ico][pre_start_time]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['pre_start_time']) ?>" class="regular-text" /></td>
                    </tr>
                </table>
                <h3 style="margin-top: 20px;padding-top: 30px;border-top: 1px solid #ddd;">クラウドセールの設定</h3>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><label>ETHの調達目標額</label></th>
                        <td><input name="contract_settings[ico][funding_goal]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['funding_goal']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>販売するトークンの上限額</label></th>
                        <td><input name="contract_settings[ico][transferable_token]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['transferable_token']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>1ETHあたりのトークン交換量</label></th>
                        <td><input name="contract_settings[ico][price]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['price']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>クラウドセールの開催日時</label></th>
                        <td><input name="contract_settings[ico][start_time]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['start_time']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>セール1週めのレート</label></th>
                        <td><input name="contract_settings[ico][swap_rate1]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['swap_rate1']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>セール2週めのレート</label></th>
                        <td><input name="contract_settings[ico][swap_rate2]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['swap_rate2']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>セール3週めのレート</label></th>
                        <td><input name="contract_settings[ico][swap_rate3]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['swap_rate3']) ?>" class="regular-text" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label>セール4週めのレート</label></th>
                        <td><input name="contract_settings[ico][swap_rate4]" type="text" value="<?php  echo htmlspecialchars($contract_settings['ico']['swap_rate4']) ?>" class="regular-text" /></td>
                    </tr>
                </table>
                <p class="submit"><input type="submit" name="Submit" class="button-primary" value="変更を保存" /></p>
            <!-- /#post-body-content --></div>
            <div id="postbox-container-1">

                <div class="postbox">
                    <button type="button" class="handlediv">■</button>
                    <h2><span>トークンのコントラクト</span></h2>
                    <div class="inside">
                        <textarea readonly="readonly" name="" rows="20" style="width:100%;"><?php  echo stripslashes($prototype_text) ?></textarea>
                    </div>
                    <div class="clear"></div>
                </div><!-- .postbox -->
                <div class="postbox">
                    <button type="button" class="handlediv">■</button>
                    <h2><span>プレセールのコントラクト</span></h2>
                    <div class="inside">
                        <textarea readonly="readonly" name="" rows="20" style="width:100%;"><?php  echo stripslashes($pre_sale_prototype_text) ?></textarea>
                    </div>
                    <div class="clear"></div>
                </div><!-- .postbox -->
                <div class="postbox">
                    <button type="button" class="handlediv">■</button>
                    <h2><span>クラウドセールのコントラクト</span></h2>
                    <div class="inside">
                        <textarea readonly="readonly" name="" rows="20" style="width:100%;"><?php  echo stripslashes($crowd_sale_prototype_text) ?></textarea>
                    </div>
                    <div class="clear"></div>
                </div><!-- .postbox -->

            <!-- /#postbox-container-1 --></div>
            </form>
        <!-- /.post-body --></div>
        <!-- /.wrap --></div>
        <script type='text/javascript' src='/wp-content/plugins/ico-setting/script.js'></script>
        <?php
    }
    function get_contract_settings() {
        return get_option('contract_settings');
    }

    private function createTokenPrototype($data, $template_text)
    {
        // 変換する項目
        $replace_array = [
            'total_supply',
            'token_name',
            'symbol',
            'decimals',
            'isLockup',
            'lockupPeriod',
        ];
        // 変換前文字列、変換後文字列の配列化
        foreach ($replace_array as $value) {
            if($value=='lockupPeriod'){
                $search[] = '@' . $value . '@';
                $replace[] = $this->convert2epochtime($data[$value]);
            }
            if($value=='isLockup'&&$data[$value]==1){
                $search[] = '@' . $value . '@';
                $replace[] = "true";
            }
            if($value=='isLockup'&&$data[$value]==0){
                $search[] = '@' . $value . '@';
                $replace[] = "false";
            }else{
                $search[] = '@' . $value . '@';
                $replace[] = $data[$value];
            }
        }
        // 置換
        $prototype_text = str_replace($search, $replace, $template_text);

        return $prototype_text;
    }

    private function createPreSalePrototype($data,  $template_text)
    {
        // 変換する項目
        $replace_array = [
            'token_address',
            'total_supply',
            'token_name',
            'symbol',
            'decimals',
            'isLockup',
            'lockupPeriod',
            'fee_per',
            'recieve_fee_address_private',
            'recieve_fee_address_rinkeby',
            'recieve_fee_address_live',
            'isDonate',
            'pre_funding_goal',
            'pre_transferable_token',
            'pre_price',
            'pre_start_time',
            'pre_end_time',
            'funding_goal',
            'transferable_token',
            'price',
        ];
        // 変換前文字列、変換後文字列の配列化
        foreach ($replace_array as $value) {

            if($value=='lockupPeriod'){
                $search[] = '@' . $value . '@';
                $replace[] = $this->convert2epochtime($data[$value]);
            }
            if($value=='pre_start_time'){
                $search[] = '@' . $value . '@';
                $replace[] = $this->convert2epochtime($data[$value]);
            }
            if($value=='pre_end_time'){
                $search[] = '@' . $value . '@';
                $replace[] = $this->convert2epochtime($data[$value]);
            }
            if($value=='isDonate'&&$data[$value]==1){
                $search[] = '@' . $value . '@';
                $replace[] = "true";
            }
             if($value=='isDonate'&&$data[$value]==0){
                $search[] = '@' . $value . '@';
                $replace[] = "false";
            }
            if($value=='isLockup'&&$data[$value]==1){
                $search[] = '@' . $value . '@';
                $replace[] = "true";
            }
            if($value=='isLockup'&&$data[$value]==0){
                $search[] = '@' . $value . '@';
                $replace[] = "false";
            }else{
                $search[] = '@' . $value . '@';
                $replace[] = $data[$value];
            }
        }
        // 置換
        $prototype_text = str_replace($search, $replace, $template_text);
        // pre_start_time, start_timeの置換
        $replace_time[] = date('Y', strtotime($data['pre_start_time']));
        $replace_time[] = date('n', strtotime($data['pre_start_time']));
        $replace_time[] = date('j', strtotime($data['pre_start_time']));
        $replace_time[] = date('G', strtotime($data['pre_start_time']));
        $replace_time[] = intval(date('i', strtotime($data['pre_start_time'])));
        $replace_time[] = intval(date('s', strtotime($data['pre_start_time'])));
        $replace_time[] = date('Y', strtotime($data['start_time']));
        $replace_time[] = date('n', strtotime($data['start_time']));
        $replace_time[] = date('j', strtotime($data['start_time']));
        $replace_time[] = date('G', strtotime($data['start_time']));
        $replace_time[] = intval(date('i', strtotime($data['start_time'])));
        $replace_time[] = intval(date('s', strtotime($data['start_time'])));
        $replace_time_array = ['pre_year', 'pre_month', 'pre_day', 'pre_hour', 'pre_minute', 'pre_second', 'year', 'month', 'day', 'hour', 'minute', 'second'];
        foreach ($replace_time_array as $value) {
            $search_time[] = '@' . $value . '@';
        }
        $prototype_text = str_replace($search_time, $replace_time, $prototype_text);

        return $prototype_text;
    }

    private function createCrowdSalePrototype($data, $template_text)
    {
        // 変換する項目
        $replace_array = [
            'token_address',
            'total_supply',
            'token_name',
            'symbol',
            'decimals',
            'isLockup',
            'lockupPeriod',
            'fee_per',
            'recieve_fee_address_private',
            'recieve_fee_address_rinkeby',
            'recieve_fee_address_live',
            'isDonate',
            'pre_funding_goal',
            'pre_transferable_token',
            'pre_price',
            'pre_start_time',
            'pre_end_time',
            'funding_goal',
            'transferable_token',
            'price',
            'start_time',
            'end_time',
        ];
        // 変換前文字列、変換後文字列の配列化
        foreach ($replace_array as $value) {

            if($value=='lockupPeriod'){
                $search[] = '@' . $value . '@';
                $replace[] = $this->convert2epochtime($data[$value]);
            }
            if($value=='pre_start_time'){
                $search[] = '@' . $value . '@';
                $replace[] = $this->convert2epochtime($data[$value]);
            }
            if($value=='pre_end_time'){
                $search[] = '@' . $value . '@';
                $replace[] = $this->convert2epochtime($data[$value]);
            }
            if($value=='start_time'){
                $search[] = '@' . $value . '@';
                $replace[] = $this->convert2epochtime($data[$value]);
            }
            if($value=='end_time'){
                $search[] = '@' . $value . '@';
                $replace[] = $this->convert2epochtime($data[$value]);
            }
            if($value=='isDonate'&&$data[$value]==1){
                $search[] = '@' . $value . '@';
                $replace[] = "true";
            }
             if($value=='isDonate'&&$data[$value]==0){
                $search[] = '@' . $value . '@';
                $replace[] = "false";
            }
            if($value=='isLockup'&&$data[$value]==1){
                $search[] = '@' . $value . '@';
                $replace[] = "true";
            }
            if($value=='isLockup'&&$data[$value]==0){
                $search[] = '@' . $value . '@';
                $replace[] = "false";
            }else{
                $search[] = '@' . $value . '@';
                $replace[] = $data[$value];
            }
        }
        // 置換
        $prototype_text = str_replace($search, $replace, $template_text);
        // pre_start_time, start_timeの置換
        $replace_time[] = date('Y', strtotime($data['pre_start_time']));
        $replace_time[] = date('n', strtotime($data['pre_start_time']));
        $replace_time[] = date('j', strtotime($data['pre_start_time']));
        $replace_time[] = date('G', strtotime($data['pre_start_time']));
        $replace_time[] = intval(date('i', strtotime($data['pre_start_time'])));
        $replace_time[] = intval(date('s', strtotime($data['pre_start_time'])));
        $replace_time[] = date('Y', strtotime($data['start_time']));
        $replace_time[] = date('n', strtotime($data['start_time']));
        $replace_time[] = date('j', strtotime($data['start_time']));
        $replace_time[] = date('G', strtotime($data['start_time']));
        $replace_time[] = intval(date('i', strtotime($data['start_time'])));
        $replace_time[] = intval(date('s', strtotime($data['start_time'])));
        $replace_time_array = ['pre_year', 'pre_month', 'pre_day', 'pre_hour', 'pre_minute', 'pre_second', 'year', 'month', 'day', 'hour', 'minute', 'second'];
        foreach ($replace_time_array as $value) {
            $search_time[] = '@' . $value . '@';
        }
        $prototype_text = str_replace($search_time, $replace_time, $prototype_text);

        return $prototype_text;
    }

    public function convert2epochtime($data)
    {
        $result = strtotime($data);
        return $result;
    }
}
$contractSetting = new ContractSetting;
