<?php

namespace TheLion\UseyourDrive\Integrations;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Integrations
{
    /**
     * @var \TheLion\UseyourDrive\Main
     */
    private $_main;

    public function __construct(\TheLion\UseyourDrive\Main $main)
    {
        $this->_main = $main;
        $this->load_contactform7();
        $this->load_elementor();
        $this->load_gravityforms();
        $this->load_gravitypdf();
        $this->load_gutenberg();
        $this->load_woocommcerce();
        $this->load_wpforms();
    }

    public function load_contactform7()
    {
        if (!defined('WPCF7_PLUGIN')) {
            return false;
        }

        require_once 'contactform7/init.php';

        new ContactForm($this->_main);
    }

    public function load_elementor()
    {
        if (!did_action('elementor/loaded')) {
            return false;
        }
        require_once 'elementor/init.php';
    }

    public function load_gravityforms()
    {
        if (!class_exists('GFForms')) {
            return false;
        }

        require_once 'gravityforms/init.php';
    }

    public function load_gravitypdf()
    {
        if (!class_exists('GFForms')) {
            return false;
        }

        require_once 'gravitypdf/init.php';
    }

    public function load_gutenberg()
    {
        require_once 'gutenberg/init.php';
    }

    public function load_woocommcerce()
    {
        if (!class_exists('woocommerce')) {
            return false;
        }

        require_once 'woocommerce/init.php';
    }

    public function load_wpforms()
    {
        if (!defined('WPFORMS_VERSION')) {
            return false;
        }

        require_once 'wpforms/init.php';
    }
}
