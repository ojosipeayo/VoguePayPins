<?php 
/**
 * @package Vogue Pay PIN Dispenser
 */
/*
Plugin Name: Vogue Pay PIN Dispenser
Plugin URI: https://voguepay.com/wordpress
Description: The Vogue Pay PIN Dispenser allows you integrate a PIN Dispensing system into your wordpress while pins are loaded from your voguepay account.Get card payment via mastercard,visa.
Author: Ojosipe Ayomikun
Author URI: http://voguepay.com
License: GPLv3 or later
http://www.gnu.org/licenses/gpl-3.0.txt
*/


if (!class_exists("VoguePayPins")) {

  class VoguePayPins {
  
    var $Butt_Settings = "VoguePayButtonSettings";
  
    function VoguePayPins() { //constructor
       
    }
    
    function init() { $this->getButtSettings(); }
    
    function getButtSettings() {
      $VoguePay_ButtonSettings = array( 
          'button_color'      => 'blue',
          'alternate_button'      => '',
          'merchant_id' => '',
          'product_type' => 'pin'
        );

      $v_Options = get_option( $this->Butt_Settings );
      if ( !empty( $v_Options ) ) {
          foreach ( $v_Options as $k => $v )  $VoguePay_ButtonSettings[$k] = $v;
      }

      update_option($this->Butt_Settings, $VoguePay_ButtonSettings);
      return $VoguePay_ButtonSettings;
    }
    
    function echoSettingPage() {
      $v_Options = $this->getButtSettings();
      if (isset($_POST['save_settings_now'])) {
        if (isset($_POST['button_color'])) $v_Options['button_color'] = $_POST['button_color'];
    if (isset($_POST['alternate_button'])) $v_Options['alternate_button'] = $_POST['alternate_button'];
        if (isset($_POST['merchant_id'])) $v_Options['merchant_id'] = $_POST['merchant_id'];
        if (isset($_POST['product_type'])) $v_Options['product_type'] = $_POST['product_type'];


      update_option($this->Butt_Settings, $v_Options);
       
       echo '<div class="updated"><p><strong>';
         _e("Settings Updated.", "VoguePayPins");
       echo '</strong></p></div>';
      } 
    ?>

      <div class=wrap>
        <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
          <h2>Vogue Pay Pin Dispenser</h2>
          <h3>Your VoguePay Merchant ID</h3>
          <p>Enter your VoguePay Merchant ID, find it on the top right hand corner when you login to voguepay.com.</p>
          <input type="text" size="50" name="merchant_id" id="merchant_id" value="<?php _e(apply_filters('format_to_edit',$v_Options['merchant_id']), 'VoguePayPins'); ?>" />
          
          <h3>Button Colour</h3>
          <div>Select the button colour.</div>
          
          <div><select name="button_color" id="button_color">
            <option value="red" <?php if ($v_Options['button_color'] == "red") { _e('selected="selected"', "VoguePayPins"); }?> >Red</option>
            <option value="blue" <?php if ($v_Options['button_color'] == "blue") { _e('selected="selected"', "VoguePayPins"); }?> >Blue</option>
            <option value="green" <?php if ($v_Options['button_color'] == "green") { _e('selected="selected"', "VoguePayPins"); }?> >Green</option>
            <option value="grey"  <?php if ($v_Options['button_color'] == "grey") { _e('selected="selected"', "VoguePayPins"); }?>>Grey</option>
          </select>

          <h3>Product Type</h3>
          <div>Select the product type.</div>
          <div>
          <select name="product_type" id="product_type">
            <option value="pin" <?php if ($v_Options['product_type'] == "pin") { _e('selected="selected"', "VoguePayPins"); }?> >Pin Product</option>
            <option value="downloadable" <?php if ($v_Options['product_type'] == "downloadable") { _e('selected="selected"', "VoguePayPins"); }?> >Downloadable Product</option> 
          </select>
          </div></br>
          <div>Or enter the url of your image to use as  "Buy Now" button.</div>
          <div> <input type="text" size="50" name="alternate_button" id="alternate_button" value="<?php _e(apply_filters('format_to_edit',$v_Options['alternate_button']), 'VoguePayPins'); ?>" />
          </div>         
          <div class="submit">
          <input type="submit" name="save_settings_now" value="<?php _e('Update Settings', 'VoguePayPins') ?>" /></div>
        </form>
      </div>
       
    <?php
    }
      

    function getVoguePayButton ($p=''){ 
      extract( shortcode_atts( array( 'code' => '', 'item' => '' ), $p )); 
      $x = $this->getButtSettings(); 
      $p['merchant_id']  = $x['merchant_id'];
      $p['product_type'] = $x['product_type'];
      $p['alternate_button'] = empty($x['alternate_button']) ? 'http://voguepay.com/images/buttons/buynow_'.$x['button_color'].'.png' : $x['alternate_button'];
      return $this->makeButtShow( $p );
    }  

    function makeButtShow( $param )
    { 
      if ( $param['merchant_id'] != '' ){
        
     $form = '<form method="post" action="https://voguepay.com/pay/">
    Specify Quantity<br />
    <input type="text" name="total" style="width:120px" /><br />
    <input type="hidden" name="v_merchant_id" value="'.$param['merchant_id'].'" />
    <input type="hidden" name="memo" value="Payment for '.$param['item'].'" />';

    if ( $param['product_type'] == 'pin' ) $form .= '<input type="hidden" name="xid_code" value="'.$param['code'].'" />';
     if ( $param['product_type'] == 'downloadable' ) $form .= '<input type="hidden" name="vid_code" value="'.$param['code'].'" />';

    $form .= '<input type="hidden" name="developer_code" value="56e0022f80a0c" />
    <input type="image" style="border: 0;" name="submit" src="'.$param['alternate_button'].'" alt="Pay with VoguePay" />
    </form>';

    return $form;

  } else return'<div style="color: red;" >Please specify voguePay Merchant ID on plugin settings page for this plugin!</div>';
    }
  }
}

if (class_exists("VoguePayPins")) $v_VoguePayPins = new VoguePayPins();

if ( !function_exists("VoguePayPins_ap") ) { 
    function VoguePayPins_ap() { 
        global $v_VoguePayPins; 
        if ( !isset($v_VoguePayPins) )return; 
        if ( function_exists('add_options_page') )  add_options_page('Vogue Pay PIN Dispenser', 'Vogue Pay PIN Dispenser', 9, basename(__FILE__), array(&$v_VoguePayPins, 'echoSettingPage'));
    }   
}

//Actions and Filters   
if (isset($v_VoguePayPins)) { 
    add_action('admin_menu', 'VoguePayPins_ap'); 
    add_action('activate_VoguePayPins/VoguePayPins.php',  array(&$v_VoguePayPins, 'init')); 
    add_shortcode('voguepay', array(&$v_VoguePayPins, 'getVoguePayButton'), 1); 
} 
?>
