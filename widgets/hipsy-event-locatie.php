<?php
class Hipsy_Event_Locatie_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'hipsy_event_locatie'; }
    public function get_title()      { return 'Hipsy · Locatie'; }
    public function get_icon()       { return 'eicon-map-pin'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords()   { return [ 'hipsy', 'event', 'locatie', 'adres', 'kaart', 'maps' ]; }

    protected function register_controls() {

        // ── INHOUD ──────────────────────────────────────────────────
        $this->start_controls_section( 'section_content', [
            'label' => 'Inhoud',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        hipsy_register_data_source_controls( $this );

        $this->add_control( 'toon_icoon', [
            'label'        => 'Pin-icoon tonen',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Ja', 'label_off' => 'Nee',
            'return_value' => 'yes', 'default' => 'yes',
        ]);

        $this->add_control( 'toon_kaart_link', [
            'label'        => '"Bekijk op kaart" tonen',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Ja', 'label_off' => 'Nee',
            'return_value' => 'yes', 'default' => 'yes',
            'description'  => 'Toont een uitvouwbare Google Maps embed onder het adres.',
        ]);

        $this->add_control( 'kaart_link_tekst', [
            'label'     => 'Tekst "Bekijk op kaart"',
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => 'Bekijk op kaart',
            'condition' => [ 'toon_kaart_link' => 'yes' ],
        ]);

        $this->add_control( 'kaart_hoogte', [
            'label'      => 'Kaarthoogte (px)',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 150, 'max' => 600 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 300 ],
            'condition'  => [ 'toon_kaart_link' => 'yes' ],
        ]);

        $this->add_control( 'kaart_radius', [
            'label'      => 'Afgeronde hoeken kaart (px)',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 24 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 8 ],
            'condition'  => [ 'toon_kaart_link' => 'yes' ],
        ]);

        $this->add_responsive_control( 'uitlijning', [
            'label'     => 'Uitlijning',
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => 'Links',  'icon' => 'eicon-text-align-left' ],
                'center'     => [ 'title' => 'Midden', 'icon' => 'eicon-text-align-center' ],
                'flex-end'   => [ 'title' => 'Rechts', 'icon' => 'eicon-text-align-right' ],
            ],
            'selectors' => [ '{{WRAPPER}} .hloc-adres' => 'justify-content: {{VALUE}};' ],
        ]);

        $this->end_controls_section();

        // ── STIJL ADRES ─────────────────────────────────────────────
        $this->start_controls_section( 'section_stijl', [
            'label' => 'Adres',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'typografie', 'selector' => '{{WRAPPER}} .hloc-tekst' ]
        );
        $this->add_control( 'kleur', [
            'label'     => 'Tekstkleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#374151',
            'selectors' => [ '{{WRAPPER}} .hloc-tekst' => 'color: {{VALUE}} !important;' ],
        ]);
        $this->add_control( 'icoon_kleur', [
            'label'     => 'Icoonkleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#6b7280',
            'selectors' => [ '{{WRAPPER}} .hloc-pin' => 'stroke: {{VALUE}} !important;' ],
            'condition' => [ 'toon_icoon' => 'yes' ],
        ]);
        $this->add_control( 'icoon_grootte', [
            'label'      => 'Icoongrootte (px)',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 40 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 16 ],
            'selectors'  => [ '{{WRAPPER}} .hloc-pin' => 'width: {{SIZE}}px !important; height: {{SIZE}}px !important;' ],
            'condition'  => [ 'toon_icoon' => 'yes' ],
        ]);
        $this->add_responsive_control( 'marge', [
            'label'      => 'Marge',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', 'rem' ],
            'selectors'  => [ '{{WRAPPER}} .hloc-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->end_controls_section();

        // ── STIJL KAART LINK ────────────────────────────────────────
        $this->start_controls_section( 'section_stijl_link', [
            'label'     => '"Bekijk op kaart"-link',
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [ 'toon_kaart_link' => 'yes' ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'link_typografie', 'selector' => '{{WRAPPER}} .hloc-kaart-toggle' ]
        );
        $this->add_control( 'link_kleur', [
            'label'     => 'Kleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#059669',
            'selectors' => [ '{{WRAPPER}} .hloc-kaart-toggle' => 'color: {{VALUE}} !important;' ],
        ]);
        $this->add_control( 'link_kleur_hover', [
            'label'     => 'Kleur (hover)',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#047857',
            'selectors' => [ '{{WRAPPER}} .hloc-kaart-toggle:hover' => 'color: {{VALUE}} !important;' ],
        ]);
        $this->add_responsive_control( 'link_marge_top', [
            'label'      => 'Ruimte boven link',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'unit' => 'px', 'size' => 6 ],
            'selectors'  => [ '{{WRAPPER}} .hloc-kaart-toggle' => 'margin-top: {{SIZE}}px;' ],
        ]);

        $this->end_controls_section();
    }

    // ── RENDER ────────────────────────────────────────────────────────
    protected function render() {
        $s    = $this->get_settings_for_display();
        $data = hipsy_get_event_data( hipsy_resolve_event_id( $s ) );

        if ( ! $data ) { echo '<p><em>Geen event gevonden.</em></p>'; return; }
        if ( ! $data['locatie'] ) return;

        $locatie     = $data['locatie'];
        $toon_kaart  = ( $s['toon_kaart_link'] ?? '' ) === 'yes';
        $hoogte      = (int)( $s['kaart_hoogte']['size'] ?? 300 );
        $radius      = (int)( $s['kaart_radius']['size'] ?? 8 );
        $link_tekst  = esc_html( $s['kaart_link_tekst'] ?? 'Bekijk op kaart' );
        $uid         = 'hloc-' . $this->get_id() . '-' . wp_rand(100,999);
        $maps_embed  = 'https://www.google.com/maps?q=' . urlencode($locatie) . '&output=embed';

        // Pin icoon
        $icoon = '';
        if ( ( $s['toon_icoon'] ?? '' ) === 'yes' ) {
            $icoon = '<svg class="hloc-pin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;width:16px;height:16px;vertical-align:middle"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';
        }

        echo '<div class="hloc-wrapper">';

        // Adresregel
        echo '<div class="hloc-adres" style="display:flex;align-items:flex-start;gap:6px;">';
        echo $icoon;
        echo '<span class="hloc-tekst">' . esc_html( $locatie ) . '</span>';
        echo '</div>';

        // "Bekijk op kaart"-toggle
        if ( $toon_kaart ) {
            // Chevron SVG (wisselt van richting via JS class)
            $chevron_neer  = '<svg class="hloc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px;vertical-align:middle;transition:transform .25s"><polyline points="6 9 12 15 18 9"/></svg>';

            echo '<button class="hloc-kaart-toggle" '
                . 'aria-expanded="false" '
                . 'aria-controls="' . esc_attr($uid) . '" '
                . 'onclick="hlocToggle(this)" '
                . 'style="background:none;border:none;cursor:pointer;padding:0;display:inline-flex;align-items:center;gap:4px;font-family:inherit;">'
                . $link_tekst . ' ' . $chevron_neer
                . '</button>';

            // Kaartcontainer (verborgen bij start)
            echo '<div id="' . esc_attr($uid) . '" '
                . 'class="hloc-kaart-container" '
                . 'style="display:none;margin-top:10px;overflow:hidden;border-radius:' . $radius . 'px;">'
                . '<iframe '
                . 'src="' . esc_url($maps_embed) . '" '
                . 'width="100%" '
                . 'height="' . $hoogte . '" '
                . 'style="border:0;display:block;" '
                . 'allowfullscreen="" '
                . 'loading="lazy" '
                . 'referrerpolicy="no-referrer-when-downgrade">'
                . '</iframe>'
                . '</div>';
        }

        echo '</div>'; // hloc-wrapper

        // Inline JS — klein en eenmalig
        static $js_printed = false;
        if ( ! $js_printed ) {
            $js_printed = true;
            echo '<script>
            function hlocToggle(btn) {
                var id  = btn.getAttribute("aria-controls");
                var box = document.getElementById(id);
                var open = btn.getAttribute("aria-expanded") === "true";
                var chev = btn.querySelector(".hloc-chevron");

                if (open) {
                    box.style.display = "none";
                    btn.setAttribute("aria-expanded", "false");
                    if (chev) chev.style.transform = "rotate(0deg)";
                } else {
                    box.style.display = "block";
                    btn.setAttribute("aria-expanded", "true");
                    if (chev) chev.style.transform = "rotate(180deg)";
                }
            }
            </script>';
        }
    }
}
