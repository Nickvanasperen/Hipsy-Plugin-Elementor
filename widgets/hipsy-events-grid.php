<?php
/**
 * Widget: Hipsy Events Grid — v4.2
 *
 * 5 layouts + volledig configureerbare kaartopbouw:
 *   - Kaartoriëntatie per device (verticaal/horizontaal/gespiegeld)
 *   - Volgorde van elk blok instelbaar via nummers
 *   - Afbeeldingsbreedte responsief
 */
class Hipsy_Events_Grid_Widget extends \Elementor\Widget_Base {

    public function get_name()       { return 'hipsy_events_grid'; }
    public function get_title()      { return 'Hipsy · Events Grid'; }
    public function get_icon()       { return 'eicon-gallery-grid'; }
    public function get_categories() { return [ 'general' ]; }
    public function get_keywords()   { return [ 'hipsy','events','grid','lijst','kalender','agenda','carrousel' ]; }

    protected function register_controls() {

        // ══════════════════════════════════════════
        // 1. LAYOUT (welk type grid)
        // ══════════════════════════════════════════
        $this->start_controls_section('sec_layout', [
            'label' => '🎨 Grid-type',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('layout', [
            'label'   => 'Kies layout',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'grid'      => '⊞  Grid — kaarten in kolommen',
                'lijst'     => '☰  Lijst — horizontale rijen',
                'carrousel' => '⟷  Carrousel — horizontaal schuifbaar',
                'kalender'  => '📅  Maandkalender — echte agenda',
                'agenda'    => '📋  Agenda — gegroepeerd per maand',
            ],
            'default' => 'grid',
        ]);

        $this->add_responsive_control('kolommen', [
            'label'     => 'Kolommen',
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => ['1'=>'1','2'=>'2','3'=>'3','4'=>'4'],
            'default'   => '3','tablet_default'=>'2','mobile_default'=>'1',
            'condition' => ['layout' => ['grid','carrousel']],
        ]);

        $this->add_control('carrousel_pijlen', ['label'=>'Pijlen','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>['layout'=>'carrousel']]);
        $this->add_control('carrousel_dots',   ['label'=>'Dots',  'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>['layout'=>'carrousel']]);
        $this->add_control('carrousel_loop',   ['label'=>'Loop',  'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'no','condition'=>['layout'=>'carrousel']]);
        $this->add_control('carrousel_autoplay',['label'=>'Autoplay (sec, 0=uit)','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>0,'min'=>0,'max'=>10,'condition'=>['layout'=>'carrousel']]);
        $this->add_control('kalender_start',['label'=>'Startmaand','type'=>\Elementor\Controls_Manager::SELECT,'options'=>['huidig'=>'Huidige maand','volgende'=>'Volgende maand'],'default'=>'huidig','condition'=>['layout'=>'kalender']]);

        $this->end_controls_section();

        // ══════════════════════════════════════════
        // 2. KAARTOPBOUW (oriëntatie + volgorde)
        // ══════════════════════════════════════════
        $this->start_controls_section('sec_kaartopbouw', [
            'label' => '🃏 Kaartopbouw',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('_ori_uitleg', [
            'type'            => \Elementor\Controls_Manager::RAW_HTML,
            'raw'             => '<div style="background:#f5f0ff;border-radius:6px;padding:.6rem .8rem;font-size:.78rem;color:#6b21a8;line-height:1.5">
                <strong>Kaartoriëntatie</strong> bepaalt of de afbeelding boven, links, rechts of onder de tekst staat.
                Stel per device (desktop/tablet/mobiel) een andere oriëntatie in.<br><br>
                <strong>Volgorde</strong> van de tekstvelden: geef elk blok een getal (1 = bovenaan, hogere nummers komen lager).
                Twee blokken met hetzelfde getal volgen de HTML-volgorde.
            </div>',
            'content_classes' => 'elementor-panel-alert',
        ]);

        // Oriëntatie per device
        $this->add_responsive_control('kaart_orientatie', [
            'label'   => 'Kaartoriëntatie',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'column'         => '↓  Verticaal — afbeelding boven',
                'column-reverse' => '↑  Verticaal — afbeelding onder',
                'row'            => '→  Horizontaal — afbeelding links',
                'row-reverse'    => '←  Horizontaal — afbeelding rechts',
            ],
            'default'        => 'column',
            'tablet_default' => 'column',
            'mobile_default' => 'row',
            'selectors'      => ['{{WRAPPER}} .hew-card' => 'flex-direction: {{VALUE}} !important;'],
        ]);

        // Afbeeldingsbreedte (voor horizontale modus)
        $this->add_responsive_control('img_breedte_hor', [
            'label'      => 'Afbeeldingsbreedte (horizontaal)',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px','%'],
            'range'      => ['px'=>['min'=>60,'max'=>400],'%'=>['min'=>15,'max'=>60]],
            'default'    => ['unit'=>'px','size'=>140],
            'tablet_default' => ['unit'=>'px','size'=>160],
            'mobile_default' => ['unit'=>'px','size'=>110],
            'selectors'  => ['{{WRAPPER}} .hew-card-img' => 'width: {{SIZE}}{{UNIT}} !important; flex-shrink: 0 !important;'],
            'description'=> 'Alleen zichtbaar bij horizontale oriëntatie.',
        ]);

        $this->add_control('_volgorde_sep', [
            'label'     => 'Volgorde van blokken',
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control('_volgorde_info', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => '<div style="font-size:.75rem;color:#6b7280;line-height:1.5">Geef elk blok een volgorde-getal (1 t/m 10). Blokken met een lager getal verschijnen eerder in de kaart. Je hoeft alleen te wijzigen wat je anders wilt dan de standaard.</div>',
        ]);

        // Volgorde-controls per blok
        $volgorde_items = [
            'volgorde_datum'       => ['label'=>'📅 Datum',        'default'=>1],
            'volgorde_tijd'        => ['label'=>'🕐 Tijd',          'default'=>2],
            'volgorde_titel'       => ['label'=>'🔤 Titel',         'default'=>3],
            'volgorde_locatie'     => ['label'=>'📍 Locatie',       'default'=>4],
            'volgorde_beschrijving'=> ['label'=>'📝 Beschrijving',  'default'=>5],
            'volgorde_prijs'       => ['label'=>'💰 Prijs',         'default'=>6],
            'volgorde_knoppen'     => ['label'=>'🎟 Knoppen',       'default'=>7],
        ];

        foreach ($volgorde_items as $key => $item) {
            $css_class = str_replace(['volgorde_','_'],['hew-',''],$key);
            // Map key to actual CSS class
            $class_map = [
                'volgorde_datum'        => '.hew-datum',
                'volgorde_tijd'         => '.hew-tijd',
                'volgorde_titel'        => '.hew-titel',
                'volgorde_locatie'      => '.hew-locatie',
                'volgorde_beschrijving' => '.hew-desc',
                'volgorde_prijs'        => '.hew-prijs',
                'volgorde_knoppen'      => '.hew-card-actions',
            ];
            $this->add_control($key, [
                'label'     => $item['label'],
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => $item['default'],
                'min'       => 1, 'max' => 10,
                'selectors' => ['{{WRAPPER}} ' . $class_map[$key] => 'order: {{VALUE}};'],
            ]);
        }

        $this->end_controls_section();

        // ══════════════════════════════════════════
        // 3. QUERY & FILTERING
        // ══════════════════════════════════════════
        $this->start_controls_section('sec_query', [
            'label' => 'Events & Filtering',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        // ── Query ID (v4.0) ────────────────────────────────────────
        $this->add_control('_query_id_info', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => '<div style="background:#ecfdf5;border-radius:6px;padding:.7rem;font-size:.8rem;color:#065f46;line-height:1.5">
                <strong>🆕 Query ID (v4.0)</strong> — Gebruik dezelfde ID als je Filter Bar widget om ze te koppelen voor AJAX filtering.<br>
                <strong>Voorbeeld:</strong> Filter Bar → Query ID: <code>agenda</code> | Events Grid → Query ID: <code>agenda</code>
            </div>',
        ]);
        $this->add_control('query_id', [
            'label'       => 'Query ID (voor filter koppeling)',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => '',
            'placeholder' => 'bijv: agenda, homepage-events',
            'description' => 'Laat leeg als je geen filter gebruikt. Gebruik alleen letters, cijfers en streepjes.',
        ]);
        $this->add_control('_query_sep', ['type' => \Elementor\Controls_Manager::DIVIDER]);

        $this->add_control('aantal', ['label'=>'Max. events','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>6,'min'=>1,'max'=>100]);
        $this->add_control('alleen_toekomst', ['label'=>'Alleen aankomende events','type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);

        $cats = ['' => 'Alle categorieën'];
        foreach (get_terms(['taxonomy'=>'event_categorie','hide_empty'=>false]) ?: [] as $t) {
            $cats[$t->term_id] = $t->name;
        }
        $this->add_control('filter_categorie', ['label'=>'Toon categorie','type'=>\Elementor\Controls_Manager::SELECT,'options'=>$cats,'default'=>'']);

        // ── Meer Events opties ────────────────────────────────────────
        $this->add_control('_meer_events_sep', [
            'label'     => 'Meer events modus',
            'type'      => \Elementor\Controls_Manager::HEADING,
            'separator' => 'before',
        ]);
        $this->add_control('_meer_events_info', [
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'raw'  => '<div style="font-size:.75rem;color:#6b7280;line-height:1.5">Gebruik dit als <strong>gerelateerde events</strong> widget onder een event-pagina. Sluit het huidige event uit en voeg een organisatienaam en agenda-link toe.</div>',
        ]);
        $this->add_control('organisatie_naam', [
            'label'       => 'Naam organisatie',
            'type'        => \Elementor\Controls_Manager::TEXT,
            'default'     => '',
            'placeholder' => 'bv. De Bewuste Community',
            'description' => 'Getoond als koptekst: "Meer events van [naam]". Laat leeg om geen koptekst te tonen.',
        ]);
        $this->add_control('auto_exclude', [
            'label'        => 'Huidig event uitsluiten (dynamisch template)',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default'      => 'no',
            'description'  => 'Zet aan als je dit widget op een event-detailpagina plaatst.',
        ]);
        $this->add_control('huidig_event_id', [
            'label'     => 'Of kies een event om uit te sluiten',
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => hipsy_get_event_opties(),
            'default'   => '',
            'condition' => ['auto_exclude!' => 'yes'],
        ]);

        $this->end_controls_section();

        // ══════════════════════════════════════════
        // 3b. AGENDA-KNOP
        // ══════════════════════════════════════════
        $this->start_controls_section('sec_agenda_knop', [
            'label' => 'Agenda-knop',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('toon_agenda_knop', [
            'label'        => '"Bekijk alle events" tonen',
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on'     => 'Ja', 'label_off' => 'Nee',
            'return_value' => 'yes', 'default' => 'no',
        ]);
        $this->add_control('agenda_knop_tekst', [
            'label'     => 'Tekst knop',
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => '→ Bekijk alle events',
            'condition' => ['toon_agenda_knop' => 'yes'],
        ]);
        $this->add_control('agenda_knop_url', [
            'label'       => 'Link (URL) agenda-pagina',
            'type'        => \Elementor\Controls_Manager::URL,
            'placeholder' => 'https://jouwsite.nl/events',
            'default'     => ['url' => '', 'is_external' => false],
            'condition'   => ['toon_agenda_knop' => 'yes'],
        ]);
        $this->add_responsive_control('agenda_knop_uitlijning', [
            'label'     => 'Uitlijning',
            'type'      => \Elementor\Controls_Manager::CHOOSE,
            'options'   => [
                'flex-start' => ['title'=>'Links',  'icon'=>'eicon-text-align-left'],
                'center'     => ['title'=>'Midden', 'icon'=>'eicon-text-align-center'],
                'flex-end'   => ['title'=>'Rechts', 'icon'=>'eicon-text-align-right'],
            ],
            'default'   => 'flex-start',
            'selectors' => ['{{WRAPPER}} .hew-agenda-wrap' => 'justify-content: {{VALUE}};'],
            'condition' => ['toon_agenda_knop' => 'yes'],
        ]);

        $this->end_controls_section();

        // ══════════════════════════════════════════
        // 4. VELDEN AAN/UIT + INSTELLINGEN
        // ══════════════════════════════════════════
        $this->start_controls_section('sec_velden', [
            'label' => 'Velden aan/uit',
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('toon_afbeelding',   ['label'=>'Afbeelding',   'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);
        $this->add_control('toon_datum',        ['label'=>'Datum',        'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);
        $this->add_control('toon_tijd',         ['label'=>'Tijd',         'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);
        $this->add_control('toon_titel',        ['label'=>'Titel',        'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);
        $this->add_control('toon_locatie',      ['label'=>'Locatie',      'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);
        $this->add_control('toon_beschrijving', ['label'=>'Beschrijving', 'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>['layout!'=>'kalender']]);
        $this->add_control('toon_prijs',        ['label'=>'Prijs',        'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes']);
        $this->add_control('toon_knoppen',      ['label'=>'Knoppen',      'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>['layout!'=>'kalender']]);

        $this->add_control('_iconen_sep', ['label'=>'Iconen','type'=>\Elementor\Controls_Manager::HEADING,'separator'=>'before']);
        $this->add_control('icoon_datum',   ['label'=>'Icoon datum',   'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>['toon_datum'=>'yes']]);
        $this->add_control('icoon_tijd',    ['label'=>'Icoon tijd',    'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>['toon_tijd'=>'yes']]);
        $this->add_control('icoon_locatie', ['label'=>'Icoon locatie', 'type'=>\Elementor\Controls_Manager::SWITCHER,'return_value'=>'yes','default'=>'yes','condition'=>['toon_locatie'=>'yes']]);

        hipsy_register_datum_formaat_control($this);
        $this->add_control('max_woorden',  ['label'=>'Max. woorden beschrijving','type'=>\Elementor\Controls_Manager::NUMBER,'default'=>20,'min'=>0]);
        $this->add_control('knoptekst',    ['label'=>'Tekst ticketknop','type'=>\Elementor\Controls_Manager::TEXT,'default'=>'Bestel tickets']);

        $this->end_controls_section();

        // ══════════════════════════════════════════
        // STIJL: CONTAINER
        // ══════════════════════════════════════════
        $this->start_controls_section('sec_stijl_grid', ['label'=>'Container','tab'=>\Elementor\Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('grid_gap', ['label'=>'Tussenruimte','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>['px'],'range'=>['px'=>['min'=>0,'max'=>80]],'default'=>['unit'=>'px','size'=>24],'selectors'=>['{{WRAPPER}} .hew-grid'=>'gap:{{SIZE}}px !important;','{{WRAPPER}} .hew-lijst'=>'gap:{{SIZE}}px !important;']]);
        $this->add_control('pijl_kleur', ['label'=>'Pijlkleur carrousel','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#6b21c8','selectors'=>['{{WRAPPER}} .swiper-button-next,{{WRAPPER}} .swiper-button-prev'=>'--swiper-navigation-color:{{VALUE}};']]);
        $this->end_controls_section();

        // ══════════════════════════════════════════
        // STIJL: KAART
        // ══════════════════════════════════════════
        $this->start_controls_section('sec_stijl_card', ['label'=>'Kaart','tab'=>\Elementor\Controls_Manager::TAB_STYLE]);

        $this->add_control('card_bg', ['label'=>'Achtergrond','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>['{{WRAPPER}} .hew-card'=>'background-color:{{VALUE}} !important;']]);
        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), ['name'=>'card_rand','selector'=>'{{WRAPPER}} .hew-card','fields_options'=>['border'=>['default'=>'solid'],'width'=>['default'=>['top'=>'1','right'=>'1','bottom'=>'1','left'=>'1','unit'=>'px']],'color'=>['default'=>'#e5e7eb']]]);
        $this->add_responsive_control('card_radius', ['label'=>'Afgeronde hoeken','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>['px','%'],'default'=>['top'=>'12','right'=>'12','bottom'=>'12','left'=>'12','unit'=>'px'],'selectors'=>['{{WRAPPER}} .hew-card'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(), ['name'=>'card_schaduw','selector'=>'{{WRAPPER}} .hew-card']);
        $this->add_responsive_control('card_padding', ['label'=>'Padding tekstveld','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>['px','em'],'default'=>['top'=>'16','right'=>'18','bottom'=>'18','left'=>'18','unit'=>'px'],'selectors'=>['{{WRAPPER}} .hew-card-body'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);

        $this->add_responsive_control('card_outer_padding', [
            'label'      => 'Padding hele kaart',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px','em'],
            'default'    => ['top'=>'0','right'=>'0','bottom'=>'0','left'=>'0','unit'=>'px'],
            'selectors'  => ['{{WRAPPER}} .hew-card' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;'],
            'description'=> 'Ruimte rondom de gehele kaart, inclusief afbeelding.',
        ]);

        $this->end_controls_section();

        // ══════════════════════════════════════════
        // STIJL: AFBEELDING
        // ══════════════════════════════════════════
        $this->start_controls_section('sec_stijl_img', ['label'=>'Afbeelding','tab'=>\Elementor\Controls_Manager::TAB_STYLE,'condition'=>['toon_afbeelding'=>'yes']]);

        $this->add_control('img_verhouding', [
            'label'   => 'Beeldverhouding afbeelding',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => [
                '16/9'  => '16:9 — Landschap (breed)',
                '4/3'   => '4:3 — Standaard',
                '3/2'   => '3:2 — Foto',
                '1/1'   => '1:1 — Vierkant',
                '2/3'   => '2:3 — Portret',
                '3/4'   => '3:4 — Portret breed',
                'custom'=> 'Aangepaste hoogte (px)',
            ],
            'default'     => '4/3',
            'description' => 'De afbeelding schaalt automatisch mee met de kaartbreedte.',
            'selectors'   => ['{{WRAPPER}} .hew-card-img' => 'aspect-ratio: {{VALUE}};'],
        ]);

        $this->add_responsive_control('img_hoogte', [
            'label'      => 'Hoogte afbeelding (px)',
            'type'       => \Elementor\Controls_Manager::SLIDER,
            'size_units' => ['px','vh'],
            'range'      => ['px' => ['min'=>60,'max'=>600],'vh'=>['min'=>10,'max'=>80]],
            'default'    => ['unit'=>'px','size'=>200],
            'condition'  => ['img_verhouding' => 'custom'],
            'selectors'  => [
                '{{WRAPPER}} .hew-card-img'     => 'aspect-ratio: unset !important;',
                '{{WRAPPER}} .hew-card-img img' => 'height:{{SIZE}}{{UNIT}} !important; width:100% !important; object-fit:cover !important; display:block !important;',
            ],
            'description'=> 'Alleen actief bij "Aangepaste hoogte".',
        ]);

        $this->add_control('img_object_fit', [
            'label'     => 'Bijsnijden',
            'type'      => \Elementor\Controls_Manager::SELECT,
            'options'   => ['cover'=>'Bijsnijden (cover)','contain'=>'Volledig zichtbaar (contain)','fill'=>'Uitrekken (fill)'],
            'default'   => 'cover',
            'selectors' => ['{{WRAPPER}} .hew-card-img img' => 'object-fit:{{VALUE}} !important;'],
        ]);

        $this->add_responsive_control('img_positie_v', [
            'label'   => 'Verticale positie afbeelding',
            'type'    => \Elementor\Controls_Manager::CHOOSE,
            'options' => [
                'top'    => ['title' => 'Boven',  'icon' => 'eicon-v-align-top'],
                'center' => ['title' => 'Midden', 'icon' => 'eicon-v-align-middle'],
                'bottom' => ['title' => 'Onder',  'icon' => 'eicon-v-align-bottom'],
            ],
            'default'   => 'center',
            'selectors' => ['{{WRAPPER}} .hew-card-img img' => 'object-position: center {{VALUE}} !important;'],
            'description' => 'Bepaalt welk deel van de afbeelding zichtbaar is bij bijsnijden.',
        ]);

        $this->add_responsive_control('img_radius', [
            'label'      => 'Afgeronde hoeken afbeelding',
            'type'       => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => ['px','%'],
            'selectors'  => ['{{WRAPPER}} .hew-card-img img' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;'],
        ]);

        $this->add_control('img_vorm', [
            'label'       => 'Afbeeldingsvorm',
            'type'        => \Elementor\Controls_Manager::SELECT,
            'options'     => [
                'vierkant' => 'Vierkant',
                'rond'     => 'Rond (cirkel) — Hipsy-stijl',
            ],
            'default'     => 'vierkant',
            'description' => 'Rond = de Hipsy.nl lijststijl. Combineer met Horizontale oriëntatie en breedte ~70px.',
            'selectors_dictionary' => [
                'rond'     => '50%',
                'vierkant' => '0px',
            ],
            'selectors' => ['{{WRAPPER}} .hew-card-img img' => 'border-radius: {{VALUE}} !important;'],
        ]);

        $this->end_controls_section();

        // ══════════════════════════════════════════
        // STIJL: TEKST
        // ══════════════════════════════════════════
        $this->start_controls_section('sec_stijl_tekst', ['label'=>'Tekst','tab'=>\Elementor\Controls_Manager::TAB_STYLE]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), ['name'=>'datum_typ','label'=>'Datum typografie','selector'=>'{{WRAPPER}} .hew-datum']);
        $this->add_control('datum_kleur',   ['label'=>'Datumkleur',  'type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#6b21c8','selectors'=>['{{WRAPPER}} .hew-datum'   =>'color:{{VALUE}} !important;']]);
        $this->add_control('tijd_kleur',    ['label'=>'Tijdkleur',   'type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#6b7280','selectors'=>['{{WRAPPER}} .hew-tijd'    =>'color:{{VALUE}} !important;']]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), ['name'=>'titel_typ','label'=>'Titel typografie','selector'=>'{{WRAPPER}} .hew-titel']);
        $this->add_control('titel_kleur',   ['label'=>'Titelkleur',  'type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .hew-titel'   =>'color:{{VALUE}} !important;']]);
        $this->add_control('locatie_kleur', ['label'=>'Locatiekleur','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#6b7280','selectors'=>['{{WRAPPER}} .hew-locatie'=>'color:{{VALUE}} !important;']]);
        $this->add_control('desc_kleur',    ['label'=>'Beschrijving','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#4b5563','selectors'=>['{{WRAPPER}} .hew-desc'    =>'color:{{VALUE}} !important;']]);
        $this->add_control('prijs_kleur',   ['label'=>'Prijskleur',  'type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#059669','selectors'=>['{{WRAPPER}} .hew-prijs'   =>'color:{{VALUE}} !important;']]);
        $this->end_controls_section();

        // ══════════════════════════════════════════
        // STIJL: KNOPPEN
        // ══════════════════════════════════════════
        $this->start_controls_section('sec_stijl_knoppen', ['label'=>'Knoppen','tab'=>\Elementor\Controls_Manager::TAB_STYLE]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), ['name'=>'knop_typ','selector'=>'{{WRAPPER}} .hew-btn']);
        $this->add_responsive_control('knop_radius',  ['label'=>'Afgerond','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>['px'],'default'=>['unit'=>'px','size'=>8],'selectors'=>['{{WRAPPER}} .hew-btn'=>'border-radius:{{SIZE}}px !important;']]);
        $this->add_responsive_control('knop_padding', ['label'=>'Padding','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>['px'],'default'=>['top'=>'8','right'=>'16','bottom'=>'8','left'=>'16','unit'=>'px'],'selectors'=>['{{WRAPPER}} .hew-btn'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->add_control('info_bg',      ['label'=>'Meer info achtergrond','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#f3f4f6','selectors'=>['{{WRAPPER}} .hew-btn--info'=>'background-color:{{VALUE}} !important;']]);
        $this->add_control('info_kleur',   ['label'=>'Meer info tekst',      'type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#374151','selectors'=>['{{WRAPPER}} .hew-btn--info'=>'color:{{VALUE}} !important;']]);
        $this->add_control('ticket_bg',    ['label'=>'Tickets achtergrond',  'type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#6b21c8','selectors'=>['{{WRAPPER}} .hew-btn--ticket'=>'background-color:{{VALUE}} !important;']]);
        $this->add_control('ticket_kleur', ['label'=>'Tickets tekst',        'type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>['{{WRAPPER}} .hew-btn--ticket'=>'color:{{VALUE}} !important;']]);

        $this->add_control('_hover_heading', ['label'=>'Hover-kleuren','type'=>\Elementor\Controls_Manager::HEADING,'separator'=>'before']);
        $this->add_control('info_bg_hover',     ['label'=>'Meer info achtergrond (hover)','type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .hew-btn--info:hover'=>'background-color:{{VALUE}} !important;opacity:1 !important;']]);
        $this->add_control('info_kleur_hover',  ['label'=>'Meer info tekst (hover)',      'type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .hew-btn--info:hover'=>'color:{{VALUE}} !important;']]);
        $this->add_control('ticket_bg_hover',   ['label'=>'Tickets achtergrond (hover)',  'type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .hew-btn--ticket:hover'=>'background-color:{{VALUE}} !important;opacity:1 !important;']]);
        $this->add_control('ticket_kleur_hover',['label'=>'Tickets tekst (hover)',        'type'=>\Elementor\Controls_Manager::COLOR,'selectors'=>['{{WRAPPER}} .hew-btn--ticket:hover'=>'color:{{VALUE}} !important;']]);

        $this->end_controls_section();

        // ══════════════════════════════════════════
        // STIJL: KALENDER
        // ══════════════════════════════════════════
        $this->start_controls_section('sec_stijl_kal', ['label'=>'Kalender','tab'=>\Elementor\Controls_Manager::TAB_STYLE,'condition'=>['layout'=>'kalender']]);
        $this->add_control('kal_header_bg',    ['label'=>'Header achtergrond','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#6b21c8','selectors'=>['{{WRAPPER}} .hew-kal-header'=>'background-color:{{VALUE}} !important;']]);
        $this->add_control('kal_header_kleur', ['label'=>'Header tekstkleur', 'type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>['{{WRAPPER}} .hew-kal-header *'=>'color:{{VALUE}} !important;']]);
        $this->add_control('kal_dag_kleur',    ['label'=>'Dagkleur (namen)',  'type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#6b7280','selectors'=>['{{WRAPPER}} .hew-kal-dagnaam'=>'color:{{VALUE}} !important;']]);
        $this->add_control('kal_event_bg',     ['label'=>'Event-bolletje',    'type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#6b21c8','selectors'=>['{{WRAPPER}} .hew-kal-event'=>'background-color:{{VALUE}} !important;']]);
        $this->add_control('kal_event_kleur',  ['label'=>'Event-bolletje tekst','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>['{{WRAPPER}} .hew-kal-event'=>'color:{{VALUE}} !important;']]);
        $this->add_control('kal_vandaag_bg',   ['label'=>'"Vandaag" accent',  'type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#fef3c7','selectors'=>['{{WRAPPER}} .hew-kal-cel.is-vandaag .hew-kal-nr'=>'background-color:{{VALUE}} !important;']]);
        $this->end_controls_section();

        // ══════════════════════════════════════════
        // STIJL: AGENDA-KNOP
        // ══════════════════════════════════════════
        $this->start_controls_section('sec_stijl_agenda_knop', [
            'label'     => 'Agenda-knop',
            'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
            'condition' => ['toon_agenda_knop' => 'yes'],
        ]);
        $this->add_group_control(\Elementor\Group_Control_Typography::get_type(), ['name'=>'agenda_typ','selector'=>'{{WRAPPER}} .hew-agenda-btn']);
        $this->add_responsive_control('agenda_marge_top', ['label'=>'Ruimte boven knop','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>['px'],'default'=>['unit'=>'px','size'=>20],'selectors'=>['{{WRAPPER}} .hew-agenda-wrap'=>'margin-top:{{SIZE}}px;']]);
        $this->add_responsive_control('agenda_padding', ['label'=>'Padding','type'=>\Elementor\Controls_Manager::DIMENSIONS,'size_units'=>['px','em'],'default'=>['top'=>'10','right'=>'24','bottom'=>'10','left'=>'24','unit'=>'px'],'selectors'=>['{{WRAPPER}} .hew-agenda-btn'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;']]);
        $this->add_responsive_control('agenda_radius',  ['label'=>'Afgeronde hoeken','type'=>\Elementor\Controls_Manager::SLIDER,'size_units'=>['px'],'default'=>['unit'=>'px','size'=>6],'selectors'=>['{{WRAPPER}} .hew-agenda-btn'=>'border-radius:{{SIZE}}px !important;']]);

        $this->start_controls_tabs('tabs_agenda_knop');
        $this->start_controls_tab('tab_agenda_normaal', ['label'=>'Normaal']);
        $this->add_control('agenda_bg',    ['label'=>'Achtergrond','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'transparent','selectors'=>['{{WRAPPER}} .hew-agenda-btn'=>'background-color:{{VALUE}} !important;']]);
        $this->add_control('agenda_kleur', ['label'=>'Tekstkleur', 'type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#6b21c8','selectors'=>['{{WRAPPER}} .hew-agenda-btn'=>'color:{{VALUE}} !important;']]);
        $this->add_group_control(\Elementor\Group_Control_Border::get_type(), ['name'=>'agenda_rand','selector'=>'{{WRAPPER}} .hew-agenda-btn','fields_options'=>['border'=>['default'=>'solid'],'width'=>['default'=>['top'=>'2','right'=>'2','bottom'=>'2','left'=>'2','unit'=>'px']],'color'=>['default'=>'#6b21c8']]]);
        $this->end_controls_tab();
        $this->start_controls_tab('tab_agenda_hover', ['label'=>'Hover']);
        $this->add_control('agenda_bg_hover',    ['label'=>'Achtergrond','type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#6b21c8','selectors'=>['{{WRAPPER}} .hew-agenda-btn:hover'=>'background-color:{{VALUE}} !important;opacity:1 !important;']]);
        $this->add_control('agenda_kleur_hover', ['label'=>'Tekstkleur', 'type'=>\Elementor\Controls_Manager::COLOR,'default'=>'#ffffff','selectors'=>['{{WRAPPER}} .hew-agenda-btn:hover'=>'color:{{VALUE}} !important;']]);
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->end_controls_section();
    }

    // ══════════════════════════════════════════════════════
    // RENDER — dispatcher
    // ══════════════════════════════════════════════════════
    protected function render() {
        $s      = $this->get_settings_for_display();
        $layout = $s['layout'] ?? 'grid';
        $events = $this->get_events($s);

        // v4.0: Register Query ID voor filter koppeling
        $query_id = sanitize_key( $s['query_id'] ?? '' );
        if ( $query_id ) {
            hipsy_register_query( $query_id, [
                'posts_per_page'  => (int)($s['aantal'] ?? 6),
                'alleen_toekomst' => $s['alleen_toekomst'] ?? 'yes',
                'filter_categorie' => $s['filter_categorie'] ?? '',
                'auto_exclude'    => $s['auto_exclude'] ?? 'no',
                'huidig_event_id' => $s['huidig_event_id'] ?? '',
            ]);
        }

        $this->render_base_styles();

        // Wrapper ID voor filter targeting (v4.0)
        $wrapper_id = $query_id ? 'heg-' . $query_id : '';
        $wrapper_attrs = $wrapper_id ? ' id="' . esc_attr($wrapper_id) . '" data-query-id="' . esc_attr($query_id) . '" data-layout="' . esc_attr($layout) . '"' : '';

        // Organisatienaam koptekst (Meer Events modus)
        $this->render_organisatie_header($s);

        echo '<div' . $wrapper_attrs . '>';

        switch ($layout) {
            case 'lijst':     $this->render_lijst($s, $events);    break;
            case 'carrousel': $this->render_carrousel($s, $events); break;
            case 'kalender':  $this->render_kalender($s, $events);  break;
            case 'agenda':    $this->render_agenda($s, $events);    break;
            default:          $this->render_grid($s, $events);      break;
        }

        echo '</div>';

        // Agenda-knop onderaan (optioneel)
        $this->render_agenda_knop($s);
    }

    // ══════════════════════════════════════════════════════
    // DATA
    // ══════════════════════════════════════════════════════
    private function get_events($s) {
        $args = [
            'post_type'      => 'events',
            'posts_per_page' => (int)($s['aantal'] ?? 6),
            'post_status'    => 'publish',
            'meta_key'       => 'hipsy_events_date',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
        ];
        if (($s['alleen_toekomst'] ?? '') === 'yes') {
            $args['meta_query'] = [['key'=>'hipsy_events_date','value'=>date('Y-m-d\TH:i'),'compare'=>'>=','type'=>'DATETIME']];
        }
        if (!empty($s['filter_categorie'])) {
            $args['tax_query'] = [['taxonomy'=>'event_categorie','field'=>'term_id','terms'=>(int)$s['filter_categorie']]];
        }

        // Meer Events: uitsluiten van huidig/specifiek event
        $exclude = [];
        if (($s['auto_exclude'] ?? '') === 'yes' && get_the_ID()) {
            $exclude[] = get_the_ID();
        }
        if (!empty($s['huidig_event_id'])) {
            $exclude[] = (int)$s['huidig_event_id'];
        }
        if ($exclude) {
            $args['post__not_in'] = array_unique($exclude);
        }

        $query  = new WP_Query($args);
        $events = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $id       = get_the_ID();
                $datum    = get_post_meta($id,'hipsy_events_date',true);
                $einde    = get_post_meta($id,'hipsy_events_date_end',true);
                $tickets  = hipsy_get_tickets($id);
                $prijzen  = array_filter(array_column($tickets,'price'),fn($p)=>(float)$p>0);
                $events[] = [
                    'id'         => $id,
                    'titel'      => get_the_title(),
                    'permalink'  => get_permalink(),
                    'thumb'      => has_post_thumbnail() ? get_the_post_thumbnail($id,'medium') : '',
                    'datum_raw'  => $datum,
                    'einde_raw'  => $einde,
                    'datum_dt'   => hipsy_parse_datetime($datum),
                    'locatie'    => get_post_meta($id,'hipsy_events_location',true),
                    'ticket_url' => get_post_meta($id,'hipsy_events_link',true),
                    'content'    => get_post_field('post_content',$id),
                    'prijs_str'  => $prijzen ? 'Vanaf '.hipsy_format_prijs(min($prijzen)) : ($tickets ? 'Gratis' : ''),
                ];
            }
            wp_reset_postdata();
        }
        return $events;
    }

    // Toon organisatienaam als koptekst (voor Meer Events gebruik)
    private function render_organisatie_header($s) {
        if (!empty($s['organisatie_naam'])) {
            echo '<h3 class="hew-org-kop">Meer events van '.esc_html($s['organisatie_naam']).'</h3>';
        }
    }

    // Toon agenda-knop onderaan
    private function render_agenda_knop($s) {
        if (($s['toon_agenda_knop'] ?? '') !== 'yes') return;
        $url   = $s['agenda_knop_url']['url'] ?? '';
        $tekst = esc_html($s['agenda_knop_tekst'] ?? '→ Bekijk alle events');

        if (!$url) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="hew-agenda-wrap" style="display:flex;margin-top:1.25rem;"><span class="hew-agenda-btn" style="cursor:default;opacity:.6;">'.$tekst.' <em style="font-size:.75em">(stel URL in)</em></span></div>';
            }
            return;
        }

        $target = !empty($s['agenda_knop_url']['is_external']) ? ' target="_blank" rel="noopener noreferrer"' : '';
        echo '<div class="hew-agenda-wrap" style="display:flex;margin-top:1.25rem;">';
        echo '<a href="'.esc_url($url).'" class="hew-agenda-btn"'.$target.'>'.$tekst.'</a>';
        echo '</div>';
    }

    // ══════════════════════════════════════════════════════
    // KAART HTML — gedeeld door grid, carrousel en meer-events
    // ══════════════════════════════════════════════════════
    private function render_card($s, $e, $extra_class = '') {
        echo '<div class="hew-card' . ($extra_class ? ' '.$extra_class : '') . '">';

        // Afbeelding — direct kind van .hew-card (buiten .hew-card-body)
        if (($s['toon_afbeelding'] ?? '') === 'yes' && $e['thumb']) {
            echo '<div class="hew-card-img"><a href="'.esc_url($e['permalink']).'" tabindex="-1" aria-hidden="true">'.$e['thumb'].'</a></div>';
        }

        // Tekstvelden — in .hew-card-body als flex-column
        echo '<div class="hew-card-body">';

        if (($s['toon_datum'] ?? '') === 'yes' && $e['datum_raw']) {
            $d  = hipsy_format_datum($e['datum_raw'], $s['datum_formaat'] ?? 'volledig');
            $ic = ($s['icoon_datum'] ?? '') === 'yes' ? $this->svg_kalender() : '';
            echo '<div class="hew-datum">'.$ic.esc_html(strtoupper($d)).'</div>';
        }

        if (($s['toon_tijd'] ?? '') === 'yes') {
            $t  = hipsy_format_tijd($e['datum_raw'],$e['einde_raw']);
            if ($t) {
                $ic = ($s['icoon_tijd'] ?? '') === 'yes' ? $this->svg_klok() : '';
                echo '<div class="hew-tijd">'.$ic.esc_html($t).'</div>';
            }
        }

        if (($s['toon_titel'] ?? '') === 'yes') {
            echo '<h3 class="hew-titel"><a href="'.esc_url($e['permalink']).'" style="text-decoration:none;color:inherit;">'.esc_html($e['titel']).'</a></h3>';
        }

        if (($s['toon_locatie'] ?? '') === 'yes' && $e['locatie']) {
            $ic = ($s['icoon_locatie'] ?? '') === 'yes' ? $this->svg_pin() : '';
            echo '<div class="hew-locatie">'.$ic.esc_html($e['locatie']).'</div>';
        }

        if (($s['toon_beschrijving'] ?? '') === 'yes') {
            $max  = (int)($s['max_woorden'] ?? 20);
            $desc = $max > 0 ? wp_trim_words(wp_strip_all_tags($e['content']), $max) : wp_strip_all_tags($e['content']);
            if ($desc) echo '<p class="hew-desc">'.esc_html($desc).'</p>';
        }

        if (($s['toon_prijs'] ?? '') === 'yes' && $e['prijs_str']) {
            echo '<div class="hew-prijs">'.esc_html($e['prijs_str']).'</div>';
        }

        if (($s['toon_knoppen'] ?? '') === 'yes') {
            echo '<div class="hew-card-actions">';
            echo '<a href="'.esc_url($e['permalink']).'" class="hew-btn hew-btn--info">Meer info</a>';
            if ($e['ticket_url']) {
                echo '<a href="'.esc_url($e['ticket_url']).'" class="hew-btn hew-btn--ticket" target="_blank" rel="noopener">'.esc_html($s['knoptekst'] ?? 'Bestel tickets').'</a>';
            }
            echo '</div>';
        }

        echo '</div>'; // .hew-card-body
        echo '</div>'; // .hew-card
    }

    // ══════════════════════════════════════════════════════
    // LAYOUT 1: GRID
    // ══════════════════════════════════════════════════════
    private function render_grid($s, $events) {
        if (!$events) { echo '<p>Geen events gevonden.</p>'; return; }

        $cols_d = (int)($s['kolommen']        ?? 3);
        $cols_t = (int)($s['kolommen_tablet'] ?? 2);
        $cols_m = (int)($s['kolommen_mobile'] ?? 1);

        echo '<div class="hew-grid" style="--cols-d:'.$cols_d.';--cols-t:'.$cols_t.';--cols-m:'.$cols_m.';">';
        foreach ($events as $e) {
            echo '<div>';
            $this->render_card($s, $e);
            echo '</div>';
        }
        echo '</div>';
    }

    // ══════════════════════════════════════════════════════
    // LAYOUT 2: LIJST (Amelia-stijl, aparte HTML)
    // ══════════════════════════════════════════════════════
    private function render_lijst($s, $events) {
        if (!$events) { echo '<p>Geen events gevonden.</p>'; return; }

        echo '<div class="hew-lijst">';
        foreach ($events as $e) {
            echo '<div class="hew-card hew-lijst-rij">';

            // Datum blok links
            if (($s['toon_datum'] ?? '') === 'yes' && $e['datum_dt']) {
                $dag = $e['datum_dt']->format('j');
                $mnd = strtoupper($this->maand_kort((int)$e['datum_dt']->format('n')));
                echo '<div class="hew-lijst-datum-blok"><span class="hew-lijst-dag">'.esc_html($dag).'</span><span class="hew-lijst-mnd">'.esc_html($mnd).'</span></div>';
            }

            if (($s['toon_afbeelding'] ?? '') === 'yes' && $e['thumb']) {
                echo '<div class="hew-lijst-img"><a href="'.esc_url($e['permalink']).'">'.$e['thumb'].'</a></div>';
            }

            echo '<div class="hew-lijst-info">';
            if (($s['toon_titel'] ?? '') === 'yes') {
                echo '<h3 class="hew-titel"><a href="'.esc_url($e['permalink']).'" style="text-decoration:none;color:inherit;">'.esc_html($e['titel']).'</a></h3>';
            }
            echo '<div class="hew-lijst-meta">';
            if (($s['toon_tijd'] ?? '') === 'yes') {
                $t = hipsy_format_tijd($e['datum_raw'],$e['einde_raw']);
                if ($t) echo '<span class="hew-meta-item">'.$this->svg_klok().esc_html($t).'</span>';
            }
            if (($s['toon_locatie'] ?? '') === 'yes' && $e['locatie']) {
                echo '<span class="hew-meta-item hew-locatie">'.$this->svg_pin().esc_html($e['locatie']).'</span>';
            }
            if (($s['toon_prijs'] ?? '') === 'yes' && $e['prijs_str']) {
                echo '<span class="hew-meta-item hew-prijs">'.esc_html($e['prijs_str']).'</span>';
            }
            echo '</div>';
            if (($s['toon_beschrijving'] ?? '') === 'yes') {
                $max = (int)($s['max_woorden'] ?? 20);
                $desc = $max > 0 ? wp_trim_words(wp_strip_all_tags($e['content']), $max) : wp_strip_all_tags($e['content']);
                if ($desc) echo '<p class="hew-desc">'.esc_html($desc).'</p>';
            }
            echo '</div>';

            if (($s['toon_knoppen'] ?? '') === 'yes') {
                echo '<div class="hew-lijst-acties">';
                echo '<a href="'.esc_url($e['permalink']).'" class="hew-btn hew-btn--info">Meer info</a>';
                if ($e['ticket_url']) echo '<a href="'.esc_url($e['ticket_url']).'" class="hew-btn hew-btn--ticket" target="_blank" rel="noopener">'.esc_html($s['knoptekst'] ?? 'Bestel tickets').'</a>';
                echo '</div>';
            }

            echo '</div>';
        }
        echo '</div>';
    }

    // ══════════════════════════════════════════════════════
    // LAYOUT 3: CARROUSEL
    // ══════════════════════════════════════════════════════
    private function render_carrousel($s, $events) {
        if (!$events) { echo '<p>Geen events gevonden.</p>'; return; }
        hipsy_ew_enqueue_swiper();

        $uid  = 'hew-sw-'.$this->get_id();
        $pvd  = (int)($s['kolommen']        ?? 3);
        $pvt  = (int)($s['kolommen_tablet'] ?? 2);
        $pvm  = (int)($s['kolommen_mobile'] ?? 1);
        $pijl = ($s['carrousel_pijlen'] ?? '') === 'yes';
        $dots = ($s['carrousel_dots']   ?? '') === 'yes';
        $loop = ($s['carrousel_loop']   ?? '') === 'yes' ? 'true' : 'false';
        $auto = (int)($s['carrousel_autoplay'] ?? 0);

        echo '<div class="swiper '.esc_attr($uid).'">';
        echo '<div class="swiper-wrapper hew-grid hew-carrousel">';
        foreach ($events as $e) {
            echo '<div class="swiper-slide">';
            $this->render_card($s, $e);
            echo '</div>';
        }
        echo '</div>';
        if ($pijl) echo '<div class="swiper-button-prev"></div><div class="swiper-button-next"></div>';
        if ($dots) echo '<div class="swiper-pagination"></div>';
        echo '</div>';

        $auto_cfg = $auto > 0 ? "{delay:{$auto}000,disableOnInteraction:false}" : 'false';
        $nav = $pijl ? "{nextEl:'.{$uid} .swiper-button-next',prevEl:'.{$uid} .swiper-button-prev'}" : 'false';
        $pag = $dots ? "{el:'.{$uid} .swiper-pagination',clickable:true}" : 'false';
        echo "<script>document.addEventListener('DOMContentLoaded',function(){new Swiper('.{$uid}',{slidesPerView:{$pvm},spaceBetween:16,loop:{$loop},autoplay:{$auto_cfg},navigation:{$nav},pagination:{$pag},breakpoints:{768:{slidesPerView:{$pvt},spaceBetween:20},1024:{slidesPerView:{$pvd},spaceBetween:24}}});});</script>";
    }

    // ══════════════════════════════════════════════════════
    // LAYOUT 4: MAANDKALENDER
    // ══════════════════════════════════════════════════════
    private function render_kalender($s, $events) {
        $uid = 'hew-kal-'.$this->get_id();
        $per_dag = [];
        foreach ($events as $e) {
            if ($e['datum_dt']) $per_dag[$e['datum_dt']->format('Y-m-d')][] = $e;
        }

        $start_ts    = ($s['kalender_start'] ?? 'huidig') === 'volgende' ? mktime(0,0,0,date('n')+1,1) : mktime(0,0,0,date('n'),1);
        $max_maanden = 3;
        if ($events) {
            $last = end($events);
            if ($last['datum_dt']) {
                $diff = (int)(($last['datum_dt']->getTimestamp()-$start_ts)/(30*86400))+1;
                $max_maanden = min(max($diff,1),6);
            }
        }
        $vandaag = date('Y-m-d');

        echo '<div class="hew-kalender-wrapper" id="'.esc_attr($uid).'">';
        for ($m=0;$m<$max_maanden;$m++) {
            $ts = mktime(0,0,0,date('n',$start_ts)+$m,1,date('Y',$start_ts));
            $jaar=$t=(int)date('Y',$ts); $maand=(int)date('n',$ts); $dagen=(int)date('t',$ts); $start=(int)date('N',$ts);

            echo '<div class="hew-kalender"><div class="hew-kal-header"><span class="hew-kal-maand-naam">'.ucfirst($this->maand_lang($maand)).' '.date('Y',$ts).'</span></div>';
            echo '<div class="hew-kal-dagnamen">';
            foreach(['Ma','Di','Wo','Do','Vr','Za','Zo'] as $dn) echo '<div class="hew-kal-dagnaam">'.esc_html($dn).'</div>';
            echo '</div><div class="hew-kal-cellen">';
            for($v=1;$v<$start;$v++) echo '<div class="hew-kal-cel is-leeg"></div>';
            for($dag=1;$dag<=$dagen;$dag++) {
                $key=sprintf('%04d-%02d-%02d',$jaar,$maand,$dag);
                $ev=$per_dag[$key]??[];
                $cls='hew-kal-cel'.($key===$vandaag?' is-vandaag':'').($ev?' heeft-events':'');
                echo '<div class="'.esc_attr($cls).'"><span class="hew-kal-nr">'.esc_html($dag).'</span>';
                foreach(array_slice($ev,0,2) as $e) echo '<a href="'.esc_url($e['permalink']).'" class="hew-kal-event" title="'.esc_attr($e['titel']).'">'.esc_html(mb_strimwidth($e['titel'],0,18,'…')).'</a>';
                if(count($ev)>2) echo '<span class="hew-kal-meer">+'.(count($ev)-2).'</span>';
                echo '</div>';
            }
            echo '</div></div>';
        }
        echo '</div>';
        $this->render_kalender_styles();
    }

    // ══════════════════════════════════════════════════════
    // LAYOUT 5: AGENDA (gegroepeerd per maand)
    // ══════════════════════════════════════════════════════
    private function render_agenda($s, $events) {
        if (!$events) { echo '<p>Geen events gevonden.</p>'; return; }

        $groepen = [];
        foreach ($events as $e) {
            $key = $e['datum_dt'] ? $e['datum_dt']->format('Y-m') : '0000-00';
            $groepen[$key][] = $e;
        }

        echo '<div class="hew-agenda">';
        foreach ($groepen as $key => $maand_events) {
            if ($key==='0000-00') continue;
            [$jaar,$mnd] = explode('-',$key);
            echo '<div class="hew-agenda-groep">';
            echo '<div class="hew-agenda-mnd-header"><span class="hew-agenda-mnd-label">'.esc_html(ucfirst($this->maand_lang((int)$mnd)).' '.$jaar).'</span></div>';
            foreach ($maand_events as $e) {
                echo '<div class="hew-card hew-agenda-rij">';
                if (($s['toon_datum'] ?? '') === 'yes' && $e['datum_dt']) {
                    echo '<div class="hew-agenda-datum"><span class="hew-agenda-dagnr">'.esc_html($e['datum_dt']->format('j')).'</span><span class="hew-agenda-dagnaam">'.esc_html(strtoupper($this->dag_kort($e['datum_dt']->format('l')))).'</span></div>';
                }
                if (($s['toon_afbeelding'] ?? '') === 'yes' && $e['thumb']) {
                    echo '<div class="hew-agenda-img"><a href="'.esc_url($e['permalink']).'">'.$e['thumb'].'</a></div>';
                }
                echo '<div class="hew-card-body">';
                if (($s['toon_titel'] ?? '') === 'yes') echo '<h3 class="hew-titel"><a href="'.esc_url($e['permalink']).'" style="text-decoration:none;color:inherit;">'.esc_html($e['titel']).'</a></h3>';
                echo '<div class="hew-meta-rij">';
                if (($s['toon_tijd'] ?? '') === 'yes') { $t=hipsy_format_tijd($e['datum_raw'],$e['einde_raw']); if($t) echo '<span class="hew-meta-item">'.$this->svg_klok().esc_html($t).'</span>'; }
                if (($s['toon_locatie'] ?? '') === 'yes' && $e['locatie']) echo '<span class="hew-meta-item hew-locatie">'.$this->svg_pin().esc_html($e['locatie']).'</span>';
                if (($s['toon_prijs'] ?? '') === 'yes' && $e['prijs_str']) echo '<span class="hew-meta-item hew-prijs">'.esc_html($e['prijs_str']).'</span>';
                echo '</div>';
                if (($s['toon_beschrijving'] ?? '') === 'yes') { $max=(int)($s['max_woorden']??20); $desc=$max>0?wp_trim_words(wp_strip_all_tags($e['content']),$max):wp_strip_all_tags($e['content']); if($desc) echo '<p class="hew-desc">'.esc_html($desc).'</p>'; }
                if (($s['toon_knoppen'] ?? '') === 'yes') {
                    echo '<div class="hew-card-actions" style="margin-top:.75rem;">';
                    echo '<a href="'.esc_url($e['permalink']).'" class="hew-btn hew-btn--info">Meer info</a>';
                    if ($e['ticket_url']) echo '<a href="'.esc_url($e['ticket_url']).'" class="hew-btn hew-btn--ticket" target="_blank" rel="noopener">'.esc_html($s['knoptekst']??'Bestel tickets').'</a>';
                    echo '</div>';
                }
                echo '</div></div>';
            }
            echo '</div>';
        }
        echo '</div>';
    }

    // ══════════════════════════════════════════════════════
    // SVG HELPERS
    // ══════════════════════════════════════════════════════
    private function svg_kalender() { return '<svg style="display:inline;vertical-align:middle;flex-shrink:0;margin-right:3px" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'; }
    private function svg_klok()    { return '<svg style="display:inline;vertical-align:middle;flex-shrink:0;margin-right:3px" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>'; }
    private function svg_pin()     { return '<svg style="display:inline;vertical-align:middle;flex-shrink:0;margin-right:3px" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>'; }

    // ══════════════════════════════════════════════════════
    // DATUM HELPERS
    // ══════════════════════════════════════════════════════
    private function maand_lang($n) { return ['','januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december'][$n]??''; }
    private function maand_kort($n) { return ['','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec'][$n]??''; }
    private function dag_kort($e)   { return ['Monday'=>'ma','Tuesday'=>'di','Wednesday'=>'wo','Thursday'=>'do','Friday'=>'vr','Saturday'=>'za','Sunday'=>'zo'][$e]??''; }

    // ══════════════════════════════════════════════════════
    // CSS — alleen structureel, geen visuele waarden
    // ══════════════════════════════════════════════════════
    private function render_base_styles() {
        static $p=false; if($p) return; $p=true;
        echo '<style>
        /* Structurele CSS — visuele waarden komen uit Elementor controls */

        /* Kaart: flex-direction wordt bepaald door Elementor (kaart_orientatie control) */
        .hew-card{overflow:hidden;display:flex;height:100%;transition:box-shadow .2s,transform .2s;flex-direction:column}
        .hew-card:hover{box-shadow:0 6px 24px rgba(0,0,0,.10) !important}
        /* Afbeelding: vult altijd netjes de container via aspect-ratio */
        .hew-card-img{flex-shrink:0;overflow:hidden;aspect-ratio:4/3}
        .hew-card-img img{width:100%;height:100%;display:block;object-fit:cover;object-position:center center}
        /* In horizontale modus (row/row-reverse): afbeelding vult hoogte van kaart */
        .hew-card[style*="row"] .hew-card-img{align-self:stretch}
        .hew-card[style*="row"] .hew-card-img img{height:100% !important;min-height:100px}
        /* Card body: flex-column zodat volgorde (CSS order) werkt */
        .hew-card-body{display:flex;flex-direction:column;flex:1;min-width:0}
        /* Tekstvelden */
        .hew-datum{display:flex;align-items:center;margin-bottom:.2rem;font-weight:700;letter-spacing:.06em;font-size:.72rem}
        .hew-tijd{display:flex;align-items:center;margin-bottom:.35rem;font-size:.8rem}
        .hew-titel{margin:0 0 .45rem;line-height:1.3;font-size:1rem;font-weight:700}
        .hew-locatie{display:flex;align-items:center;margin-bottom:.35rem;font-size:.82rem}
        .hew-desc{line-height:1.5;margin-bottom:.5rem;flex:1;font-size:.84rem}
        .hew-prijs{font-weight:600;margin-bottom:.4rem;font-size:.82rem}
        .hew-card-actions{display:flex;gap:.5rem;flex-wrap:wrap;margin-top:auto;padding-top:.7rem}
        .hew-btn{display:inline-block;font-weight:600;text-decoration:none;transition:opacity .15s,background-color .15s;font-size:.82rem;cursor:pointer}
        .hew-btn:hover{opacity:.85}
        /* Grid */
        .hew-grid{display:grid;grid-template-columns:repeat(var(--cols-d,3),1fr)}
        @media(max-width:1024px){.hew-grid{grid-template-columns:repeat(var(--cols-t,2),1fr)}}
        @media(max-width:767px){.hew-grid{grid-template-columns:repeat(var(--cols-m,1),1fr)}}
        .hew-carrousel{display:flex!important}
        
        /* Lijst - UPGRADED VOOR MANDIRA STYLE */
        .hew-lijst{display:flex;flex-direction:column;gap:1.25rem}
        .hew-lijst-rij{
            flex-direction:row!important;
            align-items:stretch;
            gap:1.25rem;
            padding:1.25rem;
            border-radius:12px;
            box-shadow:0 1px 3px rgba(0,0,0,0.08);
            border-left:4px solid currentColor;
            transition:all 0.2s ease;
        }
        .hew-lijst-rij:hover{
            box-shadow:0 4px 12px rgba(0,0,0,0.12);
            transform:translateY(-2px);
        }
        
        /* Datum blok - verborgen in lijst (te veel ruimte) */
        .hew-lijst-datum-blok{display:none}
        
        /* Afbeelding - mooiere sizing */
        .hew-lijst-img{
            width:200px;
            flex-shrink:0;
            border-radius:8px;
            overflow:hidden;
        }
        .hew-lijst-img img{
            width:100%;
            height:180px;
            object-fit:cover;
            display:block;
        }
        
        /* Content area */
        .hew-lijst-info{
            flex:1;
            display:flex;
            flex-direction:column;
            gap:0.75rem;
            min-width:0; /* Fix voor text overflow */
        }
        
        /* Datum line bovenaan (zoals Mandira) */
        .hew-lijst-info .hew-datum{
            font-size:0.75rem;
            font-weight:700;
            letter-spacing:0.05em;
            margin-bottom:0;
            color:currentColor;
        }
        
        /* Titel */
        .hew-lijst-info .hew-titel{
            margin:0;
            font-size:1.25rem;
            line-height:1.3;
        }
        
        /* Meta items (tijd, locatie) */
        .hew-lijst-meta{
            display:flex;
            flex-wrap:wrap;
            gap:0.5rem 1rem;
            margin:0;
            font-size:0.875rem;
        }
        .hew-meta-item{
            display:inline-flex;
            align-items:center;
            gap:6px;
        }
        
        /* Beschrijving */
        .hew-lijst-info .hew-desc{
            margin:0;
            line-height:1.6;
            font-size:0.9rem;
            color:#666;
        }
        
        /* Buttons area */
        .hew-lijst-acties{
            display:flex;
            gap:0.75rem;
            align-items:center;
            flex-shrink:0;
            padding:0;
        }
        .hew-lijst-acties .hew-btn{
            padding:0.625rem 1.25rem;
            border-radius:6px;
            font-size:0.875rem;
            font-weight:600;
            text-align:center;
            white-space:nowrap;
        }
        .hew-lijst-acties .hew-btn--info{
            background:transparent;
            border:2px solid currentColor;
            color:currentColor;
        }
        .hew-lijst-acties .hew-btn--ticket{
            background:currentColor;
            color:#fff;
            border:2px solid currentColor;
        }
        
        /* Prijs - prominent rechtsboven */
        .hew-lijst-info .hew-prijs{
            position:absolute;
            top:1.25rem;
            right:1.25rem;
            font-size:1.125rem;
            font-weight:700;
            margin:0;
            padding:0.5rem 0.875rem;
            background:#10b981;
            color:#fff;
            border-radius:6px;
        }
        .hew-lijst-rij{position:relative} /* Nodig voor absolute prijs */
        
        /* MOBIEL - BETERE RESPONSIVE */
        @media(max-width:768px){
            .hew-lijst-rij{
                flex-direction:column;
                gap:1rem;
                padding:1rem;
            }
            .hew-lijst-img{
                width:100%;
            }
            .hew-lijst-img img{
                height:200px;
            }
            .hew-lijst-acties{
                flex-direction:row;
                justify-content:flex-start;
            }
            .hew-lijst-acties .hew-btn{
                flex:1;
            }
            .hew-lijst-info .hew-prijs{
                position:static;
                display:inline-block;
                margin-top:0.5rem;
            }
        }
        
        /* Oudere fallback */
        .hew-meta-rij{display:flex;flex-wrap:wrap;gap:.5rem .9rem;margin:.3rem 0 .5rem}
        /* Agenda */
        .hew-agenda{display:flex;flex-direction:column;gap:2rem}
        .hew-agenda-groep{}
        .hew-agenda-mnd-header{display:flex;align-items:center;gap:1rem;margin-bottom:.75rem}
        .hew-agenda-mnd-header::after{content:"";flex:1;height:1px;background:#e5e7eb}
        .hew-agenda-mnd-label{font-size:1rem;font-weight:700;white-space:nowrap}
        .hew-agenda-rij{flex-direction:row!important;margin-bottom:.75rem;gap:0}
        .hew-agenda-datum{display:flex;flex-direction:column;align-items:center;justify-content:center;min-width:56px;padding:.75rem .5rem;border-right:1px solid #e5e7eb;flex-shrink:0}
        .hew-agenda-dagnr{font-size:1.5rem;font-weight:800;line-height:1}
        .hew-agenda-dagnaam{font-size:.65rem;font-weight:700;letter-spacing:.08em}
        .hew-agenda-img{width:110px;flex-shrink:0}
        .hew-agenda-img img{width:100%;height:100%;object-fit:cover;display:block}
        /* Org koptekst + agenda-knop */
        .hew-org-kop{margin-bottom:1rem;font-size:1.1rem;font-weight:700}
        .hew-agenda-wrap{display:flex}
        .hew-agenda-btn{display:inline-block;font-weight:600;text-decoration:none;cursor:pointer;transition:background-color .15s,color .15s,opacity .15s}
        /* Emoji-fix in alle kaart-elementen */
        .hew-card img.emoji,.hew-datum img.emoji,.hew-titel img.emoji,.hew-desc img.emoji,.hew-locatie img.emoji,.hew-card-body img.emoji{height:1em!important;width:1em!important;max-width:1em!important;max-height:1em!important;vertical-align:-0.1em!important;display:inline!important;margin:0 0.05em!important;padding:0!important;box-shadow:none!important;border:none!important;background:none!important;border-radius:0!important;float:none!important;}
        </style>';
    }

    private function render_kalender_styles() {
        static $p=false; if($p) return; $p=true;
        echo '<style>
        .hew-kalender-wrapper{display:flex;flex-direction:column;gap:2rem}
        .hew-kalender{border:1px solid #e5e7eb;border-radius:12px;overflow:hidden}
        .hew-kal-header{padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between}
        .hew-kal-maand-naam{font-size:1.05rem;font-weight:700;letter-spacing:.02em}
        .hew-kal-dagnamen{display:grid;grid-template-columns:repeat(7,1fr);background:#f9fafb;border-bottom:1px solid #e5e7eb}
        .hew-kal-dagnaam{text-align:center;font-size:.72rem;font-weight:600;padding:.6rem 0;letter-spacing:.06em}
        .hew-kal-cellen{display:grid;grid-template-columns:repeat(7,1fr)}
        .hew-kal-cel{min-height:80px;border-bottom:1px solid #f3f4f6;border-right:1px solid #f3f4f6;padding:.4rem .35rem;display:flex;flex-direction:column;gap:2px}
        .hew-kal-cel:nth-child(7n){border-right:none}
        .hew-kal-cel.is-leeg{background:#fafafa}
        .hew-kal-cel.heeft-events{background:#fdf8ff}
        .hew-kal-nr{font-size:.82rem;font-weight:600;width:24px;height:24px;display:flex;align-items:center;justify-content:center;border-radius:50%;margin-bottom:2px}
        .hew-kal-event{display:block;font-size:.68rem;font-weight:500;border-radius:3px;padding:2px 5px;text-decoration:none;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;line-height:1.4;margin-top:1px}
        .hew-kal-event:hover{opacity:.85}
        .hew-kal-meer{font-size:.68rem;font-weight:600;padding:1px 4px}
        @media(max-width:540px){
            .hew-kal-cel{min-height:52px;padding:.25rem}
            .hew-kal-event{display:none}
            .hew-kal-cel.heeft-events .hew-kal-nr::after{content:"•";font-size:.6rem;display:block;text-align:center;line-height:0;margin-top:2px}
        }
        </style>';
    }
}
