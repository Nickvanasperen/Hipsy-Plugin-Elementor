<?php
/**
 * Widget: Hipsy Event Tickets
 *
 * Toont alle tickets van een Hipsy event met naam, prijs en optioneel beschrijving.
 * Ticketdata zit in meta-veld 'hipsy_ticket_info' als geserialiseerde array.
 * Elk ticket heeft: name, price, description  (zie singleEventRenderer.php)
 */
class Hipsy_Event_Tickets_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'hipsy_event_tickets'; }
    public function get_title()      { return 'Hipsy · Tickets'; }
    public function get_icon()       { return 'eicon-price-list'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords()   { return [ 'hipsy', 'tickets', 'prijs', 'event' ]; }

    protected function register_controls() {

        // ── INHOUD ───────────────────────────────────────────────────
        $this->start_controls_section( 'section_content', [
            'label' => 'Inhoud',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        hipsy_register_data_source_controls( $this );

        $this->add_control( 'toon_beschrijving', [
            'label'        => 'Ticketbeschrijving tonen',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Ja', 'label_off' => 'Nee',
            'return_value' => 'yes', 'default' => 'yes',
        ]);

        $this->add_control( 'toon_servicecosts', [
            'label'        => '"incl. servicekosten" tonen',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Ja', 'label_off' => 'Nee',
            'return_value' => 'yes', 'default' => 'yes',
        ]);

        $this->add_control( 'gratis_label', [
            'label'   => 'Label voor gratis ticket',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Gratis',
        ]);

        $this->add_control( 'toon_koopknop', [
            'label'        => 'Koopknop tonen',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Ja', 'label_off' => 'Nee',
            'return_value' => 'yes', 'default' => 'yes',
        ]);

        $this->add_control( 'koopknop_tekst', [
            'label'     => 'Tekst koopknop',
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => 'Bestel tickets',
            'condition' => [ 'toon_koopknop' => 'yes' ],
        ]);

        $this->end_controls_section();

        // ── STIJL LIJST ──────────────────────────────────────────────
        $this->start_controls_section( 'section_stijl_lijst', [
            'label' => 'Ticketlijst',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'lijst_achtergrond', [
            'label'     => 'Achtergrondkleur lijst',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .hipsy-tickets-lijst' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [ 'name' => 'lijst_rand', 'selector' => '{{WRAPPER}} .hipsy-tickets-lijst',
              'fields_options' => [ 'border' => ['default'=>'solid'], 'width' => ['default'=>['top'=>'1','right'=>'1','bottom'=>'1','left'=>'1','unit'=>'px']], 'color' => ['default'=>'#e5e7eb'] ] ]
        );

        $this->add_responsive_control( 'lijst_radius', [
            'label'      => 'Afgeronde hoeken',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px','%' ],
            'default'    => [ 'top'=>'10','right'=>'10','bottom'=>'10','left'=>'10','unit'=>'px' ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-tickets-lijst' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [ 'name' => 'lijst_schaduw', 'selector' => '{{WRAPPER}} .hipsy-tickets-lijst' ]
        );

        $this->add_responsive_control( 'lijst_padding', [
            'label'      => 'Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px','em' ],
            'default'    => [ 'top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','unit'=>'px' ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-tickets-lijst' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->end_controls_section();

        // ── STIJL TICKETRIJ ──────────────────────────────────────────
        $this->start_controls_section( 'section_stijl_rij', [
            'label' => 'Ticketrij',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control( 'rij_achtergrond', [
            'label'     => 'Achtergrondkleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticket-rij' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'rij_achtergrond_hover', [
            'label'     => 'Achtergrondkleur (hover)',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#f9fafb',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticket-rij:hover' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'scheidingslijn_kleur', [
            'label'     => 'Scheidingslijn kleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#e5e7eb',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticket-rij + .hipsy-ticket-rij' => 'border-top: 1px solid {{VALUE}};' ],
        ]);

        $this->add_responsive_control( 'rij_padding', [
            'label'      => 'Padding per rij',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px','em' ],
            'default'    => [ 'top'=>'16','right'=>'20','bottom'=>'16','left'=>'20','unit'=>'px' ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-ticket-rij' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->end_controls_section();

        // ── STIJL NAAM ───────────────────────────────────────────────
        $this->start_controls_section( 'section_stijl_naam', [
            'label' => 'Ticketnaam',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'naam_typografie', 'selector' => '{{WRAPPER}} .hipsy-ticket-naam' ]
        );

        $this->add_control( 'naam_kleur', [
            'label'     => 'Kleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#111827',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticket-naam' => 'color: {{VALUE}};' ],
        ]);

        $this->end_controls_section();

        // ── STIJL OMSCHRIJVING ───────────────────────────────────────
        $this->start_controls_section( 'section_stijl_omschrijving', [
            'label'     => 'Ticketomschrijving',
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [ 'toon_beschrijving' => 'yes' ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'omschrijving_typografie', 'selector' => '{{WRAPPER}} .hipsy-ticket-omschrijving' ]
        );

        $this->add_control( 'omschrijving_kleur', [
            'label'     => 'Kleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#6b7280',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticket-omschrijving' => 'color: {{VALUE}};' ],
        ]);

        $this->add_responsive_control( 'omschrijving_marge', [
            'label'      => 'Ruimte boven omschrijving',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'unit'=>'px','size'=>4 ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-ticket-omschrijving' => 'margin-top: {{SIZE}}px;' ],
        ]);

        $this->end_controls_section();

        // ── STIJL PRIJS ──────────────────────────────────────────────
        $this->start_controls_section( 'section_stijl_prijs', [
            'label' => 'Prijs',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'prijs_typografie', 'selector' => '{{WRAPPER}} .hipsy-ticket-prijs' ]
        );

        $this->add_control( 'prijs_kleur', [
            'label'     => 'Prijskleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#111827',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticket-prijs' => 'color: {{VALUE}};' ],
        ]);

        $this->add_control( 'prijs_gratis_kleur', [
            'label'     => 'Kleur gratis-label',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#059669',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticket-prijs.is-gratis' => 'color: {{VALUE}};' ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'servicecosts_typografie', 'selector' => '{{WRAPPER}} .hipsy-ticket-servicecosts',
              'condition' => [ 'toon_servicecosts' => 'yes' ] ]
        );

        $this->add_control( 'servicecosts_kleur', [
            'label'     => 'Kleur "incl. servicekosten"',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#9ca3af',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticket-servicecosts' => 'color: {{VALUE}};' ],
            'condition' => [ 'toon_servicecosts' => 'yes' ],
        ]);

        $this->end_controls_section();

        // ── STIJL KOOPKNOP ───────────────────────────────────────────
        $this->start_controls_section( 'section_stijl_knop', [
            'label'     => 'Koopknop',
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [ 'toon_koopknop' => 'yes' ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'knop_typografie', 'selector' => '{{WRAPPER}} .hipsy-ticket-koopknop' ]
        );

        $this->add_control( 'knop_bg', [
            'label'     => 'Achtergrondkleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#111827',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticket-koopknop' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'knop_bg_hover', [
            'label'     => 'Achtergrondkleur (hover)',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#374151',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticket-koopknop:hover' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'knop_tekstkleur', [
            'label'     => 'Tekstkleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticket-koopknop' => 'color: {{VALUE}};' ],
        ]);

        $this->add_responsive_control( 'knop_radius', [
            'label'      => 'Afgeronde hoeken',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min'=>0,'max'=>50 ] ],
            'default'    => [ 'unit'=>'px','size'=>6 ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-ticket-koopknop' => 'border-radius: {{SIZE}}px;' ],
        ]);

        $this->add_responsive_control( 'knop_padding', [
            'label'      => 'Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px','em' ],
            'default'    => [ 'top'=>'12','right'=>'24','bottom'=>'12','left'=>'24','unit'=>'px' ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-ticket-koopknop' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->add_responsive_control( 'knop_breedte', [
            'label'     => 'Breedte',
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => [ 'auto'=>'Automatisch', '100%'=>'Volledig (100%)' ],
            'default'   => 'auto',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticket-koopknop' => 'width: {{VALUE}}; text-align: center;' ],
        ]);

        $this->add_responsive_control( 'knop_marge', [
            'label'      => 'Ruimte boven knop',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'default'    => [ 'unit'=>'px','size'=>20 ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-ticket-koopknop-wrapper' => 'margin-top: {{SIZE}}px;' ],
        ]);

        $this->end_controls_section();
    }

    // ── RENDER ────────────────────────────────────────────────────────
    protected function render() {
        $s       = $this->get_settings_for_display();
        $data    = hipsy_get_event_data( hipsy_resolve_event_id( $s ) );

        if ( ! $data ) {
            echo '<p><em>Geen event gevonden. Controleer de databron of kies een specifiek event.</em></p>';
            return;
        }

        $tickets = hipsy_get_tickets( $data['id'] );

        if ( empty( $tickets ) ) {
            echo '<p><em>Geen tickets gevonden voor dit event.</em></p>';
            return;
        }

        echo '<div class="hipsy-tickets-lijst">';

        foreach ( $tickets as $ticket ) {
            $naam        = isset( $ticket['name'] )        ? $ticket['name']        : '';
            $prijs_raw   = isset( $ticket['price'] )       ? $ticket['price']       : 0;
            $omschrijving = isset( $ticket['description'] ) ? $ticket['description'] : '';

            $prijs_str   = hipsy_format_prijs( $prijs_raw, esc_html( $s['gratis_label'] ) );
            $is_gratis   = (float) $prijs_raw <= 0;
            $prijs_class = 'hipsy-ticket-prijs' . ( $is_gratis ? ' is-gratis' : '' );

            echo '<div class="hipsy-ticket-rij">';
            echo '  <div class="hipsy-ticket-links">';
            if ( $naam ) {
                echo '    <div class="hipsy-ticket-naam">' . esc_html( $naam ) . '</div>';
            }
            if ( $s['toon_beschrijving'] === 'yes' && $omschrijving ) {
                echo '    <div class="hipsy-ticket-omschrijving">' . esc_html( $omschrijving ) . '</div>';
            }
            echo '  </div>';

            echo '  <div class="hipsy-ticket-rechts">';
            echo '    <div class="' . esc_attr( $prijs_class ) . '">' . esc_html( $prijs_str ) . '</div>';
            if ( $s['toon_servicecosts'] === 'yes' && ! $is_gratis ) {
                echo '    <div class="hipsy-ticket-servicecosts">incl. servicekosten</div>';
            }
            echo '  </div>';
            echo '</div>';
        }

        echo '</div>';

        // Koopknop
        if ( $s['toon_koopknop'] === 'yes' && ! empty( $data['link'] ) ) {
            echo '<div class="hipsy-ticket-koopknop-wrapper">';
            echo '<a href="' . esc_url( $data['link'] ) . '" class="hipsy-ticket-koopknop" target="_blank" rel="noopener">'
                . esc_html( $s['koopknop_tekst'] ) . '</a>';
            echo '</div>';
        }

        // Basis CSS (overschrijfbaar via stijlcontroles)
        echo '<style>
        .hipsy-tickets-lijst {
            overflow: hidden;
        }
        .hipsy-ticket-rij {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            transition: background-color .15s;
        }
        .hipsy-ticket-links {
            flex: 1;
            min-width: 0;
        }
        .hipsy-ticket-rechts {
            text-align: right;
            flex-shrink: 0;
        }
        .hipsy-ticket-naam {
            font-weight: 600;
        }
        .hipsy-ticket-omschrijving {
            font-size: .85em;
        }
        .hipsy-ticket-prijs {
            font-weight: 700;
        }
        .hipsy-ticket-servicecosts {
            font-size: .75em;
        }
        .hipsy-ticket-koopknop {
            display: inline-block;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: background-color .15s, opacity .15s;
        }
        .hipsy-ticket-koopknop:hover {
            opacity: .9;
        }
        </style>';
    }
}
