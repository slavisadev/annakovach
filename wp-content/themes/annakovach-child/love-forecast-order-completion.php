<?php
/**
 * Template Name: Love Forecast Order Success
 */

//Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
//include 'api/orm.php';
include_once 'api/cronEmail.php';
$error_status = true;

if (isset($_SESSION['l_user_id']) && $_SESSION['l_user_id'] != '') {

    $row = ORM::for_table('wp_user_details')->where('user_id', $_SESSION['l_user_id'])->find_one();

    $row->set(array("order_status" => "success"));

    if ($row->save()) {
        $error_status = false;

        $sendMail = new SendCronMail();
        $sendMail->sendLoveForecastOrderCompletionPdf($_SESSION['l_user_id']);

    } else {
        $error_status = true;
    }

} else {
    header('Location: https://annakovach.com/love-reading/');
    exit;
}

get_header();
get_template_part('inc/header-parts/main');

?>
    <section class="mainContent">
        <div class="container">
            <?php
            if (!$error_status) {
                ?>
                <p>Congratulations!</p>
                <p>You have successfully purchased your Full Birth Chart Interpretation!</p>
                <p>Please check your email within 24 hours. I will send you your reading from my email
                    <b>readings@annakovach.com</b></p>
                <?php
            } else {
                ?>
                <p>Error :(</p>
                <p>Due to some reason some error occurred.</p>
                <p>Please contact to Admin on the below given email <br>
                    <b>readings@annakovach.com</b></p>
                <?php

            }
            ?>

        </div>

    </section>
<?php
get_footer();