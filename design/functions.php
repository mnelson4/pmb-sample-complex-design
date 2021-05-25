<?php
// Add filters, action callback, and functions you want to use in your design.
// Note that this file only gets included when generating a new project, not on every pageload.
add_action(
	'pmb_pdf_generation_start',
	function(\PrintMyBlog\entities\ProjectGeneration $project_generation, \PrintMyBlog\orm\entities\Design $design){
        global $pmb_design;
        $pmb_design = $design;
        add_action('wp_enqueue_scripts', 'pmb_enqueue_easy_script', 1001);
	},
	10,
	2
);

function pmb_enqueue_easy_script(){
    global $pmb_design;
    $css = pmb_design_styles($pmb_design);

    // dynamic CSS can go here
    wp_add_inline_style(
        'pmb_print_common',
        $css
    );

    // Pass some data to the Javascript
    wp_localize_script(
        'pmb-design',
        'pmb_design_options',
        [
            'internal_footnote_text' => $pmb_design->getSetting('internal_footnote_text'),
            'external_footnote_text' => $pmb_design->getSetting('footnote_text')
        ]
    );
}