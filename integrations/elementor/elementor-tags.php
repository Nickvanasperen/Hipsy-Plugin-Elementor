<?php
/**
 * Elementor Dynamic Tag Classes
 */

if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Controls_Manager;

// ═══════════════════════════════════════════════════════
// EVENT TITLE TAG
// ═══════════════════════════════════════════════════════

class Hipsy_Event_Title_Tag extends Tag {
    
    public function get_name() {
        return 'hipsy-event-title';
    }
    
    public function get_title() {
        return 'Event Titel';
    }
    
    public function get_group() {
        return 'hipsy-events';
    }
    
    public function get_categories() {
        return [ TagsModule::TEXT_CATEGORY ];
    }
    
    public function render() {
        echo esc_html( get_the_title() );
    }
}

// ═══════════════════════════════════════════════════════
// EVENT DATE TAG
// ═══════════════════════════════════════════════════════

class Hipsy_Event_Date_Tag extends Tag {
    
    public function get_name() {
        return 'hipsy-event-date';
    }
    
    public function get_title() {
        return 'Event Datum';
    }
    
    public function get_group() {
        return 'hipsy-events';
    }
    
    public function get_categories() {
        return [ TagsModule::TEXT_CATEGORY ];
    }
    
    protected function register_controls() {
        $this->add_control(
            'format',
            [
                'label' => 'Format',
                'type' => Controls_Manager::SELECT,
                'default' => 'd M Y',
                'options' => [
                    'd M Y' => '03 Mei 2025',
                    'd-m-Y' => '03-05-2025',
                    'l j F Y' => 'Zaterdag 3 Mei 2025',
                    'D j M' => 'Za 3 Mei',
                ],
            ]
        );
    }
    
    public function render() {
        $date_raw = get_post_meta( get_the_ID(), 'hipsy_events_date', true );
        if ( empty( $date_raw ) ) return;
        
        $date_obj = DateTime::createFromFormat( 'Y-m-d\TH:i', $date_raw );
        if ( ! $date_obj ) return;
        
        $format = $this->get_settings( 'format' );
        $formatted = $date_obj->format( $format );
        
        // Nederlandse maanden
        $nl = array(
            'January' => 'januari', 'February' => 'februari', 'March' => 'maart',
            'April' => 'april', 'May' => 'mei', 'June' => 'juni',
            'July' => 'juli', 'August' => 'augustus', 'September' => 'september',
            'October' => 'oktober', 'November' => 'november', 'December' => 'december',
            'Jan' => 'jan', 'Feb' => 'feb', 'Mar' => 'mrt',
            'Monday' => 'maandag', 'Tuesday' => 'dinsdag', 'Wednesday' => 'woensdag',
            'Thursday' => 'donderdag', 'Friday' => 'vrijdag', 'Saturday' => 'zaterdag', 'Sunday' => 'zondag',
            'Mon' => 'ma', 'Tue' => 'di', 'Wed' => 'wo', 'Thu' => 'do',
            'Fri' => 'vr', 'Sat' => 'za', 'Sun' => 'zo',
        );
        
        echo str_replace( array_keys( $nl ), array_values( $nl ), $formatted );
    }
}

// ═══════════════════════════════════════════════════════
// EVENT TIME TAG
// ═══════════════════════════════════════════════════════

class Hipsy_Event_Time_Tag extends Tag {
    
    public function get_name() {
        return 'hipsy-event-time';
    }
    
    public function get_title() {
        return 'Event Tijd';
    }
    
    public function get_group() {
        return 'hipsy-events';
    }
    
    public function get_categories() {
        return [ TagsModule::TEXT_CATEGORY ];
    }
    
    protected function register_controls() {
        $this->add_control(
            'show_end',
            [
                'label' => 'Toon Eindtijd',
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
    }
    
    public function render() {
        $date_start = get_post_meta( get_the_ID(), 'hipsy_events_date', true );
        if ( empty( $date_start ) ) return;
        
        $start_obj = DateTime::createFromFormat( 'Y-m-d\TH:i', $date_start );
        if ( ! $start_obj ) return;
        
        $output = $start_obj->format( 'H:i' );
        
        if ( $this->get_settings( 'show_end' ) === 'yes' ) {
            $date_end = get_post_meta( get_the_ID(), 'hipsy_events_date_end', true );
            if ( ! empty( $date_end ) ) {
                $end_obj = DateTime::createFromFormat( 'Y-m-d\TH:i', $date_end );
                if ( $end_obj ) {
                    $output .= ' - ' . $end_obj->format( 'H:i' );
                }
            }
        }
        
        echo esc_html( $output );
    }
}

// ═══════════════════════════════════════════════════════
// EVENT LOCATION TAG
// ═══════════════════════════════════════════════════════

class Hipsy_Event_Location_Tag extends Tag {
    
    public function get_name() {
        return 'hipsy-event-location';
    }
    
    public function get_title() {
        return 'Event Locatie';
    }
    
    public function get_group() {
        return 'hipsy-events';
    }
    
    public function get_categories() {
        return [ TagsModule::TEXT_CATEGORY ];
    }
    
    public function render() {
        echo esc_html( get_post_meta( get_the_ID(), 'hipsy_events_location', true ) );
    }
}

// ═══════════════════════════════════════════════════════
// EVENT DESCRIPTION TAG
// ═══════════════════════════════════════════════════════

class Hipsy_Event_Description_Tag extends Tag {
    
    public function get_name() {
        return 'hipsy-event-description';
    }
    
    public function get_title() {
        return 'Event Beschrijving';
    }
    
    public function get_group() {
        return 'hipsy-events';
    }
    
    public function get_categories() {
        return [ TagsModule::TEXT_CATEGORY ];
    }
    
    protected function register_controls() {
        $this->add_control(
            'length',
            [
                'label' => 'Max Woorden',
                'type' => Controls_Manager::NUMBER,
                'default' => 50,
            ]
        );
    }
    
    public function render() {
        $description = get_post_field( 'post_content', get_the_ID() );
        if ( empty( $description ) ) return;
        
        $length = intval( $this->get_settings( 'length' ) );
        
        if ( $length > 0 ) {
            $words = explode( ' ', strip_tags( $description ) );
            if ( count( $words ) > $length ) {
                echo implode( ' ', array_slice( $words, 0, $length ) ) . '...';
                return;
            }
            echo implode( ' ', $words );
        } else {
            echo strip_tags( $description );
        }
    }
}

// ═══════════════════════════════════════════════════════
// EVENT CATEGORIES TAG
// ═══════════════════════════════════════════════════════

class Hipsy_Event_Categories_Tag extends Tag {
    
    public function get_name() {
        return 'hipsy-event-categories';
    }
    
    public function get_title() {
        return 'Event Categorieën';
    }
    
    public function get_group() {
        return 'hipsy-events';
    }
    
    public function get_categories() {
        return [ TagsModule::TEXT_CATEGORY ];
    }
    
    public function render() {
        $terms = get_the_terms( get_the_ID(), 'event_categorie' );
        if ( ! $terms || is_wp_error( $terms ) ) return;
        
        $names = array();
        foreach ( $terms as $term ) {
            $names[] = $term->name;
        }
        
        echo esc_html( implode( ', ', $names ) );
    }
}

// ═══════════════════════════════════════════════════════
// EVENT PRICE TAG
// ═══════════════════════════════════════════════════════

class Hipsy_Event_Price_Tag extends Tag {
    
    public function get_name() {
        return 'hipsy-event-price';
    }
    
    public function get_title() {
        return 'Event Prijs';
    }
    
    public function get_group() {
        return 'hipsy-events';
    }
    
    public function get_categories() {
        return [ TagsModule::TEXT_CATEGORY ];
    }
    
    public function render() {
        $ticket_info = get_post_meta( get_the_ID(), 'hipsy_ticket_info', true );
        if ( empty( $ticket_info ) ) return;
        
        $tickets = maybe_unserialize( $ticket_info );
        if ( ! is_array( $tickets ) || empty( $tickets ) ) return;
        
        $lowest = null;
        foreach ( $tickets as $ticket ) {
            $price = isset( $ticket['price'] ) ? floatval( $ticket['price'] ) : 0;
            if ( $lowest === null || $price < $lowest ) {
                $lowest = $price;
            }
        }
        
        if ( $lowest === 0 ) {
            echo 'Gratis';
        } else {
            echo 'Vanaf €' . number_format( $lowest, 2, ',', '.' );
        }
    }
}

// ═══════════════════════════════════════════════════════
// EVENT URL TAG (for buttons)
// ═══════════════════════════════════════════════════════

class Hipsy_Event_URL_Tag extends Tag {
    
    public function get_name() {
        return 'hipsy-event-url';
    }
    
    public function get_title() {
        return 'Event Ticket URL';
    }
    
    public function get_group() {
        return 'hipsy-events';
    }
    
    public function get_categories() {
        return [ TagsModule::URL_CATEGORY ];
    }
    
    public function render() {
        echo esc_url( get_post_meta( get_the_ID(), 'hipsy_events_link', true ) );
    }
}

// ═══════════════════════════════════════════════════════
// EVENT IMAGE TAG
// ═══════════════════════════════════════════════════════

class Hipsy_Event_Image_Tag extends Tag {
    
    public function get_name() {
        return 'hipsy-event-image';
    }
    
    public function get_title() {
        return 'Event Afbeelding URL';
    }
    
    public function get_group() {
        return 'hipsy-events';
    }
    
    public function get_categories() {
        return [ TagsModule::IMAGE_CATEGORY, TagsModule::URL_CATEGORY ];
    }
    
    protected function register_controls() {
        $this->add_control(
            'size',
            [
                'label' => 'Afbeelding Grootte',
                'type' => Controls_Manager::SELECT,
                'default' => 'large',
                'options' => [
                    'thumbnail' => 'Thumbnail',
                    'medium' => 'Medium',
                    'large' => 'Large',
                    'full' => 'Full',
                ],
            ]
        );
    }
    
    public function render() {
        $size = $this->get_settings( 'size' );
        $image_data = [
            'id' => get_post_thumbnail_id(),
            'url' => get_the_post_thumbnail_url( get_the_ID(), $size ),
        ];
        
        echo wp_json_encode( $image_data );
    }
}
