 <?php
 $sns = getSnsLinks();
 ?>
<footer class="footer">
    <a href="#body" id="jsi-page-top" class="page-top">top</a>
    <div class="container text-center">
        <?php wp_nav_menu( array(
                'theme_location'  =>'footer_menu',
                'container'       =>'',
                'menu_class'      =>'',
                'container_class' =>'',
                'items_wrap'      =>'<ul class="footer-link">%3$s</ul>'));
        ?>
        <div class="text-center mt-5">
            <?php echo $sns;?>
        </div>
        <div class="text-center mt-5">
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
        </div>
        <small>© 2017 <?php bloginfo('name'); ?></small>
    </div>
</footer>

</div>
<script src="/wp-content/themes/ico/common/js/jquery-3.2.1.min.js"></script>
<script src="/wp-content/themes/ico/common/js/bootstrap.min.js"></script>
<script src="/wp-content/themes/ico/common/js/script.js"></script>
<?php wp_footer(); ?>
</body>
</html>
