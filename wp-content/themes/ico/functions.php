<?php
date_default_timezone_set('Asia/Tokyo');

register_nav_menu('header_menu', 'ヘッダーメニュー');
register_nav_menu('footer_menu', 'フッターメニュー');


function getSiteSetting($param) {
    extract(shortcode_atts(array(
        'param1' => 'option1',
        'param2' => null,
        'style' => null
    ), $param));

    $opt = get_option('site_settings');
    if ($param2 == null) {
        $value = $opt[$param1];
    } else {
        $value = $opt[$param1][$param2];
    }
    if ($style == 'number_format') {
        $value = number_format($value);
    }
    return $value;
}

function getIcoSetting($param) {
    extract(shortcode_atts(array(
        'param1' => 'option1',
        'param2' => null,
        'style' => null
    ), $param));

    $opt = get_option('ico_settings');
    if ($param2 == null) {
        $value = $opt[$param1];
    } else {
        $value = $opt[$param1][$param2];
    }
    if ($style == 'number_format') {
        $value = number_format($value);
    }
    return $value;
}

function getFirstView() {
    $stg = get_option('site_settings');
    $opt = get_option('ico_settings');
    $sns = getSnsLinks();

    $amount = getEthBalance();
    
    // 現在の開催状況
    $saleDataResult = checkSaleDate($opt, $amount);
    // 現在開催中のICO/次回開催予定のICO
    $nowIcoStatus = getNowIco($opt, 0);
    $nextIcoStatus = getNextIco($opt, 0);
    
    $preico_goal_rate = min(floor(($amount / $opt['ico']['preico_goal'])*100), 100);
    $preico_limit_rate = min(floor(($amount / $opt['ico']['preico_limit'])*100), 100);
    $goal_rate = min(floor(($amount / $opt['ico']['goal'])*100), 100);
    $limit_rate = min(floor(($amount / $opt['ico']['limit'])*100), 100);

    $opt['ico']['preico_goal'] = number_format($opt['ico']['preico_goal']);
    $opt['ico']['preico_limit'] = number_format($opt['ico']['preico_limit']);
    $opt['ico']['goal'] = number_format($opt['ico']['goal']);
    $opt['ico']['limit'] = number_format($opt['ico']['limit']);
    $amount = number_format($amount);

    $countdown = '';
    $nowIcoText = '';
    $nextIcoText = '';

    if ($saleDataResult == 'before' && $nextIcoStatus) {
        // 次回開催までの時間を取得
        if ($nextIcoStatus == 'preico') {
            $timer = new DateTime($opt['preico']['start_date'] . ' ' . $opt['preico']['start_time'] . ':00');
        } else {
            $timer = new DateTime($opt['icos'][$nextIcoStatus]['start_date'] . ' ' . $opt['icos'][$nextIcoStatus]['start_time'] . ':00');
        }
        $now = new DateTime();
    } else if ( ($saleDataResult == 'presale' || $saleDataResult == 'presale_max'
              || $saleDataResult == 'sale' || $saleDataResult == 'max') && $nowIcoStatus) {
        // 販売中ならば終了までの時間を取得
        if ($nowIcoStatus == 'preico') {
            $timer = new DateTime($opt['preico']['end_date'] . ' ' . $opt['preico']['end_time'] . ':00');
            $nowIcoText = '<li class="countdown countdown-after countdown-head"><span class="countdown-week">' . $opt['preico']['title'] . '</span>';
            $nowIcoText .= '<span class="countdown-rate">1ETH = ' . $opt['preico']['rate'] . ' ' . $opt['ico']['unit'] . '</span></li>';
            if ($nextIcoStatus) {
                $nextIcoText = '<p class="mt-1 text-left next-rate">' . $opt['icos'][$nextIcoStatus]['title'] . ' : 1ETH = ' . $opt['icos'][$nextIcoStatus]['rate'] . ' ' . $opt['ico']['unit'] . '</p>';
            }
        } else {
            $timer = new DateTime($opt['icos'][$nowIcoStatus]['end_date'] . ' ' . $opt['icos'][$nowIcoStatus]['end_time'] . ':00');
            $nowIcoText = '<li class="countdown countdown-after countdown-head"><span class="countdown-week">' . $opt['icos'][$nowIcoStatus]['title']. '</span>';
            $nowIcoText .= '<span class="countdown-rate">1ETH = ' . $opt['icos'][$nowIcoStatus]['rate'] . ' ' . $opt['ico']['unit'] . '</span></li>';
            if ($nextIcoStatus) {
                $nextIcoText = '<p class="mt-1 text-left next-rate">' . $opt['icos'][$nextIcoStatus]['title'] . ' : 1ETH = ' . $opt['icos'][$nextIcoStatus]['rate'] . ' ' . $opt['ico']['unit'] . '</p>';
            }
        }
        $now = new DateTime();
    }

    if ($timer && $now) {
        $diff = $timer->diff($now);
        $cd_d = $diff->days;
        $cd_h = str_pad($diff->h, 2, 0, STR_PAD_LEFT);
        $cd_m = str_pad($diff->i, 2, 0, STR_PAD_LEFT);
        $cd_s = str_pad($diff->s, 2, 0, STR_PAD_LEFT);
        
        $countdown .= '<li class="countdown-before"><span class="countdown-num">' . $cd_d . '</span><span class="countdown-text">日</span></li>';
        $countdown .= '<li class="countdown-before"><span class="countdown-num">' . $cd_h . '</span><span class="countdown-text">時間</span></li>';
        $countdown .= '<li class="countdown-before"><span class="countdown-num">' . $cd_m . '</span><span class="countdown-text">分</span></li>';
        $countdown .= '<li class="countdown-before"><span class="countdown-num">' . $cd_s . '</span><span class="countdown-text">秒</span></li>';
    }
    

    // 設定前
    $nosetting = <<< EOF
<div class="main-inner">
    <div class="container">{$opt['ico']['pre_message']}</div>
</div>
EOF;
    // 開催前
    $before = <<< EOF
<div class="main-inner">
    <div class="container">
        <h2 class="text-center main-heading">{$opt['ico']['headline']}</h2>
        <div class="mt-4">
            <div class="countdown-wrap countdown-before-wrap mt-5 text-center ml-auto mr-auto">
                <p>TOKEN SALE WILL LIVE SOON</p>
                <ul class="countdown-list countdown-list-before mb-0">
                    {$countdown}
                </ul>
            </div>
            <div class="form-group ml-0 mr-0">
            <p class="text-center">最新の情報をご希望の方はメールアドレスをご登録ください</p>

            <div role="form" class="wpcf7" id="wpcf7-f4-p8-o1" lang="en-US" dir="ltr">
                <div class="screen-reader-response"></div>
                <form action="/#wpcf7-f4-p8-o1" method="post" class="wpcf7-form mt-5 text-center" novalidate="novalidate">
                    <div style="display: none;">
                    <input type="hidden" name="_wpcf7" value="4">
                    <input type="hidden" name="_wpcf7_version" value="4.9">
                    <input type="hidden" name="_wpcf7_locale" value="en_US">
                    <input type="hidden" name="_wpcf7_unit_tag" value="wpcf7-f4-p8-o1">
                    <input type="hidden" name="_wpcf7_container_post" value="8">
                    </div>
                    <span class="wpcf7-form-control-wrap your-email">
                        <input type="email" name="your-email" value="" placeholder="enter your email address" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email form-control input-email-address" aria-required="true" aria-invalid="false">
                    </span>
        
                    <input type="submit" value="subscribe" class="wpcf7-form-control wpcf7-submit submit-email-address"><span class="ajax-loader"></span>
                    <div class="wpcf7-response-output wpcf7-display-none"></div>
                </form>
            </div>
            </div>
            <a href="#pagelink-whitepaper" class="btn btn-secondary btn-whitepaper mt-5">DOWNLOAD WHITEPAPER</a>
            <div class="text-center mt-5">
                {$sns}
            </div>
        </div>
    </div>
</div>
EOF;
    // プレセール
    $presale = <<< EOF
<div class="main-inner">
    <div class="container">
        <h2 class="text-center main-heading">{$opt['ico']['headline']}</h2>
        <div class="mt-4">
            <div class="countdown-wrap mt-5 text-center">
                <p>THE ICO ENDS IN</p>
                <ul class="countdown-list mb-0">
                    {$nowIcoText}
                    {$countdown}
                </ul>
                {$nextIcoText}
            </div>
            <div class="progress-wrap mt-5">
                <p class="text-center">ICOの状況<br class="spDisplay">（プレセールの目標: {$opt['ico']['preico_goal']} ETHまで）</p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped" style="width:{$preico_goal_rate}%;">{$preico_goal_rate}%</div>
                </div>
                <ul class="progress-item mt-1">
                    <li class="text-left">RAISED: {$amount} ETH</li>
                </ul>
            </div>
            <div class="contribute-main">
                <div class="check-kiyaku text-center mt-5">
                    <label>
                        <input type="checkbox"><a href="{$stg['etc']['kiyaku']}" target="_blank">利用規約</a>に同意しニューヨーク市民・中国国民でないことを確認しました
                    </label>
                </div>
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary inactive pt-3 pb-3" data-toggle="modal" data-target="#modal-warning">CONTRIBUTE</button>
                </div>
            </div>
            <div class="text-center mt-5">
                {$sns}
            </div>
        </div>
    </div>
</div>
EOF;
    // プレセール（目標達成）
    $presale_max = <<< EOF
<div class="main-inner">
    <div class="container">
        <h2 class="text-center main-heading">{$opt['ico']['headline']}</h2>
        <p class="text-center">ありがとうございます。プレセールの目標の{$opt['ico']['preico_goal']} ETHを達成しました。</p>
        <div class="mt-4">
            <div class="countdown-wrap mt-5 text-center">
                <p>THE ICO ENDS IN</p>
                <ul class="countdown-list mb-0">
                    {$nowIcoText}
                    {$countdown}
                </ul>
                {$nextIcoText}
            </div>
            <div class="progress-wrap mt-5">
                <p class="text-center">ICOの状況<br class="spDisplay">（プレセールの上限: {$opt['ico']['preico_limit']} ETHまで）</p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped" style="width:{$preico_limit_rate}%;">{$preico_limit_rate}%</div>
                </div>
                <ul class="progress-item mt-1">
                    <li class="text-left">RAISED: {$amount} ETH</li>
                </ul>
            </div>
            <div class="contribute-main">
                <div class="check-kiyaku text-center mt-5">
                    <label>
                        <input type="checkbox"><a href="{$stg['etc']['kiyaku']}" target="_blank">利用規約</a>に同意しニューヨーク市民・中国国民でないことを確認しました
                    </label>
                </div>
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary inactive pt-3 pb-3" data-toggle="modal" data-target="#modal-warning">CONTRIBUTE</button>
                </div>
            </div>
            <div class="text-center mt-5">
                {$sns}
            </div>
        </div>
    </div>
</div>
EOF;
    // 開催中
    $sale = <<< EOF
<div class="main-inner">
    <div class="container">
        <h2 class="text-center main-heading">{$opt['ico']['headline']}</h2>
        <div class="mt-4">
            <div class="countdown-wrap mt-5 text-center">
                <p>THE ICO ENDS IN</p>
                <ul class="countdown-list mb-0">
                    {$nowIcoText}
                    {$countdown}
                </ul>
                {$nextIcoText}
            </div>
            <div class="progress-wrap mt-5">
                <p class="text-center">ICOの状況<br class="spDisplay">（目標: {$opt['ico']['goal']} ETHまで）</p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped" style="width:{$goal_rate}%;">{$goal_rate}%</div>
                </div>
                <ul class="progress-item mt-1">
                    <li class="text-left">RAISED: {$amount} ETH</li>
                </ul>
            </div>
            <div class="contribute-main">
                <div class="check-kiyaku text-center mt-5">
                    <label>
                        <input type="checkbox"><a href="{$stg['etc']['kiyaku']}" target="_blank">利用規約</a>に同意しニューヨーク市民・中国国民でないことを確認しました
                    </label>
                </div>
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary inactive pt-3 pb-3" data-toggle="modal" data-target="#modal-warning">CONTRIBUTE</button>
                </div>
            </div>
            <div class="text-center mt-5">
                {$sns}
            </div>
        </div>
    </div>
</div>
EOF;
    // 開催中（目標達成）
    $max = <<< EOF
<div class="main-inner">
    <div class="container">
        <h2 class="text-center main-heading">{$opt['ico']['headline']}</h2>
        <p class="text-center">ありがとうございます。目標の{$opt['ico']['goal']} ETHを達成しました。</p>
        <div class="mt-4">
            <div class="countdown-wrap mt-5 text-center">
                <p>THE ICO ENDS IN</p>
                <ul class="countdown-list mb-0">
                    {$nowIcoText}
                    {$countdown}
                </ul>
                {$nextIcoText}
            </div>
            <div class="progress-wrap mt-5">
                <p class="text-center">ICOの状況<br class="spDisplay">（上限: {$opt['ico']['limit']} ETHまで）</p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped" style="width:{$limit_rate}%;">{$limit_rate}%</div>
                </div>
                <ul class="progress-item mt-1">
                    <li class="text-left">RAISED: {$amount} ETH</li>
                </ul>
            </div>
            <div class="contribute-main">
                <div class="check-kiyaku text-center mt-5">
                    <label>
                        <input type="checkbox"><a href="{$stg['etc']['kiyaku']}" target="_blank">利用規約</a>に同意しニューヨーク市民・中国国民でないことを確認しました
                    </label>
                </div>
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary inactive pt-3 pb-3" data-toggle="modal" data-target="#modal-warning">CONTRIBUTE</button>
                </div>
            </div>
            <div class="text-center mt-5">
                {$sns}
            </div>
        </div>
    </div>
</div>
EOF;
    // 終了
    $notice = <<< EOF
<div class="main-inner">
    <div class="container">{$opt['ico']['end_message']}</div>
</div>
EOF;
    if ($saleDataResult == 'before') {
        return $before;
    } else if ($saleDataResult == 'presale') {
        return $presale;
    } else if ($saleDataResult == 'before') {
        return $before;
    } else if ($saleDataResult == 'presale_max') {
        return $presale_max;
    } else if ($saleDataResult == 'sale') {
        return $sale;
    } else if ($saleDataResult == 'max') {
        return $max;
    } else if ($saleDataResult == 'notice') {
        return $notice;
    } else if ($saleDataResult == 'nosetting') {
        return $nosetting;
    }
}

function getSnsLinks() {
    $opt = get_option('sns_options');
    // 開催中
    $html = '<ul class="sns-list">';
    if ($opt['option1'] && $opt['option1'] != "") {
        $html .= "\n";
        $html .= '<li><a href="' . $opt['option1'] . '" target="_blank"><img src="/wp-content/themes/ico/common/images/logo_bitcointalk.svg" alt="bitcointalk"></a></li>';
    }
    if ($opt['option2'] && $opt['option2'] != "") {
        $html .= "\n";
        $html .= '<li><a href="' . $opt['option2'] . '" target="_blank"><img src="/wp-content/themes/ico/common/images/logo_twitter.svg" alt="bitcointalk"></a></li>';
    }
    if ($opt['option3'] && $opt['option3'] != "") {
        $html .= "\n";
        $html .= '<li><a href="' . $opt['option3'] . '" target="_blank"><img src="/wp-content/themes/ico/common/images/logo_facebook.svg" alt="bitcointalk"></a></li>';
    }
    if ($opt['option4'] && $opt['option4'] != "") {
        $html .= "\n";
        $html .= '<li><a href="' . $opt['option4'] . '" target="_blank"><img src="/wp-content/themes/ico/common/images/logo_reddit.svg" alt="bitcointalk"></a></li>';
    }
    if ($opt['option5'] && $opt['option5'] != "") {
        $html .= "\n";
        $html .= '<li><a href="' . $opt['option5'] . '" target="_blank"><img src="/wp-content/themes/ico/common/images/logo_linkedin.svg" alt="bitcointalk"></a></li>';
    }
    if ($opt['option6'] && $opt['option6'] != "") {
        $html .= "\n";
        $html .= '<li><a href="' . $opt['option6'] . '" target="_blank"><img src="/wp-content/themes/ico/common/images/logo_telegram.svg" alt="bitcointalk"></a></li>';
    }
    if ($opt['option7'] && $opt['option7'] != "") {
        $html .= "\n";
        $html .= '<li><a href="' . $opt['option7'] . '" target="_blank"><img src="/wp-content/themes/ico/common/images/logo_github.svg" alt="bitcointalk"></a></li>';
    }
    if ($opt['option8'] && $opt['option8'] != "") {
        $html .= "\n";
        $html .= '<li><a href="' . $opt['option8'] . '" target="_blank"><img src="/wp-content/themes/ico/common/images/logo_medium.svg" alt="bitcointalk"></a></li>';
    }
    $html .= "\n";
    $html .= "</ul>";
    return $html;
}

function getTimeline() {
    $stg = get_option('site_settings');
    $opt = get_option('ico_settings');


    $html = '';
    $html .= '<div class="ico-table-wrap mt-5">';

    if ($opt['preico']['title'] &&
        $opt['preico']['start_date'] && 
        $opt['preico']['start_time'] && 
        $opt['preico']['end_date'] && 
        $opt['preico']['end_time'] && 
        $opt['preico']['rate']) {
            $start = new DateTime($opt['preico']['start_date'] . ' ' . $opt['preico']['start_time'] . ':00');
            $end   = new DateTime($opt['preico']['end_date'] . ' ' . $opt['preico']['end_time'] . ':00');

            $html .= '<dl class="time-line-table ico-table">';
            $html .= '    <dt>' . $opt['preico']['title'] . '</dt>';
            $html .= '    <dd>' . $start->format('Y年m月d日 H:i:s') . '- <br />' . $end->format('Y年m月d日 H:i:s') . '</dd>';
            $html .= '    <dd>1ETH = <span class="ico-price">' . stripslashes($opt['preico']['rate']) . '</span> ' . $opt['ico']['unit'] . '</dd>';
            $html .= '</dl>';
    }
    
    foreach ($opt['icos'] as $key => $value) {
        if ($value['title'] &&
            $value['start_date'] && 
            $value['start_time'] && 
            $value['end_date'] && 
            $value['end_time'] && 
            $value['rate']) {
                $start = new DateTime($value['start_date'] . ' ' . $value['start_time'] . ':00');
                $end   = new DateTime($value['end_date'] . ' ' . $value['end_time'] . ':00');
    
                $html .= '<dl class="time-line-table ico-table">';
                $html .= '    <dt>' . $value['title'] . '</dt>';
                $html .= '    <dd>' . $start->format('Y年m月d日 H:i:s') . '- <br />' . $end->format('Y年m月d日 H:i:s') . '</dd>';
                $html .= '    <dd>1ETH = <span class="ico-price">' . stripslashes($value['rate']) . '</span> ' . $opt['ico']['unit'] . '</dd>';
                $html .= '</dl>';
        }
    }

    $html .= '</div>';
    return $html;
}


function getRoadmap() {
    $stg = get_option('site_settings');
    $opt = get_option('ico_settings');

    $html = '';
    $html .= '<ol class="load-map mr-auto ml-auto mt-5">';

    foreach ($stg['roadmap'] as $key => $value) {
        $done = '';
        $roadmap_date = new DateTime($value['date']);
        $now_date = new DateTime();
        $date = $roadmap_date->format('Y年m月');
        if ($roadmap_date < $now_date) {
            $done = ' class="load-map-done"';
        }

        $html .= '<li' . $done . '>';
        $html .= '    <dl class="load-map-item">';
        $html .= '        <dt>' . $date . '</dt>';
        $html .= '        <dd class="mt-2">' . $value['html'] . '</dd>';
        $html .= '    </dl>';
        $html .= '</li>';
    }

    $html .= '</ol>';
    return $html;
}


function getWovnCodeSnippet() {
    $stg = get_option('site_settings');

    if (isset($stg['etc']['wovn_token']) && $stg['etc']['wovn_token'] !== "") {
        return '<script src="//j.wovn.io/1" data-wovnio="key=' . $stg['etc']['wovn_token'] .'" async></script>';
    } else {
        return '';
    }
}

function getContributeArea() {
    $stg = get_option('site_settings');
    $opt = get_option('ico_settings');

    $amount = getEthBalance();
    // 現在の開催状況
    $saleDataResult = checkSaleDate($opt, $amount);

    $html = '';
    if ($saleDataResult == 'presale' ||
        $saleDataResult == 'presale_max'||
        $saleDataResult == 'sale'||
        $saleDataResult == 'max') {

        $html .= '<div class="contribute-ico">';
        $html .= '<div class="check-kiyaku text-center">';
        $html .= '<label>';
        $html .= '    <input type="checkbox"><a href="' . $stg['etc']['kiyaku'] . '" target="_blank">利用規約</a>に同意しニューヨーク市民・中国国民でないことを確認しました';
        $html .= '</label>';
        $html .= '</div>';
        $html .= '<div class="text-center mt-3">';
        $html .= '<button type="button" class="btn btn-primary inactive pt-3 pb-3" data-toggle="modal" data-target="#modal-warning">CONTRIBUTE</button>';
        $html .= '</div>';
        $html .= '</div>';
    }
    return $html;
}

function getWhitepaperArea() {
    $stg = get_option('site_settings');
    $opt = get_option('ico_settings');

    $html = '';

    $html .= '<section id="pagelink-whitepaper"class="section">';
    $html .= '<div class="container">';
    $html .= '     <h3 class="section-heading text-center">ホワイトペーパー</h3>';
    $html .= '     <p class="text-center">ホワイトペーパーはこちらからダウンロードできます。</p>';
    $html .= '    <ul class="whitepaper-list">';
    foreach ($stg['etc']['whitepaper'] as $key => $value) {
        $html .= '      <li><a href="' . $stg['etc']['whitepaper'][$key] . '" target="_blank" class="btn">' . $stg['etc']['whitepaper_text'][$key] . '</a><li>';
    }
    $html .= '    <ul>';
    $html .= '</div>';
    $html .= '</section>';

    return $html;
}

// 現在の開催状況をチェック
function checkSaleDate($opt, $amount) {
    // 開催期間中かどうかチェック
    $exists_preico = false;
    $exists_icos = false;

    // preicoとicosの有無をチェック
    if ($opt['preico']['title'] &&
        $opt['preico']['start_date'] && 
        $opt['preico']['start_time'] && 
        $opt['preico']['end_date'] && 
        $opt['preico']['end_time'] && 
        $opt['preico']['rate']) {
        $exists_preico = true;
    }
    foreach ($opt['icos'] as $key => $value) {
        if ($value['title'] &&
            $value['start_date'] && 
            $value['start_time'] && 
            $value['end_date'] && 
            $value['end_time'] && 
            $value['rate']) {
            $exists_icos = true;
        }
    }

    // どちらも無いならば「開催前」
    if (!$exists_preico && !$exists_icos) {
        return 'nosetting';
    }

    // preicoの有無をチェック
    if ($exists_preico) {
        // preicoがあるならばpreico時刻と現在時刻を比較
        $start = new DateTime($opt['preico']['start_date'] . ' ' . $opt['preico']['start_time'] . ':00');
        $end = new DateTime($opt['preico']['end_date'] . ' ' . $opt['preico']['end_time'] . ':00');
        $now = new DateTime();
        if ($start > $now) {
            // 開催前なら「期間前」
            return 'before';
        } else if ($start <= $now && $now < $end) {
            // 開催中なら金額により分岐
            $goal = $opt['ico']['preico_goal'] ? $opt['ico']['preico_goal'] : $opt['ico']['goal'];
            $limit = $opt['ico']['preico_limit'] ? $opt['ico']['preico_limit'] : $opt['ico']['limit'];
            if ($goal > $amount) {
                // goal未満なら「プレICO中」
                return 'presale';
            } else if ($goal <= $amount && $limit > $amount) {
                // goal以上limit未満なら「プレICO中（目標達成）」
                return 'presale_max';
            } else {
                // goal以上なら「開催前」
                return 'nosetting';
            }
        }
        // 開催後なら次へ
    }
    
    // icosの有無をチェック
    if (!$exists_icos) {
        // 無ければ「開催前」
        return 'nosetting';
    }

    // あればWeek1から順にチェック
    foreach ($opt['icos'] as $key => $value) {
        if ($value['title'] &&
            $value['start_date'] && 
            $value['start_time'] && 
            $value['end_date'] && 
            $value['end_time'] && 
            $value['rate']) {

            // 現在時刻を比較
            $start = new DateTime($value['start_date'] . ' ' . $value['start_time'] . ':00');
            $end = new DateTime($value['end_date'] . ' ' . $value['end_time'] . ':00');
            $now = new DateTime();
            if ($start > $now) {
                // 開催前なら「期間前」
                return 'before';
            } else if ($start <= $now && $now < $end) {
                // 開催中なら金額により分岐
                $goal = $opt['ico']['goal'];
                $limit = $opt['ico']['limit'];
                if ($goal > $amount) {
                    // goal未満なら「ICO中」
                    return 'sale';
                } else if ($goal <= $amount && $limit > $amount) {
                // goal以上limit未満なら「ICO中（目標達成）」
                    return 'max';
                } else {
                    // goal以上なら「終了」
                    return 'notice';
                }
            }
            // 開催後なら次へ
        }
    }
    // すべて開催後なら「終了」
    return 'notice';
}

// 現在開催中のICOを取得
function getNowIco($opt, $amount) {
    // 開催期間中かどうかチェック
    $exists_preico = false;
    $exists_icos = false;

    // preicoとicosの有無をチェック
    if ($opt['preico']['title'] &&
        $opt['preico']['start_date'] && 
        $opt['preico']['start_time'] && 
        $opt['preico']['end_date'] && 
        $opt['preico']['end_time'] && 
        $opt['preico']['rate']) {
        $exists_preico = true;
    }
    foreach ($opt['icos'] as $key => $value) {
        if ($value['title'] &&
            $value['start_date'] && 
            $value['start_time'] && 
            $value['end_date'] && 
            $value['end_time'] && 
            $value['rate']) {
            $exists_icos = true;
        }
    }

    // どちらも無いならば「開催前」
    if (!$exists_preico && !$exists_icos) {
        return '';
    }

    // preicoの有無をチェック
    if ($exists_preico) {
        // preicoがあるならばpreico時刻と現在時刻を比較
        $start = new DateTime($opt['preico']['start_date'] . ' ' . $opt['preico']['start_time'] . ':00');
        $end = new DateTime($opt['preico']['end_date'] . ' ' . $opt['preico']['end_time'] . ':00');
        $now = new DateTime();
        if ($start <= $now && $now < $end) {
            // 開催中なら決定
            return 'preico';
        }
    }
    
    // icosの有無をチェック
    if (!$exists_icos) {
        // 無ければ「開催前」
        return '';
    }

    // あればWeek1から順にチェック
    foreach ($opt['icos'] as $key => $value) {
        if ($value['title'] &&
            $value['start_date'] && 
            $value['start_time'] && 
            $value['end_date'] && 
            $value['end_time'] && 
            $value['rate']) {

            // 現在時刻を比較
            $start = new DateTime($value['start_date'] . ' ' . $value['start_time'] . ':00');
            $end = new DateTime($value['end_date'] . ' ' . $value['end_time'] . ':00');
            $now = new DateTime();
            if ($start <= $now && $now < $end) {
                // 開催中なら決定
                return $key;
            }
        }
    }
    // すべて開催後なら「終了」
    return '';
}

// 次回開催予定のICOを取得
function getNextIco($opt, $amount) {
    // 開催期間中かどうかチェック
    $exists_preico = false;
    $exists_icos = false;

    // preicoとicosの有無をチェック
    if ($opt['preico']['title'] &&
        $opt['preico']['start_date'] && 
        $opt['preico']['start_time'] && 
        $opt['preico']['end_date'] && 
        $opt['preico']['end_time'] && 
        $opt['preico']['rate']) {
        $exists_preico = true;
    }
    foreach ($opt['icos'] as $key => $value) {
        if ($value['title'] &&
            $value['start_date'] && 
            $value['start_time'] && 
            $value['end_date'] && 
            $value['end_time'] && 
            $value['rate']) {
            $exists_icos = true;
        }
    }

    // どちらも無いならば次回開催なし
    if (!$exists_preico && !$exists_icos) {
        return '';
    }

    // preicoの有無をチェック
    if ($exists_preico) {
        // preicoがあるならばpreico時刻と現在時刻を比較
        $start = new DateTime($opt['preico']['start_date'] . ' ' . $opt['preico']['start_time'] . ':00');
        $end = new DateTime($opt['preico']['end_date'] . ' ' . $opt['preico']['end_time'] . ':00');
        $now = new DateTime();
        if ($start > $now) {
            // 開催前なら次回開催予定として戻す
            return 'preico';
        }
    }

    // あればWeek1から順にチェック
    foreach ($opt['icos'] as $key => $value) {
        if ($value['title'] &&
            $value['start_date'] && 
            $value['start_time'] && 
            $value['end_date'] && 
            $value['end_time'] && 
            $value['rate']) {

            // 現在時刻を比較
            $start = new DateTime($value['start_date'] . ' ' . $value['start_time'] . ':00');
            $end = new DateTime($value['end_date'] . ' ' . $value['end_time'] . ':00');
            $now = new DateTime();
            if ($start > $now) {
                // 開催前なら次回開催予定として戻す
                return $key;
            }
        }
    }

    // すべて開催後なら「終了」
    return '';
}

function getEthBalance() {
    global $wpdb;
    $contract_info = $wpdb->get_row('SELECT * FROM contract_info ORDER BY regist_date DESC LIMIT 1');
    
    return $contract_info->eth_balance;
}

add_shortcode('site_option', 'getSiteSetting');
add_shortcode('ico_option', 'getIcoSetting');

add_shortcode('get_firstview', 'getFirstView');
add_shortcode('get_timeline', 'getTimeline');
add_shortcode('get_roadmap', 'getRoadmap');
add_shortcode('get_contribute_area', 'getContributeArea');
add_shortcode('get_whitepaper_area', 'getWhitepaperArea');
