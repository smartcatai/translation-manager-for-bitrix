<?php
/**
 * @package    Smartcat Translation Manager for Bitrix
 *
 * @author     Smartcat <support@smartcat.ai>
 * @copyright  (c) 2019 Smartcat. All Rights Reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 * @link       https://smartcat.ai
 */

    CJSCore::Init(array("jquery"));
?>

<!--[if lte IE 8]>
<script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2-legacy.js"></script>
<![endif]-->
<script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2.js"></script>
<script>
hbspt.forms.create({
    portalId: "4950983",
    formId: "5da21dc3-49d9-49bb-aec1-2272338dbdcb",
    onFormReady: function($form){
        $form.find('input[name="utm_source"]').val('connectors');
        $form.find('input[name="utm_medium"]').val('referral');
        $form.find('input[name="utm_campaign"]').val('bitrix');
    },
    onFormSubmit: function(){
        window.location.href = '<?php echo $APPLICATION->GetCurPage() ?>';
    }
});
</script>