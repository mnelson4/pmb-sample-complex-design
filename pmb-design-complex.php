<?php
/**
 * @package Hello_Dolly
 * @version 1.7.2
 */
/*
Plugin Name: Print My Blog - Sample Complex Design
Plugin URI: https://github.com/mnelson4/pmb-sample-complex-design
Description: A more complex sample design that supports dividing projects into parts, "volumes" and "anthologies", as well
as front and back matter. Adds a new section template of "Sideways Title" to this design, and to the Classic Print-Ready PDF too.
Author: Mike Nelson
Version: 1.0.0
Author URI:
*/

use PrintMyBlog\entities\DesignTemplate;
use PrintMyBlog\orm\entities\Design;
use Twine\forms\base\FormSection;
use Twine\forms\inputs\AdminFileUploaderInput;
use Twine\forms\inputs\ColorInput;
use Twine\forms\inputs\DatepickerInput;
use Twine\forms\inputs\TextAreaInput;
use Twine\forms\inputs\TextInput;
use Twine\forms\strategies\validation\TextValidation;

define('PMBC_MAIN_FILE', __FILE__);
define('PMBC_MAIN_DIR', __DIR__);
add_action( 'pmb_register_designs', 'pmbc_register_design', 1 );
register_activation_hook(PMBC_MAIN_FILE,'pmbc_activation');

/**
 * Called when this plugin is activated, and gets PMB to check this design exists in the database (and if not, adds it)
 */
function pmbc_activation(){
    pmbc_register_design();
    pmb_check_db();
}

/**
 * Registers the design and design template. This should be done on every request so PMB knows they exist.
 */
function pmbc_register_design() {
    if(! function_exists('pmb_register_design')){
        return;
    }
    pmb_register_design_template(
        'complex_example',
        function () {
            return [
                'title' => __('Complex Print PDF'),
                'format' => 'print_pdf',
                'dir' => PMBC_MAIN_DIR . '/design/',
                'default' => 'complex',
                'url' => plugins_url('design/', PMBC_MAIN_FILE),
                'docs' => '',
                'supports' => [
                    'front_matter',
                    'back_matter',
                    'part',
                    'volume',
                    'anthology'
                ],
                'custom_templates' => [
                    'just_content',
                    'center_content'
                ],
                'design_form_callback' => function () {
                    return (new FormSection([
                        'subsections' =>
                            [
                                'internal_footnote_text' => new TextInput([
                                    'html_label_text' => __('Internal Footnote Text', 'print-my-blog'),
                                    'html_help_text' => __('Text to use when replacing a hyperlink with a footnote. "%s" will be replaced with the page number.', 'print-my-blog'),
                                    'default' => __('See page %s.', 'print-my-blog'),
                                    'validation_strategies' => [
                                        new TextValidation(__('You must include "%s" in the footnote text so we know where to put the URL.', 'print-my-blog'), '~.*\%s.*~')
                                    ]
                                ]),
                                'footnote_text' => new TextInput([
                                    'html_label_text' => __('External Footnote Text', 'print-my-blog'),
                                    'html_help_text' => __('Text to use when replacing a hyperlink with a footnote. "%s" will be replaced with the URL', 'print-my-blog'),
                                    'default' => __('See %s.', 'print-my-blog'),
                                    'validation_strategies' => [
                                        new TextValidation(__('You must include "%s" in the footnote text so we know where to put the URL.', 'print-my-blog'), '~.*\%s.*~')
                                    ]
                                ])
                            ],
                    ]))->merge(pmb_generic_design_form());
                },
                'project_form_callback' => function (Design $design) {
                    return new FormSection([
                        'subsections' => [
                            'institution' => new TextInput(
                                [
                                    'html_label_text' => __('Issue', 'print-my-blog'),
                                    'html_help_text' => __('Text that appears at the top-right of the cover'),
                                ]
                            ),
                            'authors' => new TextAreaInput(
                                [
                                    'html_label_text' => __('ByLine', 'print-my-blog'),
                                    'html_help_text' => __('Project Author(s)', 'print-my-blog'),
                                ]
                            ),
                            'date' => new DatepickerInput([
                                'html_label_text' => __('Date Issued', 'print-my-blog'),
                                'html_help_text' => __('Text that appears under the byline', 'print-my-blog'),
                            ]),
                        ]
                    ]);
                }
            ];
        }
    );
    pmb_register_design(
        'complex_example',
    'complex',
        function (DesignTemplate $design_template) {
            $preview_folder_url = plugins_url('/design/assets/', PMBC_MAIN_FILE);
            return [
                'title' => __('Complex Manual', 'print-my-blog'),
                'quick_description' => __('A complex example design', 'print-my-blog'),
                'description' => pmb_get_contents(PMBC_MAIN_DIR . '/design/description.php'),
                'author' => [
                    'name' => 'Mike Nelson',
                    'url' => 'https://printmy.blog'
                ],
                'previews' => [
                    [
                        'url' => $preview_folder_url . '/preview1.jpg',
                        'desc' => __('Title page', 'print-my-blog')
                    ],
                    [
                        'url' => $preview_folder_url . '/preview2.jpg',
                        'desc' => __('Main matter. Each article is part of a chapter, each "part" is labelled as a chapter, each "volume" looks like a part, and each "anthology" is a super part.', 'print-my-blog')
                    ]
                ],
                'design_defaults' => [
                    'custom_css' => ''
                ],
                'project_defaults' => [
                    'institution' => 'Print My Blog'
                ],
            ];
        }
    );
    pmb_register_section_template(
        'sideways',
        [
            'complex_example',
            'classic_print'
        ],
        function(){
            return [
                'title' => __('Sideways Title', 'print-my-blog'),
                'fallback' => '',
                'filepath' => PMBC_MAIN_DIR . '/design/templates/sideways.php'
            ];
        }
    );
}