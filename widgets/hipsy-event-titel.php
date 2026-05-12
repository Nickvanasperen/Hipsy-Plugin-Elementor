<?php
class Hipsy_Event_Titel_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'hipsy_event_titel'; }
    public function get_title()      { return 'Hipsy · Titel'; }
    public function get_icon()       { return 'eicon-t-letter'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords()   { return [ 'hipsy', 'event', 'titel', 'naam' ]; }

    protected function register_controls() {

        // ── INHOUD ──────────────────────────────────────────────────
        $this->start_controls_section( 'section_content', [
            'label' => 'Inhoud',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        hipsy_register_data_source_controls( $this );
        $this->add_control( 'tag', [
            'label'   => 'HTML-tag',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [ 'h1'=>'H1','h2'=>'H2','h3'=>'H3','h4'=>'H4','p'=>'p','div'=>'div' ],
            'default' => 'h2',
        ]);
        $this->add_responsive_control( 'uitlijning', [
            'label'     => 'Uitlijning',
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'left'   => [ 'title'=>'Links',  'icon'=>'eicon-text-align-left' ],
                'center' => [ 'title'=>'Midden', 'icon'=>'eicon-text-align-center' ],
                'right'  => [ 'title'=>'Rechts', 'icon'=>'eicon-text-align-right' ],
            ],
            'selectors' => [ '{{WRAPPER}} .hipsy-event-titel' => 'text-align: {{VALUE}};' ],
        ]);
        $this->end_controls_section();

        // ── STIJL ───────────────────────────────────────────────────
        $this->start_controls_section( 'section_stijl', [
            'label' => 'Stijl',
            'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
        ]);
        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [ 'name' => 'typografie', 'selector' => '{{WRAPPER}} .hipsy-event-titel' ]
        );
        $this->add_control( 'kleur', [
            'label'     => 'Kleur',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'selectors' => [ '{{WRAPPER}} .hipsy-event-titel' => 'color: {{VALUE}};' ],
        ]);
        $this->add_responsive_control( 'marge', [
            'label'      => 'Marge',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px','em','rem' ],
            'selectors'  => [ '{{WRAPPER}} .hipsy-event-titel' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $s    = $this->get_settings_for_display();
        $data = hipsy_get_event_data( hipsy_resolve_event_id( $s ) );
        if ( ! $data ) {
            echo '<p><em>Geen event gevonden. Controleer de databron of kies een specifiek event.</em></p>';
            return;
        }
        $tag = tag_escape( $s['tag'] );
        echo '<' . $tag . ' class="hipsy-event-titel">' . esc_html( $data['titel'] ) . '</' . $tag . '>';
    }
}
