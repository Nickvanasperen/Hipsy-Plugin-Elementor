<?php
class Hipsy_Event_Tijd_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'hipsy_event_tijd'; }
    public function get_title()      { return 'Hipsy · Tijd'; }
    public function get_icon()       { return 'eicon-clock'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords()   { return [ 'hipsy', 'event', 'tijd', 'uur' ]; }

    protected function register_controls() {

        $this->start_controls_section( 'section_content', [
            'label' => 'Inhoud',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        hipsy_register_data_source_controls( $this );
        $this->add_control( 'toon_icoon', [
            'label'        => 'Klok-icoon tonen',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Ja', 'label_off' => 'Nee',
            'return_value' => 'yes', 'default' => 'yes',
        ]);
        $this->add_responsive_control( 'uitlijning', [
            'label'     => 'Uitlijning',
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => [ 'title'=>'Links',  'icon'=>'eicon-text-align-left' ],
                'center'     => [ 'title'=>'Midden', 'icon'=>'eicon-text-align-center' ],
                'flex-end'   => [ 'title'=>'Rechts', 'icon'=>'eicon-text-align-right' ],
            ],
            'selectors' => [ '{{WRAPPER}} .hipsy-event-tijd' => 'justify-content: {{VALUE}};' ],
        ]);
        $this->end_controls_section();

        $this->start_controls_section( 'section_stijl', [
            'label' => 'Stijl',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'typografie', 'selector' => '{{WRAPPER}} .hipsy-tijd-tekst' ]
        );
        $this->add_control( 'kleur', [
            'label'     => 'Tekstkleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .hipsy-tijd-tekst' => 'color: {{VALUE}};' ],
        ]);
        $this->add_control( 'icoon_kleur', [
            'label'     => 'Icoonkleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .hipsy-event-tijd .hipsy-meta-icon' => 'stroke: {{VALUE}};' ],
            'condition' => [ 'toon_icoon' => 'yes' ],
        ]);
        $this->add_control( 'icoon_grootte', [
            'label'      => 'Icoongrootte (px)',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 10, 'max' => 40 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 16 ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-event-tijd .hipsy-meta-icon' => 'width: {{SIZE}}px; height: {{SIZE}}px;' ],
            'condition'  => [ 'toon_icoon' => 'yes' ],
        ]);
        $this->add_responsive_control( 'marge', [
            'label'      => 'Marge',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px','em','rem' ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-event-tijd' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $s    = $this->get_settings_for_display();
        $data = hipsy_get_event_data( hipsy_resolve_event_id( $s ) );
        if ( ! $data ) { echo '<p><em>Geen event gevonden.</em></p>'; return; }

        $tijd = hipsy_format_tijd( $data['datum'], $data['datum_einde'] );
        if ( ! $tijd ) return;

        $icoon = '';
        if ( $s['toon_icoon'] === 'yes' ) {
            $icoon = '<svg class="hipsy-meta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
        }
        echo '<div class="hipsy-event-tijd" style="display:flex;align-items:center;gap:6px;">'
            . $icoon
            . '<span class="hipsy-tijd-tekst">' . esc_html( $tijd ) . '</span>'
            . '</div>';
    }
}
