<?php
/*
Plugin Name: Sendloop Subscribe
Plugin URI: http://sendloop.com/wordpress
Description: Add mail list subscription form to your WordPress powered blog or website
Version: 2.0.2
Author: Sendloop
Author URI: http://sendloop.com
License: GPL2
*/

/*
 *
 * Other:
 * TODO: Help section
 * TODO: Blank slate to the settings page for initial landing and new users
 * TODO: Sendloop account creating for non-users
 *
 * Sendloop:
 * TODO: http://sendloop.com/wordpress landing page
 * TODO: Detailed wordpress help page
 *
 * */

if (class_exists('Sendloop_Subscribe'))
{
	if (!isset($SendloopSubscribe))
	{
		$SendloopSubscribe = new Sendloop_Subscribe();
	}
}

class Sendloop_Subscribe_Form_Widget extends WP_Widget
{
	private $PluginPath = '';

	function __construct()
	{
		parent::__construct(false, 'Sendloop Subscription Form');

		$this->PluginPath = plugin_dir_path(__FILE__);

		$this->ViewLoader = new Sendloop_ViewLoader($this->PluginPath, plugins_url('sendloop'));
	}

	function widget($args, $instance)
	{
		global $SendloopSubscribe;

		$Sendloop_APIKey = get_option('sendloop_apikey', false);
		$Sendloop_TargetListID = get_option('sendloop_target_listid', false);
		$SubscriptionFormType = get_option('sendloop_form_type', 'Link');
		$CustomCSS = get_option('sendloop_customcss', '');
		$TargetList = json_decode(get_option('sendloop_targetlist', false));

		$SubscriptionFormSettings = json_decode(get_option('sendloop_form_settings', json_encode(new stdClass())));

		$IsSetupCompleted = true;
		if ($Sendloop_APIKey == false || $Sendloop_TargetListID == false)
		{
			$IsSetupCompleted = false;
		}

		if ($IsSetupCompleted == true && is_bool($TargetList) == true && $TargetList == false)
		{
			$TargetList = $SendloopSubscribe->API_GetList($Sendloop_APIKey, $Sendloop_TargetListID);

			if (is_bool($TargetList) == true && $TargetList == false)
			{
				$IsSetupCompleted = false;
				$TargetList = null;
			}
			else
			{
				update_option('sendloop_targetlist', json_encode($TargetList));
			}
		}
		elseif ($IsSetupCompleted == true && is_object($TargetList) == true)
		{
			$IsSetupCompleted = true;
		}
		else
		{
			$IsSetupCompleted = false;
			$TargetList = null;
		}

		$SubscriptionFormSettings = json_decode(get_option('sendloop_form_settings', json_encode(new stdClass())));

		$CustomFields = array();
		if (isset($TargetList->CustomFields) == true && count($TargetList->CustomFields) > 0)
		{
			foreach ($TargetList->CustomFields as $Index=>$EachCustomField)
			{
				$CustomFields['CustomField'.$EachCustomField->CustomFieldID] = $EachCustomField;
			}
		}

		$this->ViewLoader->Load('widget_output', array(
			'IsSetupCompleted' => $IsSetupCompleted,
			'TargetList' => $TargetList,
			'SubscriptionFormType' => $SubscriptionFormType,
			'SubscriptionFormSettings' => $SubscriptionFormSettings,
			'CustomCSS' => $CustomCSS,
			'CustomFields' => $CustomFields,
		));
	}

	function update($new_instance, $old_instance)
	{
		$SubscriptionFormSettings = json_decode(get_option('sendloop_form_settings', json_encode(new stdClass())));

		$SubscriptionFormSettings->FormTitle = (isset($_POST['FormTitle']) == true ? $_POST['FormTitle'] : '');
		$SubscriptionFormSettings->Message = (isset($_POST['Message']) == true ? $_POST['Message'] : '');
		$SubscriptionFormSettings->Fields = (isset($_POST['Fields']) == true ? $_POST['Fields'] : array('EmailAddress'));
		$SubscriptionFormSettings->DisplayBadge = (isset($_POST['DisplayBadge']) == true && $_POST['DisplayBadge'] == 'yes' ? true : false);
		$SubscriptionFormSettings->SubmitOnNewWindow = (isset($_POST['SubmitOnNewWindow']) == true && $_POST['SubmitOnNewWindow'] == 'yes' ? true : false);
		$SubscriptionFormSettings->UseAJAX = (isset($_POST['UseAJAX']) == true && $_POST['UseAJAX'] == 'yes' ? true : false);
		$SubscriptionFormSettings->UseOverlay = (isset($_POST['UseOverlay']) == true && $_POST['UseOverlay'] == 'yes' ? true : false);
		$SubscriptionFormSettings->LinkText = (isset($_POST['LinkText']) == true ? $_POST['LinkText'] : '');
		$SubscriptionFormSettings->LinkIconURL = (isset($_POST['LinkIconURL']) == true ? $_POST['LinkIconURL'] : '');
		$SubscriptionFormSettings->LinkImageURL = (isset($_POST['LinkImageURL']) == true ? $_POST['LinkImageURL'] : '');

		update_option('sendloop_form_settings', json_encode($SubscriptionFormSettings));

		return $new_instance;
	}

	function form($instance)
	{
		global $SendloopSubscribe;

		$Sendloop_APIKey = get_option('sendloop_apikey', false);
		$Sendloop_TargetListID = get_option('sendloop_target_listid', false);
		$SubscriptionFormType = get_option('sendloop_form_type', 'Link');

		$SubscriptionFormSettings = json_decode(get_option('sendloop_form_settings', json_encode(new stdClass())));

		$IsSetupCompleted = true;
		if ($Sendloop_APIKey == false || $Sendloop_TargetListID == false)
		{
			$IsSetupCompleted = false;
		}

		if ($IsSetupCompleted == true)
		{
			$TargetList = $SendloopSubscribe->API_GetList($Sendloop_APIKey, $Sendloop_TargetListID);
		}
		else
		{
			$TargetList = null;
		}

		$this->ViewLoader->Load('widget_settings', array(
			'IsSetupCompleted' => $IsSetupCompleted,
			'TargetList' => $TargetList,
			'SubscriptionFormType' => $SubscriptionFormType,
			'SubscriptionFormSettings' => $SubscriptionFormSettings,
		));
	}
}

class Sendloop_Subscribe
{
	private $Sendloop_APIKey = false;
	private $PluginPath = '';
	private $PluginURL = '';
	private $PageMessage = array(); // 'Type' => success|error , 'Message => 'the error message'
	private $Sendloop_SubscriberLists = array();
	private $Sendloop_APIKey_Verified = false;
	private $Sendloop_TargetListID = false;
	private $AvailableFormTypes = array('Standard', 'Link');
	private $ViewLoader = null;
	private $Default_ShortcodeWidgetCSS = <<<EOF
border: 1px solid;
margin: 15px 0px;
padding:15px 20px;
width: 100%;
font-weight: bold;
font-size: 12px;
-webkit-border-radius: 5px;
-moz-border-radius: 5px;
border-radius: 5px;
color: #00529B;
background: #BDE5F8;
EOF;

	function __construct()
	{
		$this->PluginPath = plugin_dir_path(__FILE__);
		$this->PluginURL = plugin_dir_url(__FILE__);

		register_activation_hook(__FILE__, array($this, 'ActivatePlugin'));
		register_deactivation_hook(__FILE__, array($this, 'DeActivatePlugin'));

		if (is_admin() == true)
		{
			add_action('admin_menu', array($this, 'AddMenuItems'));
			add_action('admin_init', array($this, 'InitSystem'));

			add_filter('plugin_action_links_'.plugin_basename(__FILE__), array($this, 'PutSettingsLink'));
		}

		add_action('wp_enqueue_scripts', array($this, 'AddRequiredCSSandJSToPages'));
		add_action('wp_footer', array($this, 'AddWidgetCode' ), 10);

		add_action('widgets_init', array($this, 'RegisterWidget'));

		add_shortcode('sendloop', array($this, 'ShortCodeParsing'));

		include_once($this->PluginPath.'helpers/sendloopapi3.php');

		$this->ViewLoader = new Sendloop_ViewLoader($this->PluginPath, plugins_url('sendloop'));
	}

	function ActivatePlugin()
	{
		if (is_bool($this->Sendloop_APIKey) == false || $this->Sendloop_APIKey != false || $this->Sendloop_APIKey != '')
		{
			update_option('sendloop_apikey', '');
			update_option('sendloop_target_listid', '');
		}
	}

	function DeActivatePlugin()
	{
		delete_option('sendloop_apikey');
		delete_option('sendloop_target_listid');
		delete_option('sendloop_form_type');
		delete_option('sendloop_form_settings');
		delete_option('sendloop_customcss');
		delete_option('sendloop_shortcodecustomcss');
		delete_option('sendloop_targetlist');
	}

	function InitSystem()
	{
		if (get_option('sendloop_form_type', false) === false)
		{
			update_option('sendloop_form_type', 'Standard');
		}
		if (get_option('sendloop_customcss', false) === false)
		{
			update_option('sendloop_customcss', '');
		}
		if (get_option('sendloop_shortcodecustomcss', false) === false)
		{
			update_option('sendloop_shortcodecustomcss', $this->Default_ShortcodeWidgetCSS);
		}
		if (get_option('sendloop_form_settings', false) === false)
		{
			$SubscriptionFormSettings = new stdClass();
			$SubscriptionFormSettings->FormTitle = 'Subscribe to mail list';
			$SubscriptionFormSettings->Message = 'To get notified regularly about changes, subscribe to our mail list below:';
			$SubscriptionFormSettings->Fields = array('EmailAddress');
			$SubscriptionFormSettings->DisplayBadge = true;
			$SubscriptionFormSettings->SubmitOnNewWindow = false;
			$SubscriptionFormSettings->UseAJAX = false;
			$SubscriptionFormSettings->UseOverlay = false;
			$SubscriptionFormSettings->LinkText = 'Click here to subscribe to our mail list';
			$SubscriptionFormSettings->LinkIconURL = '';
			$SubscriptionFormSettings->LinkImageURL = '';

			update_option('sendloop_form_settings', json_encode($SubscriptionFormSettings));
		}
	}

	function RegisterWidget()
	{
		register_widget('Sendloop_Subscribe_Form_Widget');
	}

	function AddMenuItems()
	{
		add_options_page('Sendloop Settings', 'Sendloop Settings', 'manage_options', 'sendloop-settings', array($this, 'ShowSettingsPage'));

		add_object_page('Mail List', 'Mail List', 'edit_posts', 'sendloop-maillist', array($this, 'ShowIntroduction'), $this->PluginURL.'images/icon_maillistx16.png');
		add_submenu_page('sendloop-maillist', '', '', 'edit_posts', 'sendloop-maillist', array($this, 'ShowIntroduction'));
//		add_submenu_page('sendloop-maillist', 'Recent Subscriptions', 'Recent Subscriptions', 'edit_posts', 'sendloop-recent-subscriptions', array($this, 'ShowMailListReports'));
//		add_submenu_page('sendloop-maillist', 'Email Campaigns', 'Email Campaigns', 'edit_posts', 'sendloop-email-campaigns', array($this, 'ShowEmailCampaigns'));
		add_submenu_page('sendloop-maillist', 'Settings', 'Settings', 'edit_posts', 'sendloop-in-settings', array($this, 'ShowSettingsPage'));
		add_submenu_page('sendloop-maillist', 'Help', 'Help', 'edit_posts', 'sendloop-help', array($this, 'ShowHelp'));
	}

	function ShortCodeParsing($Attributes, $Content = null)
	{
		$Sendloop_APIKey = get_option('sendloop_apikey', false);
		$Sendloop_TargetListID = get_option('sendloop_target_listid', false);
		$SubscriptionFormType = get_option('sendloop_form_type', 'Link');
		$CustomCSS = get_option('sendloop_customcss', '');
		$ShortcodeCustomCSS = get_option('sendloop_shortcodecustomcss', '');
		$TargetList = json_decode(get_option('sendloop_targetlist', false));

		$SubscriptionFormSettings = json_decode(get_option('sendloop_form_settings', json_encode(new stdClass())));

		$IsSetupCompleted = true;
		if ($Sendloop_APIKey == false || $Sendloop_TargetListID == false)
		{
			$IsSetupCompleted = false;
		}

		if ($IsSetupCompleted == false)
		{
			return '';
		}

		// Defaults
		$listid = 0;
		$overlay = false;
		$link = true;
		$show_counter = false;
		$link_text = '';

		extract(shortcode_atts(array(
			'listid' => $Sendloop_TargetListID,
			'overlay' => false,
			'link' => true,
			'show_counter' => false,
			'link_text' => 'Click here to subscribe to my mail list'
		), $Attributes));

		if ($listid != $Sendloop_TargetListID && $listid > 0)
		{
			$TargetList = $this->API_GetList($Sendloop_APIKey, $listid);

			if (is_bool($TargetList) == true && $TargetList == false)
			{
				$TargetList = json_decode(get_option('sendloop_targetlist', false));
			}
		}

		return $this->ViewLoader->Load('shortcode_widget', array(
			'IsSetupCompleted' => $IsSetupCompleted,
			'SubscriptionFormSettings' => $SubscriptionFormSettings,
			'ListID' => $Sendloop_TargetListID,
			'IsOverlay' => $overlay,
			'IsLink' => $link,
			'ShowCounter' => $show_counter,
			'LinkText' => $link_text,
			'TargetList' => $TargetList,
			'ShortcodeCustomCSS' => $ShortcodeCustomCSS,
		), true);
	}

	function AddWidgetCode()
	{
		$Sendloop_APIKey = get_option('sendloop_apikey', false);
		$Sendloop_TargetListID = get_option('sendloop_target_listid', false);
		$SubscriptionFormType = get_option('sendloop_form_type', 'Link');
		$CustomCSS = get_option('sendloop_customcss', '');
		$ShortcodeCustomCSS = get_option('sendloop_shortcodecustomcss', '');
		$TargetList = json_decode(get_option('sendloop_targetlist', false));

		$SubscriptionFormSettings = json_decode(get_option('sendloop_form_settings', json_encode(new stdClass())));

		$IsSetupCompleted = true;
		if ($Sendloop_APIKey == false || $Sendloop_TargetListID == false)
		{
			$IsSetupCompleted = false;
		}

		if ($IsSetupCompleted == true && is_bool($TargetList) == true && $TargetList == false)
		{
			$TargetList = $this->API_GetList($Sendloop_APIKey, $Sendloop_TargetListID);

			if (is_bool($TargetList) == true && $TargetList == false)
			{
				$IsSetupCompleted = false;
				$TargetList = null;
			}
			else
			{
				update_option('sendloop_targetlist', json_encode($TargetList));
			}
		}
		elseif ($IsSetupCompleted == true && is_object($TargetList) == true)
		{
			$IsSetupCompleted = true;
		}
		else
		{
			$IsSetupCompleted = false;
			$TargetList = null;
		}

		$SubscriptionFormSettings = json_decode(get_option('sendloop_form_settings', json_encode(new stdClass())));

		$this->ViewLoader->Load('post_widget', array(
			'IsSetupCompleted' => $IsSetupCompleted,
			'TargetList' => $TargetList,
			'SubscriptionFormType' => $SubscriptionFormType,
			'SubscriptionFormSettings' => $SubscriptionFormSettings,
			'CustomCSS' => $CustomCSS,
			'ShortcodeCustomCSS' => $ShortcodeCustomCSS,
		));
	}

	function AddRequiredCSSandJSToPages()
	{
		wp_enqueue_script('sendloop-inpage-script', plugins_url('/js/widget.js', __FILE__), array(), false, true);
		wp_enqueue_style('sendloop-inpage-style', plugins_url('/css/widget.css', __FILE__), array(), false, 'all');
	}

	function PutSettingsLink($Links)
	{
		unset($Links['edit']);
		$SettingsLink = '<a href="options-general.php?page=sendloop-settings">Settings</a>';
		array_unshift($Links, $SettingsLink);
		return $Links;
	}

	function ShowEmailCampaigns()
	{
		print 'campaigns';
	}

	function ShowMailListReports()
	{
		$this->Sendloop_APIKey = get_option('sendloop_apikey', false);
		$this->Sendloop_TargetListID = get_option('sendloop_target_listid', false);

		if ($this->Sendloop_APIKey != false && $this->Sendloop_TargetListID != false)
		{
			$Lists = $this->API_GetLists($this->Sendloop_APIKey);

			$APIError = false;
			if (is_bool($Lists) == true && $Lists == false)
			{
				$APIError = true;
			}

			$TargetList = $this->API_GetList($this->Sendloop_APIKey, $this->Sendloop_TargetListID);
			if (is_bool($TargetList) == true && $TargetList == false)
			{
				$APIError = true;
			}

			if ($APIError == false)
			{
				$this->Sendloop_APIKey_Verified = true;
				$this->Sendloop_SubscriberLists = $Lists;
			}
		}

		$this->ViewLoader->Load('maillist_reports', array(
			'PageMessage' => $this->PageMessage,
			'SubscriberLists' => $this->Sendloop_SubscriberLists,
		));
	}

	function ShowHelp()
	{
		$this->ViewLoader->Load('help', array(
			'PageMessage' => $this->PageMessage,
		));
	}

	function ShowIntroduction()
	{
		$Sendloop_APIKey = get_option('sendloop_apikey', false);
		$Sendloop_TargetListID = get_option('sendloop_target_listid', false);

		$IsSetupCompleted = true;
		if ($Sendloop_APIKey == false || $Sendloop_TargetListID == false)
		{
			$IsSetupCompleted = false;
		}

		$this->ViewLoader->Load('introduction', array(
			'PageMessage' => $this->PageMessage,
			'IsSetupCompleted' => $IsSetupCompleted,
		));
	}

	function ShowSettingsPage()
	{
		$this->Sendloop_APIKey = get_option('sendloop_apikey', false);
		$this->Sendloop_TargetListID = get_option('sendloop_target_listid', false);

		if ($this->Sendloop_APIKey != false && $this->Sendloop_TargetListID != false)
		{
			$Lists = $this->API_GetLists($this->Sendloop_APIKey);

			$APIError = false;
			if (is_bool($Lists) == true && $Lists == false)
			{
				$APIError = true;
			}

			$TargetList = $this->API_GetList($this->Sendloop_APIKey, $this->Sendloop_TargetListID);
			if (is_bool($TargetList) == true && $TargetList == false)
			{
				$APIError = true;
			}

			if ($APIError == false)
			{
				$this->Sendloop_APIKey_Verified = true;
				$this->Sendloop_SubscriberLists = $Lists;
			}
		}

		if (isset($_GET['reset_shortcode_css']) == true && $_GET['reset_shortcode_css'] == true)
		{
			update_option('sendloop_shortcodecustomcss', $this->Default_ShortcodeWidgetCSS);
		}

		$this->_EventConnectAndGetLists();
		$this->_EventSaveSettings();

		$this->ViewLoader->Load('settings', array(
			'PageMessage' => $this->PageMessage,
			'SubscriberLists' => $this->Sendloop_SubscriberLists,
			'ShowStep2' => ($this->Sendloop_APIKey_Verified == true ? true : false)
		));
	}

	private function _EventSaveSettings()
	{
		if (isset($_POST['SaveSettings']) == false || $_POST['SaveSettings'] == '')
		{
			return;
		}

		if ($this->ValidateFormField('SendloopAPIKey', 'POST', array('required')) == false)
		{
			$this->PageMessage = array(
				'Type' => 'error',
				'Message' => 'You must enter your Sendloop API key to continue. If you don\'t know your API key, <a href="http://sendloop.com/help/article/api-001/getting-started" target="_blank">click here</a> to learn how to get it.',
			);
			return false;
		}

		$Lists = $this->API_GetLists($_POST['SendloopAPIKey']);

		if (is_bool($Lists) == true && $Lists == false)
		{
			$this->PageMessage = array(
				'Type' => 'error',
				'Message' => 'Couldn\'t connect to your Sendloop account with the API key you have provided. Please be sure that it\'s correct.',
			);
			return false;
		}

		$this->Sendloop_APIKey_Verified = true;

		$this->Sendloop_SubscriberLists = $Lists;

		if ($this->ValidateFormField('TargetListID', 'POST', array('required')) == false)
		{
			$this->PageMessage = array(
				'Type' => 'error',
				'Message' => 'Please select one of the available subscriber lists as a target list to subscribe your visitors',
			);
			return false;
		}

		if ($_POST['TargetListID'] == 'New' && $this->ValidateFormField('NewListName', 'POST', array('required')) == false)
		{
			$this->PageMessage = array(
				'Type' => 'error',
				'Message' => 'Enter a name for your new subscriber list and try again later.',
			);
			return false;
		}

		if ($_POST['TargetListID'] == 'New')
		{
			$SelectedListID = $this->API_CreateList($_POST['SendloopAPIKey'], $_POST['NewListName']);
			if (is_bool($SelectedListID) == true && $SelectedListID === false)
			{
				$this->PageMessage = array(
					'Type' => 'error',
					'Message' => 'Something weird has happened and the new list couldn\'t be created. Please try again. If the problem continues, please contact the Sendloop Team via <a href="mailto:hello@sendloop.com?Subject=WordPress%20plugin%20new%20list%20create%20issue">hello@sendloop.com</a>',
				);
				return false;
			}
		}
		else
		{
			$SelectedListID = $_POST['TargetListID'];
		}

		update_option('sendloop_apikey', $_POST['SendloopAPIKey']);
		update_option('sendloop_target_listid', $SelectedListID);
		update_option('sendloop_form_type', (isset($_POST['FormType']) == true && in_array($_POST['FormType'], $this->AvailableFormTypes) == true ? $_POST['FormType'] : get_option('sendloop_form_type')));
		update_option('sendloop_customcss', (isset($_POST['CustomCSS']) == true ? $_POST['CustomCSS'] : get_option('sendloop_customcss')));
		update_option('sendloop_shortcodecustomcss', (isset($_POST['ShortcodeCustomCSS']) == true ? $_POST['ShortcodeCustomCSS'] : get_option('sendloop_shortcodecustomcss')));

		$TargetList = $this->API_GetList($_POST['SendloopAPIKey'], $SelectedListID);

		if (is_bool($TargetList) == true && $TargetList == false)
		{
			$this->PageMessage = array(
				'Type' => 'error',
				'Message' => 'Failed to get the information about the selected subscriber list. Please try again.',
			);
			return false;
		}
		else
		{
			update_option('sendloop_targetlist', json_encode($TargetList));
		}

		$_POST['TargetListID'] = $SelectedListID;
		unset($_POST['NewListName']);

		$this->Sendloop_SubscriberLists = $this->API_GetLists($_POST['SendloopAPIKey']);

		$this->PageMessage = array(
			'Type' => 'success',
			'Message' => 'Nice! Settings have been saved.',
		);
		return;
	}

	private function _EventConnectAndGetLists()
	{
		if (isset($_POST['ConnectToSendloop']) == false || $_POST['ConnectToSendloop'] == '')
		{
			return;
		}

		if ($this->ValidateFormField('SendloopAPIKey', 'POST', array('required')) == false)
		{
			$this->PageMessage = array(
				'Type' => 'error',
				'Message' => 'You must enter your Sendloop API key to continue. If you don\'t know your API key, <a href="http://sendloop.com/help/article/api-001/getting-started" target="_blank">click here</a> to learn how to get it.',
			);
			return false;
		}

		// Connect to Sendloop API and try to get the list of subscriber lists

		$Lists = $this->API_GetLists($_POST['SendloopAPIKey']);

		if (is_bool($Lists) == true && $Lists == false)
		{
			$this->PageMessage = array(
				'Type' => 'error',
				'Message' => 'Couldn\'t connect to your Sendloop account with the API key you have provided. Please be sure that it\'s correct.',
			);
			return false;
		}

		$this->Sendloop_APIKey_Verified = true;

		$this->Sendloop_SubscriberLists = $Lists;

		return;
	}

	public function API_GetList($APIKey, $ListID)
	{
		$Sendloop_API = new SendloopAPI3($APIKey, null, 'php');
		$Sendloop_API->run('List.Get', array('ListID' => $ListID, 'GetCustomFields' => true));

		if (isset($Sendloop_API->Result['Success']) == false || $Sendloop_API->Result['Success'] == false)
		{
			return false;
		}

		if (isset($Sendloop_API->Result['CustomFields']) == true && count($Sendloop_API->Result['CustomFields']) > 0)
		{
			$Sendloop_API->Result['List']['CustomFields'] = $Sendloop_API->Result['CustomFields'];
		}

		return $Sendloop_API->Result['List'];
	}

	public function API_GetLists($APIKey)
	{
		$Sendloop_API = new SendloopAPI3($APIKey, null, 'php');
		$Sendloop_API->run('List.GetList');

		if (isset($Sendloop_API->Result['Success']) == false || $Sendloop_API->Result['Success'] == false)
		{
			return false;
		}

		return $Sendloop_API->Result['Lists'];
	}

	public function API_CreateList($APIKey, $ListName)
	{
		$Sendloop_API = new SendloopAPI3($APIKey, null, 'php');
		$Sendloop_API->run('List.Create', array('Name' => $ListName));

		if (isset($Sendloop_API->Result['Success']) == false || $Sendloop_API->Result['Success'] == false)
		{
			return false;
		}

		return $Sendloop_API->Result['ListID'];
	}

	private function ValidateFormField($FieldName, $ValidateWhat = 'POST', $ValidationRules = array())
	{
		if ($ValidateWhat == 'POST')
		{
			if (in_array('required', $ValidationRules) == true)
			{
				if (isset($_POST[$FieldName]) == false || $_POST[$FieldName] == '')
				{
					return false;
				}
			}
		}

		return true;
	}
}

class Sendloop_ViewLoader
{
	private $PluginPath = '';
	private $PluginURL = '';

	function __construct($PluginPath, $PluginURL)
	{
		$this->PluginPath = $PluginPath;
		$this->PluginURL = $PluginURL;
	}

	public function Load($ViewFile, $ViewData = array(), $Return = false)
	{
		if (preg_match('/\.php$/i', $ViewFile) == false)
		{
			$ViewFile .= '.php';
		}

		ob_start();

		$ViewData['PluginURL'] = $this->PluginURL;

		extract($ViewData);

		include($this->PluginPath.'views/'.$ViewFile);

		if ($Return == false)
		{
			ob_end_flush();
		}
		else
		{
			$Buffer = ob_get_contents();
			@ob_end_clean();
			return $Buffer;
		}
	}

}