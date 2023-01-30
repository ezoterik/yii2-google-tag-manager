<?php
/**
 * @var $this yii\web\View
 * @var $tagManagerId string
 * @var $dataLayerItems array
 */

use yii\helpers\Html;

if (empty($tagManagerId)) {
    return;
}

//Adding a GTM prefix
$tagManagerId = 'GTM-' . $tagManagerId;
?>

<!-- Google Tag Manager (noscript) -->
<noscript>
  <iframe src="https://www.googletagmanager.com/ns.html?id=<?= Html::encode($tagManagerId) ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

<script>
  (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','<?= Html::encode($tagManagerId) ?>');
</script>
