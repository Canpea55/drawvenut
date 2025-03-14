import './bootstrap';
import * as bootstrap from 'bootstrap';

import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

import jQueryBridget from 'jquery-bridget';

import imagesLoaded from 'imagesloaded';
window.imagesLoaded = imagesLoaded;

import Masonry from 'masonry-layout';
jQueryBridget( 'masonry', Masonry, $ );

import 'tinymce/tinymce';
import 'tinymce/skins/ui/oxide/skin.min.css';
import 'tinymce/icons/default/icons';
import 'tinymce/themes/silver/theme';
import 'tinymce/models/dom/model';

import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/table';
import 'tinymce/plugins/code';
import 'tinymce/plugins/help';
import 'tinymce/plugins/wordcount';

tinymce.init({
    selector: 'textarea#tinymce', // Target the textarea with ID 'tinymce'
    skin: false, // Disable the default skin (since we're using local assets)
    content_css: false, // Disable the default content CSS
    height: 500,
    menubar: true,
    promotion: false,
    plugins: 'lists link image table code wordcount', // Add plugins as needed
    toolbar1: 'undo redo | formatselect | bold italic backcolor link | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat ',
    toolbar2: 'image | table code ',
    image_class_list: [
        { title: 'Responsive', value: 'img-fluid' }
    ],
});
// .. After imports init TinyMCE ..
window.addEventListener('DOMContentLoaded', () => {
});