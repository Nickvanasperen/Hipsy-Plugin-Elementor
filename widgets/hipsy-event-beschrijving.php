<?php
class Hipsy_Event_Beschrijving_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'hipsy_event_beschrijving'; }
    public function get_title()      { return 'Hipsy · Beschrijving'; }
    public function get_icon()       { return 'eicon-text'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords()   { return [ 'hipsy', 'event', 'beschrijving', 'tekst' ]; }

    protected function register_controls() {

        $this->start_controls_section( 'section_content', [
            'label' => 'Inhoud',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        hipsy_register_data_source_controls( $this );
        $this->add_control( 'max_woorden', [
            'label'       => 'Max. woorden (0 = alles)',
            'type'        => \Elementor\Controls_Manager::NUMBER,
            'default'     => 0, 'min' => 0,
        ]);
        $this->add_control( 'lees_meer_tekst', [
            'label'       => '"Lees meer"-linktekst',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => '',
            'description' => 'Laat leeg voor geen link.',
        ]);
        $this->end_controls_section();

        // ── STIJL TEKST ─────────────────────────────────────────────
        $this->start_controls_section( 'section_stijl_tekst', [
            'label' => 'Tekst',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'typografie', 'selector' => '{{WRAPPER}} .hipsy-event-beschrijving, {{WRAPPER}} .hipsy-event-beschrijving p' ]
        );
        $this->add_control( 'kleur', [
            'label'     => 'Tekstkleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .hipsy-event-beschrijving, {{WRAPPER}} .hipsy-event-beschrijving p' => 'color: {{VALUE}};' ],
        ]);
        $this->add_responsive_control( 'regelafstand', [
            'label'      => 'Regelafstand',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'em' ],
            'range'      => [ 'em' => [ 'min' => 1, 'max' => 3, 'step' => 0.1 ] ],
            'default'    => [ 'unit' => 'em', 'size' => 1.7 ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-event-beschrijving p' => 'line-height: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->add_responsive_control( 'alinea_afstand', [
            'label'      => 'Ruimte tussen alinea\'s',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px','em' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 16 ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-event-beschrijving p' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->add_responsive_control( 'uitlijning', [
            'label'     => 'Uitlijning',
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'left'    => [ 'title'=>'Links',   'icon'=>'eicon-text-align-left' ],
                'center'  => [ 'title'=>'Midden',  'icon'=>'eicon-text-align-center' ],
                'right'   => [ 'title'=>'Rechts',  'icon'=>'eicon-text-align-right' ],
                'justify' => [ 'title'=>'Uitvullen','icon'=>'eicon-text-align-justify' ],
            ],
            'selectors' => [ '{{WRAPPER}} .hipsy-event-beschrijving' => 'text-align: {{VALUE}};' ],
        ]);
        $this->end_controls_section();

        // ── STIJL LEES MEER ─────────────────────────────────────────
        $this->start_controls_section( 'section_stijl_link', [
            'label'     => 'Lees meer-link',
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [ 'lees_meer_tekst!' => '' ],
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'link_typografie', 'selector' => '{{WRAPPER}} .hipsy-lees-meer' ]
        );
        $this->add_control( 'link_kleur', [
            'label'     => 'Kleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .hipsy-lees-meer' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'link_kleur_hover', [
            'label'     => 'Kleur (hover)',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .hipsy-lees-meer:hover' => 'color: {{VALUE}};' ],
        ]);
        $this->add_responsive_control( 'link_marge', [
            'label'      => 'Marge boven link',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px','em' ],
            'default'    => [ 'unit' => 'px', 'size' => 12 ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-lees-meer' => 'margin-top: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $s    = $this->get_settings_for_display();
        $data = hipsy_get_event_data( hipsy_resolve_event_id( $s ) );
        if ( ! $data ) { echo '<p><em>Geen event gevonden.</em></p>'; return; }
        if ( ! $data['beschrijving'] ) return;

        $tekst = $data['beschrijving'];

        if ( (int) $s['max_woorden'] > 0 ) {
            // Bij inkorten: wpautop zodat witregels zichtbaar blijven
            $tekst_kort = wp_trim_words( $tekst, (int) $s['max_woorden'], '' );
            echo '<div class="hipsy-event-beschrijving">' . wp_kses_post( wpautop( $tekst_kort ) ) . '</div>';
        } else {
            // Volledig: gebruik WordPress' eigen content-filters
            // Dit verwerkt enters → <p>, emojis, shortcodes en alle andere filters correct
            $tekst_html = apply_filters( 'the_content', $tekst );
            echo '<div class="hipsy-event-beschrijving">' . $tekst_html . '</div>';
        }

        if ( ! empty( $s['lees_meer_tekst'] ) && $data['permalink'] ) {
            echo '<a href="' . esc_url( $data['permalink'] ) . '" class="hipsy-lees-meer" style="display:inline-block;text-decoration:none;">'
                . esc_html( $s['lees_meer_tekst'] ) . '</a>';
        }

        // WordPress converteert emoji naar <img class="emoji"> — zonder deze CSS worden ze mega groot
        echo '<style>
        .hipsy-event-beschrijving img.emoji {
            height: 1em !important;
            width: 1em !important;
            max-width: 1em !important;
            vertical-align: -0.1em;
            display: inline !important;
            margin: 0 0.05em !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }
        </style>';
    }
}
