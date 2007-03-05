<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule()), $osC_Template->getPageTitle()); ?></h1>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' Batch Delete'; ?></div>
<div class="infoBoxContent">
  <form name="cDeleteBatch" action="<?php echo osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page'] . '&action=batchDelete'); ?>" method="post">

  <p><?php echo TEXT_DELETE_BATCH_INTRO; ?></p>

<?php
  $check_tax_zones_flag = array();

  $Qzones = $osC_Database->query('select geo_zone_id, geo_zone_name from :table_geo_zones where geo_zone_id in (":geo_zone_id") order by geo_zone_name');
  $Qzones->bindTable(':table_geo_zones', TABLE_GEO_ZONES);
  $Qzones->bindRaw(':geo_zone_id', implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))));
  $Qzones->execute();

  $names_string = '';

  while ($Qzones->next()) {
    $Qcheck = $osC_Database->query('select tax_zone_id from :table_tax_rates where tax_zone_id = :tax_zone_id limit 1');
    $Qcheck->bindTable(':table_tax_rates', TABLE_TAX_RATES);
    $Qcheck->bindInt(':tax_zone_id', $Qzones->valueInt('geo_zone_id'));
    $Qcheck->execute();

    if ( $Qcheck->numberOfRows() === 1 ) {
      $check_tax_zones_flag[] = $Qzones->value('geo_zone_name');
    }

    $names_string .= osc_draw_hidden_field('batch[]', $Qzones->valueInt('geo_zone_id')) . '<b>' . $Qzones->value('geo_zone_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2) . osc_draw_hidden_field('subaction', 'confirm');
  }

  echo '<p>' . $names_string . '</p>';

  if ( empty($check_tax_zones_flag) ) {
    echo '<p align="center"><input type="submit" value="' . IMAGE_DELETE . '" class="operationButton" /> <input type="button" value="' . IMAGE_CANCEL . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  } else {
    echo '<p><b>' . TEXT_INFO_BATCH_DELETE_PROHIBITED_TAX_ZONES . '</b></p>' .
         '<p>' . implode(', ', $check_tax_zones_flag) . '</p>';

    echo '<p align="center"><input type="button" value="' . IMAGE_BACK . '" onclick="document.location.href=\'' . osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&page=' . $_GET['page']) . '\';" class="operationButton" /></p>';
  }
?>

  </form>
</div>