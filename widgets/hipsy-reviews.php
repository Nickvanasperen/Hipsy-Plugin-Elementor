<?php
/**
 * Widget: Hipsy Reviews
 *
 * ⚠️  Hipsy heeft GEEN reviews-endpoint in hun API (v1).
 * Dit widget laat je handmatig reviews invoeren via de Elementor sidebar,
 * of haalt ze op uit WordPress Comments als je die inschakelt op het 'events' post type.
 *
 * Twee modi:
 *  1. Handmatig   — reviews rechtstreeks in de widget invullen
 *  2. WP Comments — toont goedgekeurde reacties op het huidig event (voor theme-templates)
 */
class Hipsy_Reviews_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'hipsy_reviews'; }
    public function get_title()      { return 'Hipsy · Reviews'; }
    public function get_icon()       { return 'eicon-testimonial'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords()   { return [ 'hipsy','review','testimonial','beoordeling','reactie' ]; }

    protected function register_controls() {

        $this->start_controls_section('sec_content', [ 'label'=>'Bron & Instellingen','tab'=>\Elementor\Controls_Manager::TAB_CONTENT ]);

        $this->add_control('bron', [
            'label'   => 'Bron',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'handmatig' => 'Handmatig invoeren',
                'comments'  => 'WordPress Comments (huidig event)',
            ],
            'default'     => 'handmatig',
            'description' => 'Hipsy heeft geen reviews-API. Kies "Handmatig" om reviews zelf in te vullen, of "WordPress Comments" als je de comments inschakelt op het events post type.',
        ]);

        $this->add_control('titel', [ 'label'=>'Koptekst','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Wat anderen zeggen' ]);

        $this->add_control('layout', [
            'label'   => 'Layout',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [ 'grid'=>'Grid','lijst'=>'Lijst','carrousel'=>'Carrousel' ],
            'default' => 'grid',
        ]);

        $this->add_responsive_control('kolommen', [
            'label'   => 'Kolommen (grid)',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [ '1'=>'1','2'=>'2','3'=>'3' ],
            'default' => '3','tablet_default'=>'2','mobile_default'=>'1',
            'condition' => [ 'layout' => 'grid' ],
        ]);

        $this->add_control('toon_sterren', [ 'label'=>'Sterren tonen','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes' ]);
        $this->add_control('toon_datum', [ 'label'=>'Datum tonen','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'no' ]);

        $this->end_controls_section();

        // Handmatige reviews
        $this->start_controls_section('sec_reviews', [ 'label'=>'Reviews (handmatig)','tab'=>\Elementor\Controls_Manager::TAB_CONTENT,'condition'=>['bron'=>'handmatig'] ]);

        $repeater = new \Elementor\Repeater();
        $repeater->add_control('naam',  [ 'label'=>'Naam','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Anna de Vries' ]);
        $repeater->add_control('tekst', [ 'label'=>'Review','type'=>\Elementor\Controls_Manager::TEXTAREA,'default'=>'Een onvergetelijke avond vol echte verbinding.' ]);
        $repeater->add_control('sterren', [
            'label'   => 'Sterren',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [ '5'=>'⭐⭐⭐⭐⭐','4'=>'⭐⭐⭐⭐','3'=>'⭐⭐⭐','2'=>'⭐⭐','1'=>'⭐' ],
            'default' => '5',
        ]);
        $repeater->add_control('datum_review', [ 'label'=>'Datum','type'=>\Elementor\Controls_Manager::TEXT,'placeholder'=>'April 2026' ]);

        $this->add_control('reviews_lijst', [
            'label'   => 'Reviews',
            'type'    => \Elementor\Controls_Manager::REPEATER,
            'fields'  => $repeater->get_controls(),
            'default' => [
                [ 'naam'=>'Marjolein','tekst'=>'Wat een mooie avond. Eindelijk echte gesprekken.','sterren'=>'5','datum_review'=>'April 2026' ],
                [ 'naam'=>'Thomas','tekst'=>'Ik voelde me direct welkom. Ga zeker terug!','sterren'=>'5','datum_review'=>'Maart 2026' ],
                [ 'naam'=>'Lisa','tekst'=>'De danssessie was een hoogtepunt. Aanrader!','sterren'=>'5','datum_review'=>'Februari 2026' ],
            ],
            'title_field' => '{{{ naam }}}',
        ]);

        $this->end_controls_section();

        // ── STIJL KOPTEKST ──────────────────────────────────────────
        $this->start_controls_section('sec_stijl_kop', [ 'label'=>'Koptekst','tab'=>\Elementor\Controls_Manager::TAB_STYLE ]);
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name'=>'kop_typ','selector'=>'{{WRAPPER}} .hrw-titel' ]);
        $this->add_control('kop_kleur', [ 'label'=>'Kleur','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .hrw-titel'=>'color:{{VALUE}};'] ]);
        $this->add_responsive_control('kop_uitlijning', [ 'label'=>'Uitlijning','type'=>\Elementor\Controls_Manager::CHOOSE,'options'=>['left'=>['title'=>'Links','icon'=>'eicon-text-align-left'],'center'=>['title'=>'Midden','icon'=>'eicon-text-align-center'],'right'=>['title'=>'Rechts','icon'=>'eicon-text-align-right']],'selectors'=>['{{WRAPPER}} .hrw-titel'=>'text-align:{{VALUE}};'] ]);
        $this->add_responsive_control('kop_marge', [ 'label'=>'Ruimte onder koptekst','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>['px'],'default'=>['unit'=>'px','size'=>24],'selectors'=>['{{WRAPPER}} .hrw-titel'=>'margin-bottom:{{SIZE}}px;'] ]);
        $this->end_controls_section();

        // ── STIJL KAART ─────────────────────────────────────────────
        $this->start_controls_section('sec_stijl_card', [ 'label'=>'Review-kaart','tab'=>\Elementor\Controls_Manager::TAB_STYLE ]);
        $this->add_control('card_bg', [ 'label'=>'Achtergrond','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#f9fafb','selectors'=>['{{WRAPPER}} .hrw-card'=>'background:{{VALUE}};'] ]);
        $this->add_group_control( \Elementor\Group_Control_Border::get_type(), [ 'name'=>'card_rand','selector'=>'{{WRAPPER}} .hrw-card' ]);
        $this->add_responsive_control('card_radius', [ 'label'=>'Afgeronde hoeken','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>['px','%'],'default'=>['top'=>'10','right'=>'10','bottom'=>'10','left'=>'10','unit'=>'px'],'selectors'=>['{{WRAPPER}} .hrw-card'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'] ]);
        $this->add_group_control( \Elementor\Group_Control_Box_Shadow::get_type(), [ 'name'=>'card_schaduw','selector'=>'{{WRAPPER}} .hrw-card' ]);
        $this->add_responsive_control('card_padding', [ 'label'=>'Padding','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>['px','em'],'default'=>['top'=>'20','right'=>'20','bottom'=>'20','left'=>'20','unit'=>'px'],'selectors'=>['{{WRAPPER}} .hrw-card'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'] ]);
        $this->end_controls_section();

        // ── STIJL TEKST ─────────────────────────────────────────────
        $this->start_controls_section('sec_stijl_tekst', [ 'label'=>'Review tekst','tab'=>\Elementor\Controls_Manager::TAB_STYLE ]);
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name'=>'tekst_typ','selector'=>'{{WRAPPER}} .hrw-tekst' ]);
        $this->add_control('tekst_kleur', [ 'label'=>'Kleur','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#374151','selectors'=>['{{WRAPPER}} .hrw-tekst'=>'color:{{VALUE}};'] ]);
        $this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [ 'name'=>'naam_typ','selector'=>'{{WRAPPER}} .hrw-naam' ]);
        $this->add_control('naam_kleur', [ 'label'=>'Naamkleur','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#111827','selectors'=>['{{WRAPPER}} .hrw-naam'=>'color:{{VALUE}};'] ]);
        $this->add_control('ster_kleur', [ 'label'=>'Sterrenkleur','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#f59e0b','selectors'=>['{{WRAPPER}} .hrw-sterren'=>'color:{{VALUE}};'] ]);
        $this->end_controls_section();
    }

    protected function render() {
        $s = $this->get_settings_for_display();

        // Koptekst
        if ( ! empty($s['titel']) ) {
            echo '<h3 class="hrw-titel">' . esc_html($s['titel']) . '</h3>';
        }

        $reviews = [];

        if ( $s['bron'] === 'handmatig' ) {
            foreach ( $s['reviews_lijst'] ?? [] as $r ) {
                $reviews[] = [
                    'naam'   => $r['naam'],
                    'tekst'  => $r['tekst'],
                    'sterren'=> (int)$r['sterren'],
                    'datum'  => $r['datum_review'],
                ];
            }
        } else {
            // WordPress comments
            $comments = get_comments([ 'post_id'=>get_the_ID(),'status'=>'approve','number'=>20 ]);
            foreach ( $comments as $c ) {
                $reviews[] = [
                    'naam'   => $c->comment_author,
                    'tekst'  => $c->comment_content,
                    'sterren'=> (int)(get_comment_meta($c->comment_ID,'rating',true) ?: 5),
                    'datum'  => date_i18n('F Y', strtotime($c->comment_date)),
                ];
            }
            if ( ! $reviews ) {
                echo '<p><em>Geen reacties gevonden. Schakel comments in op het events post type om deze bron te gebruiken.</em></p>';
                return;
            }
        }

        if ( ! $reviews ) {
            echo '<p><em>Geen reviews.</em></p>';
            return;
        }

        $layout = $s['layout'];
        $cols   = $s['kolommen'] ?? '3';
        $uid    = 'hrw-' . $this->get_id();

        if ( $layout === 'carrousel' ) {
            echo '<div class="swiper ' . esc_attr($uid) . '"><div class="swiper-wrapper">';
        } else {
            $grid_style = $layout === 'grid' ? 'display:grid;grid-template-columns:repeat(' . (int)$cols . ',1fr);gap:1.25rem;' : 'display:flex;flex-direction:column;gap:1rem;';
            echo '<div class="hrw-grid" style="' . $grid_style . '">';
        }

        foreach ( $reviews as $r ) {
            $wrap = $layout === 'carrousel' ? 'swiper-slide' : '';
            echo '<div class="' . trim($wrap) . '"><div class="hrw-card">';

            if ( $s['toon_sterren'] === 'yes' ) {
                $stars = str_repeat('★', $r['sterren']) . str_repeat('☆', 5 - $r['sterren']);
                echo '<div class="hrw-sterren">' . esc_html($stars) . '</div>';
            }
            echo '<p class="hrw-tekst">"' . esc_html($r['tekst']) . '"</p>';
            echo '<div class="hrw-footer">';
            echo '<strong class="hrw-naam">' . esc_html($r['naam']) . '</strong>';
            if ( $s['toon_datum'] === 'yes' && $r['datum'] ) {
                echo ' <span class="hrw-datum" style="color:#9ca3af;font-size:.8em;">— ' . esc_html($r['datum']) . '</span>';
            }
            echo '</div></div></div>';
        }

        if ( $layout === 'carrousel' ) {
            echo '</div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div><div class="swiper-pagination"></div></div>';
            echo "<script>document.addEventListener('DOMContentLoaded',function(){ new Swiper('.{$uid}',{ slidesPerView:1, spaceBetween:20, navigation:{nextEl:'.{$uid} .swiper-button-next',prevEl:'.{$uid} .swiper-button-prev'}, pagination:{el:'.{$uid} .swiper-pagination',clickable:true}, breakpoints:{768:{slidesPerView:2},1024:{slidesPerView:" . (int)$cols . "}} }); }); </script>";
        } else {
            echo '</div>';
        }

        echo '<style>
        .hrw-card { background: #f9fafb; border-radius: 10px; padding: 1.25rem; display: flex; flex-direction: column; gap: .5rem; height: 100%; }
        .hrw-sterren { color: #f59e0b; font-size: 1.1rem; letter-spacing: .1em; }
        .hrw-tekst { margin: 0; line-height: 1.6; font-style: italic; flex: 1; }
        .hrw-naam { font-weight: 700; }
        </style>';
    }
}
