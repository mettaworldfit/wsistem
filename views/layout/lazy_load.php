<?php if (str_contains($_SERVER["REQUEST_URI"], "home")) {

    ?>
    <!-- ChartJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

    <script src="<?= base_url ?>public/functions/home.js" type="text/javascript"></script>
    <?php

} elseif (
    str_contains($_SERVER["REQUEST_URI"], "invoices/addpurchase") ||
    str_contains($_SERVER["REQUEST_URI"], "invoices")
) {

    ?>
    <script src="<?= base_url ?>public/functions/invoices.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/pieces.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/products.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/services.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/contacts.js" type="text/javascript"></script>
    <?php

} elseif (
    str_contains($_SERVER["REQUEST_URI"], "invoices/index") ||
    str_contains($_SERVER["REQUEST_URI"], "invoices/index_repair")
) {

    ?>
    <script src="<?= base_url ?>public/functions/invoices.js" type="text/javascript"></script>
    <?php

} elseif (
    str_contains($_SERVER["REQUEST_URI"], "products/index") ||
    str_contains($_SERVER["REQUEST_URI"], "products")
) {

    ?>
    <script src="<?= base_url ?>public/functions/products.js" type="text/javascript"></script>
    <?php

} elseif (
    str_contains($_SERVER["REQUEST_URI"], "pieces/index") ||
    str_contains($_SERVER["REQUEST_URI"], "pieces")
) {

    ?>
    <script src="<?= base_url ?>public/functions/pieces.js" type="text/javascript"></script>
    <?php

} elseif (str_contains($_SERVER["REQUEST_URI"], "workshop/index")) {

    ?>
    <script src="<?= base_url ?>public/functions/workshop.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/contacts.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/repair.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/pieces.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/services.js" type="text/javascript"></script>
    <?php

} elseif (str_contains($_SERVER["REQUEST_URI"], "warehouses")) {

    ?>
    <script src="<?= base_url ?>public/functions/warehouses.js" type="text/javascript"></script>
    <?php

} elseif (str_contains($_SERVER["REQUEST_URI"], "categories")) {

    ?>
    <script src="<?= base_url ?>public/functions/categories.js" type="text/javascript"></script>
    <?php

} elseif (str_contains($_SERVER["REQUEST_URI"], "taxes")) {

    ?>
    <script src="<?= base_url ?>public/functions/taxes.js" type="text/javascript"></script>
    <?php

} elseif (str_contains($_SERVER["REQUEST_URI"], "contacts")) {

    ?>
    <script src="<?= base_url ?>public/functions/contacts.js" type="text/javascript"></script>
    <?php

} else {

    ?>

    <script src="<?= base_url ?>public/functions/positions.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/price_lists.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/offers.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/bills.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/payments.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/config.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/reports.js" type="text/javascript"></script>
    <script src="<?= base_url ?>public/functions/services.js" type="text/javascript"></script>
    <?php

}