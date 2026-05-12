<?php
/**
 * Widget: Hipsy Ticketknop
 * Een losse knop die linkt naar de Hipsy ticketshop van een event.
 * Volledig aanpasbaar: tekst, stijl, hover, breedte, icoon.
 */
class Hipsy_Event_Ticketknop_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'hipsy_event_ticketknop'; }
    public function get_title()      { return 'Hipsy · Ticketknop'; }
    public function get_icon()       { return 'eicon-button'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords()   { return [ 'hipsy', 'ticket', 'knop', 'button', 'kopen', 'link' ]; }

    protected function register_controls() {

        // ── INHOUD ───────────────────────────────────────────────────
        $this->start_controls_section( 'section_content', [
            'label' => 'Inhoud',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        hipsy_register_data_source_controls( $this );

        $this->add_control( 'knop_tekst', [
            'label'   => 'Knoptekst',
            'type'    => \Elementor\Controls_Manager::TEXT,
            'default' => 'Bestel tickets',
        ]);

        $this->add_control( 'nieuw_tabblad', [
            'label'        => 'Openen in nieuw tabblad',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Ja', 'label_off' => 'Nee',
            'return_value' => 'yes', 'default' => 'yes',
        ]);

        $this->add_control( 'verberg_als_leeg', [
            'label'        => 'Verberg als geen ticketlink beschikbaar',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Ja', 'label_off' => 'Nee',
            'return_value' => 'yes', 'default' => 'yes',
            'description'  => 'Als er geen ticketshop-URL in Hipsy staat, wordt de knop niet getoond.',
        ]);

        $this->add_responsive_control( 'uitlijning', [
            'label'     => 'Uitlijning',
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title' => 'Links',   'icon' => 'eicon-text-align-left' ],
                'center'     => [ 'title' => 'Midden',  'icon' => 'eicon-text-align-center' ],
                'flex-end'   => [ 'title' => 'Rechts',  'icon' => 'eicon-text-align-right' ],
                'stretch'    => [ 'title' => 'Volledig breedte', 'icon' => 'eicon-text-align-justify' ],
            ],
            'default'   => 'flex-start',
            'selectors_dictionary' => [
                'flex-start' => 'justify-content:flex-start',
                'center'     => 'justify-content:center',
                'flex-end'   => 'justify-content:flex-end',
                'stretch'    => 'justify-content:stretch',
            ],
            'selectors' => [
                '{{WRAPPER}} .hipsy-ticketknop-wrapper' => '{{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        // ── STIJL KNOP ───────────────────────────────────────────────
        $this->start_controls_section( 'section_stijl_knop', [
            'label' => 'Knop',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'knop_typografie', 'selector' => '{{WRAPPER}} .hipsy-ticketknop' ]
        );

        $this->add_responsive_control( 'knop_breedte', [
            'label'     => 'Breedte',
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => [
                'auto'  => 'Automatisch (past om tekst)',
                '100%'  => 'Volledig (100%)',
            ],
            'default'   => 'auto',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticketknop' => 'width: {{VALUE}};' ],
        ]);

        $this->add_responsive_control( 'knop_padding', [
            'label'      => 'Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top'=>'14','right'=>'32','bottom'=>'14','left'=>'32','unit'=>'px' ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-ticketknop' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->add_responsive_control( 'knop_radius', [
            'label'      => 'Afgeronde hoeken',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', '%' ],
            'default'    => [ 'top'=>'6','right'=>'6','bottom'=>'6','left'=>'6','unit'=>'px' ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-ticketknop' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        // Normaal
        $this->start_controls_tabs( 'tabs_knop_stijl' );

        $this->start_controls_tab( 'tab_knop_normaal', [ 'label' => 'Normaal' ]);

        $this->add_control( 'knop_achtergrond', [
            'label'     => 'Achtergrondkleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#111827',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticketknop' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'knop_tekstkleur', [
            'label'     => 'Tekstkleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticketknop' => 'color: {{VALUE}};' ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [ 'name' => 'knop_rand', 'selector' => '{{WRAPPER}} .hipsy-ticketknop' ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [ 'name' => 'knop_schaduw', 'selector' => '{{WRAPPER}} .hipsy-ticketknop' ]
        );

        $this->end_controls_tab();

        // Hover
        $this->start_controls_tab( 'tab_knop_hover', [ 'label' => 'Hover' ]);

        $this->add_control( 'knop_achtergrond_hover', [
            'label'     => 'Achtergrondkleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#374151',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticketknop:hover' => 'background-color: {{VALUE}};' ],
        ]);

        $this->add_control( 'knop_tekstkleur_hover', [
            'label'     => 'Tekstkleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .hipsy-ticketknop:hover' => 'color: {{VALUE}};' ],
        ]);

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [ 'name' => 'knop_schaduw_hover', 'selector' => '{{WRAPPER}} .hipsy-ticketknop:hover' ]
        );

        $this->add_control( 'knop_hover_animatie', [
            'label'     => 'Animatie',
            'type'      => \Elementor\Controls_Manager::HOVER_ANIMATION,
            'prefix_class' => 'elementor-animation-',
        ]);

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control( 'knop_marge', [
            'label'      => 'Marge',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'separator'  => 'before',
            'selectors'  => [ '{{WRAPPER}} .hipsy-ticketknop-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);

        $this->end_controls_section();
    }

    protected function render() {
        $s    = $this->get_settings_for_display();
        $data = hipsy_get_event_data( hipsy_resolve_event_id( $s ) );

        if ( ! $data ) {
            // In de Elementor editor: toon placeholder
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<p style="color:#999;font-style:italic;">Hipsy · Ticketknop — selecteer een event of gebruik in dynamisch template.</p>';
            }
            return;
        }

        $ticket_url = $data['link'];

        if ( empty( $ticket_url ) ) {
            if ( $s['verberg_als_leeg'] !== 'yes' && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<p style="color:#999;font-style:italic;">Geen ticketlink gevonden voor dit event.</p>';
            }
            return;
        }

        $target = $s['nieuw_tabblad'] === 'yes' ? ' target="_blank" rel="noopener noreferrer"' : '';

        echo '<div class="hipsy-ticketknop-wrapper" style="display:flex;">';
        echo '<a href="' . esc_url( $ticket_url ) . '" class="hipsy-ticketknop elementor-animation-' . esc_attr( $s['knop_hover_animatie'] ?? '' ) . '"' . $target . '>';
        echo esc_html( $s['knop_tekst'] );
        echo '</a>';
        echo '</div>';

        echo '<style>
        .hipsy-ticketknop {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: background-color .2s ease, color .2s ease, box-shadow .2s ease, transform .2s ease;
            white-space: nowrap;
        }
        .hipsy-ticketknop-wrapper {
            display: flex;
        }
        </style>';
    }
}
