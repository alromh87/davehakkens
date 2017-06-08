<?php
 /* Template Name: Nonce For Auth */
echo '{"nonce":"' . wp_create_nonce( 'wp_rest' ). '"}';
?>

