<?php

  ZMTemplateManager::instance()->setRightColBoxes(array('categories.php', 'manufacturers.php', 'information.php', 'banner_box.php'));
  if ('index' == ZMRequest::instance()->getRequestId()) {
      ZMTemplateManager::instance()->setLeftColBoxes(array('featured.php', 'reviews.php'));
  } else {
      ZMTemplateManager::instance()->setLeftColEnabled(false);
      if (ZMRequest::instance()->isCheckout(false)) {
          ZMTemplateManager::instance()->setRightColBoxes(array('information.php'));
      }
  }

  ZMSettings::set('isUseCategoryPage', false);
  ZMSettings::set('resultListProductFilter', '');
  ZMSettings::set('zenmagick.mvc.resultlist.defaultPagination', 6);

?>
