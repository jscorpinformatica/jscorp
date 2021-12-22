<?php
$content = '<span class="woocommerce-Price-currencySymbol">R$</span>';
echo preg_replace('/\<span class\=\"woocommerce\-Price\-currencySymbol\"\>R\$\<\/span\>/','</br><span class="woocommerce-Price-currencySymbol">R$</span>',$content);