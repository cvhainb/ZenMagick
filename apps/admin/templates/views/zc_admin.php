<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_CATALOG.ZC_ADMIN_FOLDER ?>/includes/stylesheet.css">
<?php
if (!function_exists('zen_href_link')) {

    /**
     * zen_href_link wrapper that delegates to the Zenmagick implementation.
     *
     * @package zenmagick.store.sf.override
     */
    function zen_href_link($page='', $params='', $transport='NONSSL', $addSessionId=true, $seo=true, $isStatic=false, $useContext=true) {
        if (defined('ZC_INSTALL_PATH')) {
            $request = ZMRequest::instance();
            return $request->url('zc_admin', 'zpid='.$page.'&'.$params);
        }
        return zen_href_link_DISABLED($page, $params, $transport, $addSessionId, $seo, $isStatic, $useContext);
    }

}
$zcAdminFolder = ZC_INSTALL_PATH.ZC_ADMIN_FOLDER.DIRECTORY_SEPARATOR;
$zcPage = $request->getParameter('zpid', 'index').'.php';
chdir($zcAdminFolder);

// prepare globals
global $PHP_SELF, $db, $autoLoadConfig, $sniffer, $currencies, $template, $current_page_base;
$PHP_SELF = $zcAdminFolder.$zcPage;

$code = file_get_contents($zcAdminFolder.$zcPage);
$code = preg_replace("/<!doctype[^>]*>/s", '', $code);
$code = preg_replace("/<html.*<body[^>]*>/s", '', $code);
$code = preg_replace("/require\(\s*DIR_WS_INCLUDES\s*\.\s*'header.php'\s*\);/", '', $code);
$code = preg_replace("/require\(\s*DIR_WS_INCLUDES\s*\.\s*'footer.php'\s*\);/", '', $code);
$code = preg_replace("/<\/body>\s*<\/html>/s", '', $code);
$code = preg_replace("/require\(\s*DIR_WS_INCLUDES\s*\.\s*'application_bottom.php'\s*\);/", '', $code);
ob_start();
eval('?>'.$code);
$content = ob_get_clean();
$content = str_replace('src="includes', 'src="'.DIR_WS_ADMIN.'includes', $content);
$content = str_replace('src="images', 'src="'.DIR_WS_ADMIN.'images', $content);
$content = str_replace(array('onmouseover="rowOverEffect(this)"', 'onmouseout="rowOutEffect(this)"'), '', $content);
?>
<div id="sub-menu">
  <div id="sub-common">
    <?php
      ob_start();
      $zc_menus = array('catalog', 'modules', 'customers', 'taxes', 'localization', 'reports', 'tools', 'gv_admin', 'extras', 'zenmagick');
      $menu = array();
      foreach ($zc_menus as $zm_menu) {
          require(DIR_WS_BOXES . $zm_menu . '_dhtml.php');
          if ('zenmagick' == $zm_menu) {
              continue;
          }
          $header = $za_heading['text'];
          $menu[$header] = array();
          foreach ($za_contents as $item) {
              if (-1 < strpos('zmIndex', $item['link'])) {
                  continue;
              }
              $menu[$header][$item['text']] = $item['link'];
          }
      }
      ob_end_clean();
    ?>
    <?php foreach ($menu as $header => $items) { ?>
      <h3><a href="#"><?php echo $header ?></a></h3>
      <div>
        <ul>
          <?php foreach ($items as $text => $link) { ?>
            <li><a href="<?php echo $link ?>"><?php echo $text ?></a></li>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>
  </div>
</div>
<script type="text/javascript">
	$(function() {
		$("#sub-common").accordion({
      active: false,
			autoHeight: false,
      collapsible: true,
      navigation: true,
      navigationFilter: function() {
        return this.href == location.href;
      }
		});
	});
</script>
<div id="view-container">
  <?php echo $content; ?>
  <br clear="left">
</div>
