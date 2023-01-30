<?php
/**
 * @var $this yii\web\View
 * @var $tagManagerId string
 * @var $dataLayerItems array
 * @var $scriptInit string
 */

use yii\helpers\Html;

if ($tagManagerId === '') {
    return;
}

?>
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=<?= Html::encode($tagManagerId) ?>" height="0"
            width="0" style="display:none;visibility:hidden"></iframe>
</noscript>

<?php if ($scriptInit !== '') { ?>
    <script>
        <?php echo $scriptInit ?>
    </script>
<?php } ?>
