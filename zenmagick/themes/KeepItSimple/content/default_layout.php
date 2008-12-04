<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Keep It Simple</title>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
    <meta name="author" content="Erwin Aligam - styleshout.com" />
    <meta name="description" content="Site Description Here" />
    <meta name="keywords" content="keywords, here" />
    <meta name="robots" content="index, follow, noarchive" />
    <meta name="googlebot" content="noarchive" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php $zm_theme->themeURL("css/screen.css") ?>" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php $zm_theme->themeURL("theme.css") ?>" />
    <?php $pageCSS = "css/".$zm_view->getName().".css"; ?>
    <?php /* page specific CSS */ ?>
    <?php if ($zm_theme->themeFileExists($pageCSS)) { ?>
      <link rel="stylesheet" type="text/css" media="screen" href="<?php $zm_theme->themeURL($pageCSS) ?>" />
    <?php } ?>
  </head>
  <body>
    <!-- header starts-->
    <div id="header-wrap"><div id="header" class="container_16">						
      
      <h1 id="logo-text"><a href="<?php $net->url(FILENAME_DEFAULT) ?>" title="">ZenMagick</a></h1>		
      <p id="intro">As simple as that!</p>				
      
      <!-- navigation -->
      <div  id="nav">
        <?php include $zm_theme->themeFile("top-menu.php") ?>
      </div>		
      
      <div id="header-image"></div> 		
      
      <?php $form->open(FILENAME_ADVANCED_SEARCH_RESULT, '', false, array('method' => 'get', 'id' => 'quick-search')) ?>
        <p>
        <label for="qsearch">Search:</label>
        <?php define('KEYWORD_DEFAULT', zm_l10n_get("search ...")); ?>
        <?php $onfocus = "if(this.value=='" . KEYWORD_DEFAULT . "') this.value='';" ?>
        <input class="tbox" id="keyword" type="text" name="keyword" value="<?php $html->encode(ZMRequest::getParameter('keyword', KEYWORD_DEFAULT)) ?>" onfocus="<?php echo $onfocus ?>" title="Start typing and hit ENTER" />
        <input class="btn" alt="Search" type="image" name="searchsubmit" title="Search" src="<?php $zm_theme->themeURL("images/search.gif") ?>" />
        </p>
      </form>					
    
    <!-- header ends here -->
    </div></div>
    
    <!-- content starts -->
    <div id="content-outer"><div id="content-wrapper" class="container_16">
    
      <!-- main -->
      <div id="main" class="grid_8">
          <?php if (!ZMTools::inArray($zm_view->getName(), 'index')) { /* this is the actual view, not neccessarily what is in the URL */ ?>
              <?php echo $macro->buildCrumbtrail(ZMCrumbtrail::instance(), " &gt; "); ?>
          <?php } ?>

          <?php if (ZMMessages::instance()->hasMessages()) { ?>
              <ul id="messages">
              <?php foreach (ZMMessages::instance()->getMessages() as $message) { ?>
                  <li class="<?php echo $message->getType() ?>"><?php echo $message->getText() ?></li>
              <?php } ?>
              </ul>
          <?php } ?>
        
          <?php if ($zm_view->isViewFunction()) { $zm_view->callView(); } else { include($zm_view->getViewFilename()); } ?>
      <!-- main ends -->
      </div>
      
      <!-- left-columns starts -->
      <div id="left-columns" class="grid_8">
      
        <div class="grid_4 alpha">
        
          <?php if (ZMLayout::instance()->isLeftColEnabled()) { ?>
            <div id="sidebar" >
              <?php foreach (ZMLayout::instance()->getLeftColBoxNames() as $box) { ?>
                  <div class="sidebox">
                      <?php include $zm_theme->themeFile("boxes/" .$box) ?>
                  </div>
              <?php } ?>
            </div>
          <?php } ?>

        </div>
      
        <div class="grid_4 omega">
      
          <?php foreach (ZMLayout::instance()->getRightColBoxNames() as $box) { ?>
                  <?php include $zm_theme->themeFile("boxes/" .$box) ?>
          <?php } ?>
        
        </div>	
      
      <!-- end left-columns -->
      </div>		
    
    <!-- contents end here -->	
    </div></div>

    <!-- footer starts here -->	
    <div id="footer-wrapper" class="container_16">
    
      <div id="footer-bottom">
    
        <p class="bottom-left">			
        &nbsp; &copy;2008 ZenMagick&nbsp; &nbsp;
        Design by : <a href="http://www.styleshout.com/">styleshout</a>												
        </p>	
        
        <p class="bottom-right">
            <?php $first = true; foreach (ZMEZPages::instance()->getPagesForFooter() as $page) { ?>
                <?php if (!$first) { ?>| <?php } $first = false; ?><?php $html->ezpageLink($page->getId()) ?>
            <?php } ?>
        </p>
    
      </div>	
        
    </div>
    <!-- footer ends here -->

  </body>
</html>