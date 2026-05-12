<?php
/**
 * Hipsy Elementor Widget — Gedeelde hulpfuncties
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'hipsy_parse_datetime' ) ) {
    function hipsy_parse_datetime( $raw ) {
        if ( ! $raw ) return null;
        $raw = trim( $raw );
        $formaten = [ 'Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i', 'Y-m-d', 'Y-m-d\TH:i:s' ];
        foreach ( $formaten as $fmt ) {
            $dt = DateTime::createFromFormat( $fmt, $raw );
            if ( $dt !== false ) return $dt;
        }
        try { return new DateTime( $raw ); } catch ( Exception $e ) { return null; }
    }
}

if ( ! function_exists( 'hipsy_format_datum' ) ) {
    function hipsy_format_datum( $raw, $formaat = 'volledig' ) {
        if ( ! $raw ) return '';
        $dt = hipsy_parse_datetime( $raw );
        if ( ! $dt ) return '';
        $dagen_lang = [ 'Monday'=>'Maandag','Tuesday'=>'Dinsdag','Wednesday'=>'Woensdag','Thursday'=>'Donderdag','Friday'=>'Vrijdag','Saturday'=>'Zaterdag','Sunday'=>'Zondag' ];
        $dagen_kort = [ 'Monday'=>'ma','Tuesday'=>'di','Wednesday'=>'wo','Thursday'=>'do','Friday'=>'vr','Saturday'=>'za','Sunday'=>'zo' ];
        $maanden_lang = [ 1=>'januari',2=>'februari',3=>'maart',4=>'april',5=>'mei',6=>'juni',7=>'juli',8=>'augustus',9=>'september',10=>'oktober',11=>'november',12=>'december' ];
        $maanden_kort = [ 1=>'jan',2=>'feb',3=>'mrt',4=>'apr',5=>'mei',6=>'jun',7=>'jul',8=>'aug',9=>'sep',10=>'okt',11=>'nov',12=>'dec' ];
        $dag_eng = $dt->format('l'); $dag_nr = (int) $dt->format('j'); $maand = (int) $dt->format('n'); $jaar = $dt->format('Y');
        switch ( $formaat ) {
            case 'volledig': return ( $dagen_lang[$dag_eng] ?? $dag_eng ) . ' ' . $dag_nr . ' ' . ( $maanden_lang[$maand] ?? $maand ) . ' ' . $jaar;
            case 'kort': return ( $dagen_kort[$dag_eng] ?? '' ) . ' ' . $dag_nr . ' ' . ( $maanden_kort[$maand] ?? $maand ) . ' ' . $jaar;
            case 'zonder_dag': return $dag_nr . ' ' . ( $maanden_lang[$maand] ?? $maand ) . ' ' . $jaar;
            case 'numeriek': return $dt->format('d-m-Y');
            default: return ( $dagen_lang[$dag_eng] ?? '' ) . ' ' . $dag_nr . ' ' . ( $maanden_lang[$maand] ?? $maand ) . ' ' . $jaar;
        }
    }
}

if ( ! function_exists( 'hipsy_format_tijd' ) ) {
    function hipsy_format_tijd( $start_raw, $einde_raw = '' ) {
        if ( ! $start_raw ) return '';
        $dt = hipsy_parse_datetime( $start_raw );
        if ( ! $dt ) return '';
        $tijd = $dt->format('H:i');
        if ( $einde_raw ) {
            $dt_einde = hipsy_parse_datetime( $einde_raw );
            if ( $dt_einde ) $tijd .= ' - ' . $dt_einde->format('H:i');
        }
        return $tijd;
    }
}

if ( ! function_exists( 'hipsy_get_event_opties' ) ) {
    function hipsy_get_event_opties() {
        $opties = [ '' => '— Kies een event —' ];
        $events = get_posts([ 'post_type'=>'events', 'posts_per_page'=>200, 'post_status'=>'publish', 'orderby'=>'meta_value', 'meta_key'=>'hipsy_events_date', 'order'=>'ASC' ]);
        foreach ( $events as $e ) {
            $d = get_post_meta( $e->ID, 'hipsy_events_date', true );
            $label = $e->post_title . ( $d ? ' (' . hipsy_format_datum( $d, 'kort' ) . ')' : '' );
            $opties[ $e->ID ] = $label;
        }
        return $opties;
    }
}

if ( ! function_exists( 'hipsy_resolve_event_id' ) ) {
    function hipsy_resolve_event_id( $settings ) {
        $bron = $settings['data_bron'] ?? 'dynamisch';
        if ( $bron === 'specifiek' && ! empty( $settings['event_id'] ) ) return (int) $settings['event_id'];
        return get_the_ID();
    }
}

if ( ! function_exists( 'hipsy_get_event_data' ) ) {
    function hipsy_get_event_data( $event_id = 0 ) {
        if ( ! $event_id ) $event_id = get_the_ID();
        $event_id = (int) $event_id;
        if ( ! $event_id ) return null;
        $post = get_post( $event_id );
        if ( ! $post || $post->post_type !== 'events' ) return null;
        return [
            'id' => $event_id,
            'titel' => get_the_title( $event_id ),
            'beschrijving' => $post->post_content,
            'datum' => get_post_meta( $event_id, 'hipsy_events_date', true ),
            'datum_einde' => get_post_meta( $event_id, 'hipsy_events_date_end', true ),
            'locatie' => get_post_meta( $event_id, 'hipsy_events_location', true ),
            'link' => get_post_meta( $event_id, 'hipsy_events_link', true ),
            'permalink' => get_permalink( $event_id ),
        ];
    }
}

if ( ! function_exists( 'hipsy_register_data_source_controls' ) ) {
    function hipsy_register_data_source_controls( $widget ) {
        $widget->add_control( 'data_bron', [
            'label' => 'Databron',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [ 'dynamisch' => '🔄 Huidig event (dynamisch template)', 'specifiek' => '📌 Specifiek event kiezen' ],
            'default' => 'dynamisch',
            'description' => 'Gebruik "Huidig event" als je dit widget in een Elementor Theme Builder template plaatst. Gebruik "Specifiek" om één vast event te tonen.',
        ] );
        $widget->add_control( 'event_id', [
            'label' => 'Kies event',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => hipsy_get_event_opties(),
            'default' => '',
            'condition' => [ 'data_bron' => 'specifiek' ],
        ] );
    }
}

if ( ! function_exists( 'hipsy_register_datum_formaat_control' ) ) {
    function hipsy_register_datum_formaat_control( $widget ) {
        $widget->add_control( 'datum_formaat', [
            'label' => 'Datumformaat',
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [ 'volledig'=>'Maandag 27 april 2026', 'kort'=>'ma 27 apr 2026', 'zonder_dag'=>'27 april 2026', 'numeriek'=>'27-04-2026' ],
            'default' => 'volledig',
        ] );
    }
}

if ( ! function_exists( 'hipsy_ew_styles' ) ) {
    function hipsy_ew_styles() {
        static $printed = false;
        if ( $printed ) return;
        $printed = true;
        echo '<style>
        .hipsy-grid{display:grid;gap:1.5rem}.hipsy-grid--cols{grid-template-columns:repeat(var(--cols-d,3),1fr)}@media(max-width:1024px){.hipsy-grid--cols{grid-template-columns:repeat(var(--cols-t,2),1fr)}}@media(max-width:767px){.hipsy-grid--cols{grid-template-columns:repeat(var(--cols-m,1),1fr)}}.hipsy-card{border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;background:#fff;display:flex;flex-direction:column;height:100%;transition:box-shadow .2s}.hipsy-card:hover{box-shadow:0 4px 20px rgba(0,0,0,.10)}.hipsy-card.is-horizontal{flex-direction:row}.hipsy-card.is-horizontal .hipsy-card-img{width:140px;flex-shrink:0}.hipsy-card.is-horizontal .hipsy-card-img img{width:100%;height:100%!important;object-fit:cover}.hipsy-card-img img{width:100%;display:block;object-fit:cover}.hipsy-card-body{padding:1rem 1.1rem 1.2rem;display:flex;flex-direction:column;flex:1}.hipsy-card-datum{font-size:.72rem;font-weight:700;letter-spacing:.05em;display:flex;align-items:center;gap:4px;margin-bottom:.15rem}.hipsy-card-tijd{font-size:.8rem;color:#6b7280;display:flex;align-items:center;gap:4px;margin-bottom:.4rem}.hipsy-card-titel{font-size:1rem;font-weight:700;margin:0 0 .5rem;line-height:1.3}.hipsy-card-locatie{font-size:.82rem;color:#6b7280;display:flex;align-items:center;gap:4px;margin-bottom:.4rem}.hipsy-card-desc{font-size:.85rem;color:#4b5563;line-height:1.5;margin-bottom:.5rem}.hipsy-card-prijs{font-size:.82rem;font-weight:600;color:#059669;margin-bottom:.5rem}.hipsy-card-actions{display:flex;gap:.5rem;flex-wrap:wrap;margin-top:auto;padding-top:.75rem}.hipsy-card-btn{display:inline-block;padding:.4rem .85rem;border-radius:5px;font-size:.82rem;font-weight:600;text-decoration:none;transition:opacity .15s,background-color .15s,color .15s}.hipsy-card-btn:hover{opacity:.85}.hipsy-card-btn--info{background:#f3f4f6;color:#374151}.hipsy-card-btn--ticket{background:#7c3aed;color:#fff}.hipsy-meer-kop{margin-bottom:1rem}img.emoji,.hew-card img.emoji,.hew-datum img.emoji,.hew-titel img.emoji,.hew-desc img.emoji,.hew-locatie img.emoji,.hew-prijs img.emoji,.hew-tijd img.emoji,.hew-card-body img.emoji,.hipsy-card img.emoji,.hipsy-event-beschrijving img.emoji,.hipsy-ticket-naam img.emoji,.hipsy-event-titel img.emoji{height:1em!important;width:1em!important;max-width:1em!important;min-width:unset!important;max-height:1em!important;vertical-align:-0.1em!important;display:inline!important;margin:0 0.05em!important;padding:0!important;box-shadow:none!important;border:none!important;background:none!important;border-radius:0!important;float:none!important;}
        </style>';
    }
}

if ( ! function_exists( 'hipsy_get_tickets' ) ) {
    function hipsy_get_tickets( $event_id ) {
        $raw = get_post_meta( (int) $event_id, 'hipsy_ticket_info', true );
        $tickets = maybe_unserialize( $raw );
        if ( ! is_array( $tickets ) ) return [];
        return $tickets;
    }
}

if ( ! function_exists( 'hipsy_format_prijs' ) ) {
    function hipsy_format_prijs( $price, $gratis_label = 'Gratis' ) {
        $price = (float) $price;
        if ( $price <= 0 ) return $gratis_label;
        return '€ ' . number_format( $price, 2, ',', '.' );
    }
}

if ( ! function_exists( 'hipsy_get_unique_locations' ) ) {
    function hipsy_get_unique_locations() {
        global $wpdb;
        $results = $wpdb->get_col( "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'hipsy_events_location' AND meta_value != '' ORDER BY meta_value ASC" );
        return $results ?: [];
    }
}
