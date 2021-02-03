<?php
require_once ABSPATH . '/wp-admin/includes/template.php';
class AAC_Category_Checklist extends Walker_Category_Checklist{
    public $change_radiolist = false;
    public $checklist_no_top = false;
    public $required = false;

   function __construct($change_radiolist, $checklist_no_top, $required) {
        $this->change_radiolist = $change_radiolist;
        $this->checklist_no_top = $checklist_no_top;
        $this->required = $required;
   }

    function category_has_children( $term_id = 0, $taxonomy = 'category' ) {
        $children = get_terms( $taxonomy, array( 'child_of' => $term_id  ) );
        return ( $children );
    }

    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        if ( empty( $args['taxonomy'] ) ) {
            $taxonomy = 'category';
        } else {
            $taxonomy = $args['taxonomy'];
        }

        if ( $taxonomy == 'category' ) {
            $name = 'post_category';
        } else {
            $name = 'tax_input[' . $taxonomy . ']';
        }

        $args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];

        $class_names = in_array( $category->term_id, $args['popular_cats'] ) ? 'popular-category' : '';
        $class_names = apply_filters( 'aac_category_wrapper_class', $class_names, $category );

        $class = '';
        if(!empty($class_names)){
          $class = ' class="'.$class_names.'"';
        }

        $args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

        $required = '';
        if($this->required == true){
            $required = 'required';
        }

        if ( ! empty( $args['list_only'] ) ) {

            $aria_cheched = 'false';
            $inner_class = 'category';

            if ( in_array( $category->term_id, $args['selected_cats'] ) ) {
                $inner_class .= ' selected';
                $aria_cheched = 'true';
            }

            /** This filter is documented in wp-includes/category-template.php */
            $output .= "\n" . '<li' . $class . '>' .
                '<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
                ' tabindex="0" role="checkbox" aria-checked="' . $aria_cheched . '">' .
                esc_html( apply_filters( 'the_category', $category->name ) ) . '</div>';
        } else {
            $input_type = 'checkbox';
            if($this->change_radiolist == true){
                $input_type = 'radio';
            }
            $input_class = 'rwmb-'.$input_type;
            $input_class = apply_filters( 'aac_category_input_class', $input_class, $category );

            if($this->checklist_no_top == true){
                if( $category->parent == 0 || $this->category_has_children( $category->term_id, $category->taxonomy ) ) {
                    $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                            '<b>' . esc_html( apply_filters( 'the_category', $category->name ) ) . '</b>';
                }else{
                    /** This filter is documented in wp-includes/category-template.php */
                    $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                        '<label class="selectit"><input value="' . $category->term_id . '" type="'.$input_type.'" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
                        checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
                        disabled( empty( $args['disabled'] ), false, false ) . ' class="'.$input_class.'" '.$required.' /> ' .
                        esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
                }
            }else{
                /** This filter is documented in wp-includes/category-template.php */
                $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                    '<label class="selectit"><input value="' . $category->term_id . '" type="'.$input_type.'" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
                    checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
                    disabled( empty( $args['disabled'] ), false, false ) . ' class="'.$input_class.'" '.$required.'/> ' .
                    esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
            }
        }
    }

}
