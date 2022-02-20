<?php
/**
 * @package     VikBooking
 * @subpackage  com_vikbooking
 * @author      Alessio Gaggii - e4j - Extensionsforjoomla.com
 * @copyright   Copyright (C) 2018 e4j - Extensionsforjoomla.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @link        https://vikwp.com
 */

defined('ABSPATH') or die('No script kiddies please!');

// import Joomla view library
jimport('joomla.application.component.view');

class VikBookingViewConfig extends JViewVikBooking {
	
	function display($tpl = null) {
		// Set the toolbar
		$this->addToolBar();

		/**
		 * @wponly - trigger back up of extendable files
		 */
		VikBookingLoader::import('update.manager');
		VikBookingUpdateManager::triggerExtendableClassesBackup('smsapi');
		//

		$dbo = JFactory::getDBO();
		$preset_tags = VikRequest::getInt('reset_tags', '', 'request');
		if ($preset_tags > 0) {
			$q = "UPDATE `#__vikbooking_orders` SET `colortag`=NULL;";
			$dbo->setQuery($q);
			$dbo->execute();
			$q = "UPDATE `#__vikbooking_config` SET `setting`='' WHERE `param`='bookingsctags';";
			$dbo->setQuery($q);
			$dbo->execute();
			$mainframe = JFactory::getApplication();
			$mainframe->redirect("index.php?option=com_vikbooking&task=config");
			exit;
		}

		$cookie = JFactory::getApplication()->input->cookie;
		$curtabid = $cookie->get('vbConfPt', '', 'string');
		$curtabid = empty($curtabid) ? 1 : (int)$curtabid;

		/**
		 * Pre-select one specific tab via query string.
		 * 
		 * @since 	1.14 (J) - 1.4.0 (WP)
		 */
		$tab = VikRequest::getInt('tab', 0, 'request');
		if (!empty($tab)) {
			$curtabid = $tab;
		}
		//

		$this->curtabid = &$curtabid;
		
		// Display the template
		parent::display($tpl);
	}

	/**
	 * Sets the toolbar
	 */
	protected function addToolBar() {
		JToolBarHelper::title(JText::translate('VBMAINCONFIGTITLE'), 'vikbookingconfig');
		if (JFactory::getUser()->authorise('core.edit', 'com_vikbooking')) {
			JToolBarHelper::apply( 'saveconfig', JText::translate('VBSAVE'));
			JToolBarHelper::spacer();
		}
		JToolBarHelper::cancel( 'cancel', JText::translate('VBANNULLA'));
		JToolBarHelper::spacer();
	}

}
