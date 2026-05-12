<?php
/**
 * Widget: Hipsy Filter Bar — v4.0
 * 
 * AJAX filter widget met Query ID koppeling.
 * Werkt samen met Hipsy Events Grid via unieke Query ID.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Check if Elementor is installed
if ( ! class_exists( '\Elementor\Widget_Base' ) ) {
    return;
}

class Hipsy_Filter_Bar_Widget extends \Elementor\Widget_Base {
    
    public function get_name()       { return 'hipsy_filter_bar'; }
    public function get_title()      { return 'Hipsy · Filter Bar (v4.0)'; }
    public function get_icon()       { return 'eicon-filter'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords()   { return [ 'hipsy', 'filter', 'search', 'ajax', 'categorie' ]; }
    
    protected function register_controls() {
        
        // ══════════════════════════════════════════
        // KOPPELING & TARGET
        // ══════════════════════════════════════════
        $this->start_controls_section( 'sec_koppeling', [
            'label' => '🔗 Koppeling',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );
        
        $this->add_control( '_info_query_id', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => '<div style="background:#f0f9ff;border-radius:6px;padding:.8rem;font-size:.85rem;color:#0c4a6e;line-height:1.6">
                <strong>Query ID koppeling</strong><br>
                Geef deze filter een unieke <strong>Query ID</strong> (bijv. <code>agenda</code>).<br>
                Gebruik dezelfde Query ID in je Events Grid widget om ze te koppelen.<br><br>
                <strong>Voorbeeld:</strong><br>
                Filter → Query ID: <code>homepage-events</code><br>
                Grid → Query ID: <code>homepage-events</code>
            </div>',
            'content_classes' => 'elementor-panel-alert',
        ] );
        
        $this->add_control( 'query_id', [
            'label'       => 'Query ID',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => 'events',
            'placeholder' => 'bijv: agenda, homepage-events',
            'description' => 'Unieke ID om filter en grid te koppelen. Gebruik alleen letters, cijfers en streepjes.',
        ] );
        
        $this->end_controls_section();
        
        // ══════════════════════════════════════════
        // FILTER OPTIES
        // ══════════════════════════════════════════
        $this->start_controls_section( 'sec_filters', [
            'label' => '⚙️ Filters',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );
        
        $this->add_control( 'toon_zoekbalk', [
            'label'        => 'Zoekbalk',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );
        
        $this->add_control( 'zoek_placeholder', [
            'label'     => 'Placeholder tekst',
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => 'Zoek een event...',
            'condition' => [ 'toon_zoekbalk' => 'yes' ],
        ] );
        
        $this->add_control( 'toon_categorie_filter', [
            'label'        => 'Categorie filters',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'yes',
        ] );
        
        $this->add_control( 'categorie_all_label', [
            'label'     => '"Alle" label',
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => 'Alle events',
            'condition' => [ 'toon_categorie_filter' => 'yes' ],
        ] );
        
        $this->add_control( 'toon_locatie_filter', [
            'label'        => 'Locatie filter',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'no',
        ] );
        
        $this->end_controls_section();
        
        // ══════════════════════════════════════════
        // STIJL: ZOEKBALK
        // ══════════════════════════════════════════
        $this->start_controls_section( 'sec_stijl_zoek', [
            'label'     => 'Zoekbalk',
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [ 'toon_zoekbalk' => 'yes' ],
        ] );
        
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'zoek_typography',
            'selector' => '{{WRAPPER}} .hfw-search-input',
        ] );
        
        $this->add_control( 'zoek_bg_color', [
            'label'     => 'Achtergrond',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .hfw-search-input' => 'background-color: {{VALUE}};' ],
        ] );
        
        $this->add_control( 'zoek_border_color', [
            'label'     => 'Rand',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#d1d5db',
            'selectors' => [ '{{WRAPPER}} .hfw-search-input' => 'border-color: {{VALUE}};' ],
        ] );
        
        $this->add_responsive_control( 'zoek_radius', [
            'label'      => 'Afgeronde hoeken',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 8 ],
            'selectors'  => [ '{{WRAPPER}} .hfw-search-input' => 'border-radius: {{SIZE}}{{UNIT}};' ],
        ] );
        
        $this->end_controls_section();
        
        // ══════════════════════════════════════════
        // STIJL: CATEGORIE BUTTONS
        // ══════════════════════════════════════════
        $this->start_controls_section( 'sec_stijl_cat', [
            'label'     => 'Categorie Buttons',
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => [ 'toon_categorie_filter' => 'yes' ],
        ] );
        
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
            'name'     => 'cat_typography',
            'selector' => '{{WRAPPER}} .hfw-filter-btn',
        ] );
        
        $this->start_controls_tabs( 'cat_tabs' );
        
        $this->start_controls_tab( 'cat_normal', [ 'label' => 'Normaal' ] );
        $this->add_control( 'cat_bg', [
            'label'     => 'Achtergrond',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#f3f4f6',
            'selectors' => [ '{{WRAPPER}} .hfw-filter-btn' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_control( 'cat_color', [
            'label'     => 'Tekst',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#374151',
            'selectors' => [ '{{WRAPPER}} .hfw-filter-btn' => 'color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();
        
        $this->start_controls_tab( 'cat_active', [ 'label' => 'Actief' ] );
        $this->add_control( 'cat_bg_active', [
            'label'     => 'Achtergrond',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#7c3aed',
            'selectors' => [ '{{WRAPPER}} .hfw-filter-btn.is-active' => 'background-color: {{VALUE}};' ],
        ] );
        $this->add_control( 'cat_color_active', [
            'label'     => 'Tekst',
            'type'      => \Elementor\Controls_Manager::COLOR,
            'default'   => '#ffffff',
            'selectors' => [ '{{WRAPPER}} .hfw-filter-btn.is-active' => 'color: {{VALUE}};' ],
        ] );
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_responsive_control( 'cat_radius', [
            'label'      => 'Afgeronde hoeken',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
            'default'    => [ 'unit' => 'px', 'size' => 20 ],
            'selectors'  => [ '{{WRAPPER}} .hfw-filter-btn' => 'border-radius: {{SIZE}}{{UNIT}};' ],
            'separator'  => 'before',
        ] );
        
        $this->add_responsive_control( 'cat_padding', [
            'label'      => 'Padding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em' ],
            'default'    => [ 'top' => '8', 'right' => '16', 'bottom' => '8', 'left' => '16', 'unit' => 'px' ],
            'selectors'  => [ '{{WRAPPER}} .hfw-filter-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
        ] );
        
        $this->end_controls_section();
    }
    
    protected function render() {
        $s = $this->get_settings_for_display();
        
        $query_id   = sanitize_key( $s['query_id'] ?: 'events' );
        $widget_id  = 'hfw-' . $this->get_id();
        $target_id  = 'heg-' . $query_id; // Events Grid gebruikt dezelfde query_id
        
        $terms = [];
        if ( $s['toon_categorie_filter'] === 'yes' ) {
            $terms = get_terms( [ 'taxonomy' => 'event_categorie', 'hide_empty' => true ] ) ?: [];
        }
        
        echo '<div id="' . esc_attr( $widget_id ) . '" class="hfw-wrapper hfw-v4" data-target-id="' . esc_attr( $target_id ) . '" data-query-id="' . esc_attr( $query_id ) . '">';
        
        // Zoekbalk
        if ( $s['toon_zoekbalk'] === 'yes' ) {
            echo '<div class="hfw-search-wrap">';
            echo '<svg class="hfw-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>';
            echo '<input type="text" class="hfw-search-input" placeholder="' . esc_attr( $s['zoek_placeholder'] ) . '" autocomplete="off">';
            echo '</div>';
        }
        
        // Categorie filters
        if ( $s['toon_categorie_filter'] === 'yes' && $terms ) {
            echo '<div class="hfw-filters">';
            echo '<button class="hfw-filter-btn is-active" data-category="">' . esc_html( $s['categorie_all_label'] ) . '</button>';
            foreach ( $terms as $term ) {
                echo '<button class="hfw-filter-btn" data-category="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</button>';
            }
            echo '</div>';
        }
        
        // Locatie filter (if enabled)
        if ( $s['toon_locatie_filter'] === 'yes' ) {
            $locaties = hipsy_get_unique_locations();
            if ( $locaties ) {
                echo '<div class="hfw-location-wrap">';
                echo '<select class="hfw-location-select">';
                echo '<option value="">Alle locaties</option>';
                foreach ( $locaties as $loc ) {
                    echo '<option value="' . esc_attr( $loc ) . '">' . esc_html( $loc ) . '</option>';
                }
                echo '</select>';
                echo '</div>';
            }
        }
        
        echo '</div>'; // .hfw-wrapper
        
        // Inline CSS
        ?>
        <style>
        .hfw-v4 {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .hfw-search-wrap {
            position: relative;
        }
        .hfw-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }
        .hfw-search-input {
            width: 100%;
            padding: 10px 12px 10px 40px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .hfw-search-input:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }
        .hfw-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .hfw-filter-btn {
            padding: 8px 16px;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .hfw-filter-btn:hover {
            background: #e5e7eb;
        }
        .hfw-filter-btn.is-active {
            background: #7c3aed;
            color: #ffffff;
        }
        .hfw-location-select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }
        .hew-grid.is-loading,
        .hew-lijst.is-loading {
            opacity: 0.5;
            pointer-events: none;
        }
        </style>
        <?php
    }
}
