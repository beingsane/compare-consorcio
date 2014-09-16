<?php

class Compare_Front
{
    private static $initiated = false;

    public static function init()
    {
        if (! self::$initiated) {
            self::init_hooks();
        }
    }

    public static function init_hooks()
    {
        self::$initiated = true;
        add_shortcode( 'compare_form', array('Compare_Front','shortcode_form') );

    }

    public static function shortcode_form($attrs)
    {
        extract( shortcode_atts( array(
            'label' => __( 'Financiamento x Consórcio', 'compare-consorcio' )
        ), $attrs ) );

        ob_start();
        //self::deliver_mail();
        self::html_form_code();

        return ob_get_clean();
    }

    public static function html_form_code()
    {
        echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
        echo '<p>';
        echo 'Your Name (required) <br/>';
        echo '<input type="text" name="cf-name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["cf-name"] ) ? esc_attr( $_POST["cf-name"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Your Email (required) <br/>';
        echo '<input type="email" name="cf-email" value="' . ( isset( $_POST["cf-email"] ) ? esc_attr( $_POST["cf-email"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Subject (required) <br/>';
        echo '<input type="text" name="cf-subject" pattern="[a-zA-Z ]+" value="' . ( isset( $_POST["cf-subject"] ) ? esc_attr( $_POST["cf-subject"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p>';
        echo 'Your Message (required) <br/>';
        echo '<textarea rows="10" cols="35" name="cf-message">' . ( isset( $_POST["cf-message"] ) ? esc_attr( $_POST["cf-message"] ) : '' ) . '</textarea>';
        echo '</p>';
        echo '<p><input type="submit" name="cf-submitted" value="Send"></p>';
        echo '</form>';
    }

    public static function deliver_mail()
    {
        // if the submit button is clicked, send the email
        if ( isset( $_POST['cf-submitted'] ) ) {

            // sanitize form values
            $name    = sanitize_text_field( $_POST["cf-name"] );
            $email   = sanitize_email( $_POST["cf-email"] );
            $subject = sanitize_text_field( $_POST["cf-subject"] );
            $message = esc_textarea( $_POST["cf-message"] );

            // get the blog administrator's email address
            $to = get_option( 'admin_email' );

            $headers = "From: $name <$email>" . "\r\n";

            // If email has been process for sending, display a success message
            if ( wp_mail( $to, $subject, $message, $headers ) ) {
                echo '<div>';
                echo '<p>Thanks for contacting me, expect a response soon.</p>';
                echo '</div>';
            } else {
                echo 'An unexpected error occurred';
            }
        }
    }
}
