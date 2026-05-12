<?php
/**
 * Widget: Hipsy Zoek & Filter
 * Client-side zoekbalk + categorie-tabs die de Events Grid op dezelfde pagina filteren.
 * Werkt samen met hipsy-events-grid via CSS classes (geen aparte AJAX call nodig).
 */
class Hipsy_Zoek_Filter_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'hipsy_zoek_filter'; }
    public function get_title()      { return 'Hipsy · Zoek & Filter'; }
    public function get_icon()       { return 'eicon-search'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords()   { return [ 'hipsy','zoeken','filter','categorie','search' ]; }

    protected function register_controls() {

        $this->start_controls_section('sec_content', [ 'label'=>'Instellingen','tab'=>\Elementor\Controls_Manager::TAB_CONTENT ]);

        $this->add_control('toon_zoekbalk', [ 'label'=>'Zoekbalk tonen','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes' ]);
        $this->add_control('zoek_placeholder', [ 'label'=>'Placeholder zoekbalk','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Zoek een event...','condition'=>['toon_zoekbalk'=>'yes'] ]);
        $this->add_control('toon_categorie_tabs', [ 'label'=>'Categorie-tabs tonen','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes' ]);
        $this->add_control('alle_label', [ 'label'=>'Label "Alle" tab','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Alle events','condition'=>['toon_categorie_tabs'=>'yes'] ]);
        $this->add_control('filter_target', [ 'label'=>'Klasse grid-container','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'hipsy-grid','description'=>'CSS-klasse van het Events Grid element dat gefilterd moet worden. Standaard: hipsy-grid' ]);

        $this->end_controls_section();

        // Stijl zoekbalk
        $this->start_controls_section('sec_stijl_zoek', [ 'label'=>'Zoekbalk','tab'=>\Elementor\Controls_Manager::TAB_STYLE,'condition'=>['toon_zoekbalk'=>'yes'] ]);
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name'=>'zoek_typ','selector'=>'{{WRAPPER}} .hfw-zoek input' ]);
        $this->add_control('zoek_bg', [ 'label'=>'Achtergrond','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>['{{WRAPPER}} .hfw-zoek input'=>'background:{{VALUE}};'] ]);
        $this->add_control('zoek_rand_kleur', [ 'label'=>'Randkleur','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#d1d5db','selectors'=>['{{WRAPPER}} .hfw-zoek input'=>'border-color:{{VALUE}};'] ]);
        $this->add_responsive_control('zoek_radius', [ 'label'=>'Afgerond','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>['px'],'default'=>['unit'=>'px','size'=>6],'selectors'=>['{{WRAPPER}} .hfw-zoek input'=>'border-radius:{{SIZE}}px;'] ]);
        $this->end_controls_section();

        // Stijl tabs
        $this->start_controls_section('sec_stijl_tabs', [ 'label'=>'Categorie-tabs','tab'=>\Elementor\Controls_Manager::TAB_STYLE,'condition'=>['toon_categorie_tabs'=>'yes'] ]);
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name'=>'tab_typ','selector'=>'{{WRAPPER}} .hfw-tab' ]);
        $this->add_control('tab_bg', [ 'label'=>'Achtergrond','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#f3f4f6','selectors'=>['{{WRAPPER}} .hfw-tab'=>'background:{{VALUE}};'] ]);
        $this->add_control('tab_kleur', [ 'label'=>'Tekstkleur','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#374151','selectors'=>['{{WRAPPER}} .hfw-tab'=>'color:{{VALUE}};'] ]);
        $this->add_control('tab_actief_bg', [ 'label'=>'Achtergrond actief','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#7c3aed','selectors'=>['{{WRAPPER}} .hfw-tab.is-actief'=>'background:{{VALUE}};'] ]);
        $this->add_control('tab_actief_kleur', [ 'label'=>'Tekstkleur actief','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>['{{WRAPPER}} .hfw-tab.is-actief'=>'color:{{VALUE}};'] ]);
        $this->add_responsive_control('tab_radius', [ 'label'=>'Afgerond','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>['px'],'default'=>['unit'=>'px','size'=>20],'selectors'=>['{{WRAPPER}} .hfw-tab'=>'border-radius:{{SIZE}}px;'] ]);
        $this->end_controls_section();
    }

    protected function render() {
        $s      = $this->get_settings_for_display();
        $terms  = get_terms([ 'taxonomy'=>'event_categorie','hide_empty'=>true ]) ?: [];
        $target = esc_js( $s['filter_target'] ?: 'hipsy-grid' );
        $uid    = 'hfw-' . $this->get_id();

        echo '<div class="hfw-wrapper" id="' . esc_attr($uid) . '">';

        // Zoekbalk
        if ( $s['toon_zoekbalk'] === 'yes' ) {
            echo '<div class="hfw-zoek">';
            echo '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>';
            echo '<input type="text" placeholder="' . esc_attr($s['zoek_placeholder']) . '" class="hfw-zoek-input" autocomplete="off">';
            echo '</div>';
        }

        // Categorie tabs
        if ( $s['toon_categorie_tabs'] === 'yes' && $terms ) {
            echo '<div class="hfw-tabs">';
            echo '<button class="hfw-tab is-actief" data-cat="">' . esc_html($s['alle_label']) . '</button>';
            foreach ( $terms as $term ) {
                echo '<button class="hfw-tab" data-cat="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</button>';
            }
            echo '</div>';
        }

        echo '</div>'; // hfw-wrapper

        // JavaScript: client-side filter op kaarten
        echo "<script>
        (function(){
            var uid = document.getElementById('{$uid}');
            if(!uid) return;

            function getCards() {
                return document.querySelectorAll('.{$target} .hipsy-card, .{$target} .swiper-slide');
            }

            function filter() {
                var zoek   = uid.querySelector('.hfw-zoek-input');
                var zoekVal = zoek ? zoek.value.toLowerCase() : '';
                var actief = uid.querySelector('.hfw-tab.is-actief');
                var cat    = actief ? actief.dataset.cat : '';
                getCards().forEach(function(card) {
                    var titel  = (card.querySelector('.card-titel')     || {}).innerText || '';
                    var loc    = (card.querySelector('.card-locatie')   || {}).innerText || '';
                    var catEl  = card.dataset.categories || '';
                    var matchZoek = !zoekVal || titel.toLowerCase().includes(zoekVal) || loc.toLowerCase().includes(zoekVal);
                    var matchCat  = !cat || catEl.includes(cat);
                    card.style.display = (matchZoek && matchCat) ? '' : 'none';
                });
            }

            var zoekInput = uid.querySelector('.hfw-zoek-input');
            if(zoekInput) zoekInput.addEventListener('input', filter);

            uid.querySelectorAll('.hfw-tab').forEach(function(tab){
                tab.addEventListener('click', function(){
                    uid.querySelectorAll('.hfw-tab').forEach(function(t){ t.classList.remove('is-actief'); });
                    tab.classList.add('is-actief');
                    filter();
                });
            });
        })();
        </script>";

        echo '<style>
        .hfw-wrapper { display: flex; flex-direction: column; gap: .75rem; }
        .hfw-zoek { position: relative; display: flex; align-items: center; }
        .hfw-zoek svg { position: absolute; left: .75rem; color: #9ca3af; pointer-events: none; }
        .hfw-zoek input { width: 100%; padding: .6rem .75rem .6rem 2.2rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: .9rem; outline: none; }
        .hfw-zoek input:focus { border-color: #7c3aed; box-shadow: 0 0 0 2px rgba(124,58,237,.15); }
        .hfw-tabs { display: flex; flex-wrap: wrap; gap: .4rem; }
        .hfw-tab { padding: .35rem .85rem; background: #f3f4f6; color: #374151; border: none; border-radius: 20px; font-size: .83rem; font-weight: 500; cursor: pointer; transition: background .15s, color .15s; }
        .hfw-tab:hover { background: #e5e7eb; }
        .hfw-tab.is-actief { background: #7c3aed; color: #fff; }
        </style>';
    }
}
