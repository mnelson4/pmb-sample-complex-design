// once doc conversion requested, process the HTML and trigger when we're ready.
jQuery(document).on('pmb_doc_conversion_requested', function(){
    pmb_doc_conversion_request_handled = true;
    pmb_default_align_center();
    pmb_replace_internal_links_with_page_refs_and_footnotes('footnote', 'footnote', pmb_design_options['external_footnote_text'], pmb_design_options['internal_footnote_text']);
    new PmbToc();
    jQuery(document).on('pmb_done_processing_videos', function() {
        pmb_resize_images(400);
        jQuery(document).trigger('pmb_doc_conversion_ready');
    });
    pmb_convert_youtube_videos_to_images();
});

