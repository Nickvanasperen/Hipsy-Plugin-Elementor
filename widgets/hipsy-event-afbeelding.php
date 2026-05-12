<?php
class Hipsy_Event_Afbeelding_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'hipsy_event_afbeelding'; }
    public function get_title()      { return 'Hipsy · Afbeelding'; }
    public function get_icon()       { return 'eicon-image'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords()   { return [ 'hipsy', 'event', 'afbeelding', 'foto' ]; }

    protected function register_controls() {

        $this->start_controls_section( 'section_content', [
            'label' => 'Inhoud',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        hipsy_register_data_source_controls( $this );
        $this->add_control( 'afbeelding_grootte', [
            'label'   => 'Bestandsgrootte',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [ 'thumbnail'=>'Klein','medium'=>'Middel','large'=>'Groot','full'=>'Volledig' ],
            'default' => 'large',
        ]);
        $this->add_control( 'als_link', [
            'label'        => 'Klikbaar naar event',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Ja', 'label_off' => 'Nee',
            'return_value' => 'yes', 'default' => 'no',
        ]);
        $this->end_controls_section();

        // ── STIJL ───────────────────────────────────────────────────
        $this->start_controls_section( 'section_stijl', [
            'label' => 'Stijl',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_responsive_control( 'breedte', [
            'label'      => 'Breedte',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ '%','px' ],
            'range'      => [ '%' => [ 'min'=>10,'max'=>100 ], 'px' => [ 'min'=>50,'max'=>1400 ] ],
            'default'    => [ 'unit'=>'%','size'=>100 ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-event-afbeelding img' => 'width: {{SIZE}}{{UNIT}};' ],
        ]);
        $this->add_responsive_control( 'hoogte', [
            'label'      => 'Hoogte (0 = automatisch)',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px','vh' ],
            'range'      => [ 'px' => [ 'min'=>0,'max'=>800 ] ],
            'default'    => [ 'unit'=>'px','size'=>0 ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-event-afbeelding img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;' ],
        ]);
        $this->add_responsive_control( 'border_radius', [
            'label'      => 'Afgeronde hoeken',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px','%' ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-event-afbeelding img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [ 'name' => 'schaduw', 'selector' => '{{WRAPPER}} .hipsy-event-afbeelding img' ]
        );
        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [ 'name' => 'rand', 'selector' => '{{WRAPPER}} .hipsy-event-afbeelding img' ]
        );
        $this->add_responsive_control( 'uitlijning', [
            'label'     => 'Uitlijning',
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => [ 'title'=>'Links',  'icon'=>'eicon-text-align-left' ],
                'center' => [ 'title'=>'Midden', 'icon'=>'eicon-text-align-center' ],
                'right'  => [ 'title'=>'Rechts', 'icon'=>'eicon-text-align-right' ],
            ],
            'selectors' => [ '{{WRAPPER}} .hipsy-event-afbeelding' => 'text-align: {{VALUE}};' ],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $s    = $this->get_settings_for_display();
        $data = hipsy_get_event_data( hipsy_resolve_event_id( $s ) );
        if ( ! $data ) { echo '<p><em>Geen event gevonden.</em></p>'; return; }

        $thumb = get_the_post_thumbnail( $data['id'], $s['afbeelding_grootte'], [
            'style' => 'display:block;',
            'alt'   => esc_attr( $data['titel'] ),
        ]);
        if ( ! $thumb ) { echo '<p><em>Geen afbeelding beschikbaar.</em></p>'; return; }

        $open  = '<div class="hipsy-event-afbeelding">';
        $close = '</div>';
        if ( $s['als_link'] === 'yes' && $data['permalink'] ) {
            $open  = '<a href="' . esc_url( $data['permalink'] ) . '" class="hipsy-event-afbeelding" style="display:block;">';
            $close = '</a>';
        }
        echo $open . $thumb . $close;
    }
}
